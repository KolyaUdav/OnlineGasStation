package handlers

import (
	"context"
	"encoding/json"
	"net/http"
	"time"

	"promotions_service/db"
	"promotions_service/promotions"
	"promotions_service/requests"
)

func PromotionCheckHandler(w http.ResponseWriter, r *http.Request) {
	ctx, cancel := context.WithTimeout(r.Context(), 2*time.Second)
	defer cancel()

	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	parsedParams, err := requests.ParseGetParams(r)

	if err != nil {
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, err.Error(), 500)
		return
	}

	d := db.GetDBConn()
	defer d.Close()

	promos, err := promotions.GetActualPromotions(ctx, d)

	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	mConds := map[int]bool{}

	cache := promotions.NewUserCache()

	for _, promo := range promos {
		if promo.Conditions == nil {
			continue
		}

		condition, err := promotions.ParseConditions(promo.ID, *promo.Conditions)

		if err != nil {
			http.Error(w, err.Error(), 500)
			return
		}

		isMatch, err := promotions.IsMatchCondition(ctx, d, condition, parsedParams, cache)

		if err != nil {
			http.Error(w, err.Error(), 500)
			return
		}

		mConds[promo.ID] = isMatch

	}

	maxSale := promotions.GetMaxSalePercent(promos, mConds)

	response := map[string]int{
		"max_sale": maxSale,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(response)
}
