package handlers

import (
	"encoding/json"
	"fmt"
	"net/http"
	"time"
)

var apiCache = NewCache[float64]()

var fuelDictionary = map[string]string{
	"pba":    "ПБА",
	"dt":     "ДТ",
	"ai-98":  "АИ-98",
	"ai-95":  "АИ-95",
	"ai-92":  "АИ-92",
	"dt-eco": "ДТ ECO",
}

func GasPricesHandler(w http.ResponseWriter, r *http.Request) {
	fuelCode := r.URL.Query().Get("fuel_code")

	if fuelCode == "" {
		w.WriteHeader(http.StatusBadRequest)
		json.NewEncoder(w).Encode(map[string]string{"error": "не был передан код топлива"})
		return
	}

	if !IsCodeActual(fuelCode) {
		w.WriteHeader(http.StatusUnprocessableEntity)
		json.NewEncoder(w).Encode(map[string]string{"error": "неизвестный код топлива"})
		return
	}

	var fuelID string

	fuelID = fuelDictionary[fuelCode]
	price, found := apiCache.Get(fuelID)

	if !found {
		var err error

		price, err = CreateCacheData(fuelID)

		if err != nil {
			w.WriteHeader(http.StatusInternalServerError)
			json.NewEncoder(w).Encode(map[string]string{"error": err.Error()})
			return
		}
	}

	response := map[string]any{
		"fuelCode": fuelCode,
		"price":    price,
	}

	w.Header().Set("Content-Type", "text/plain")
	json.NewEncoder(w).Encode(response)
}

func CreateCacheData(fuelID string) (float64, error) {
	fuelPrices, err := ApiHandle()

	if err != nil {
		return 0, fmt.Errorf("ошибка обращения к API")
	}

	found := false

	var targetPrice float64

	for _, fuelPrice := range fuelPrices {
		apiCache.Set(fuelPrice.Fuel.ID, fuelPrice.Price, time.Hour*24)

		if fuelPrice.Fuel.ID == fuelID {
			targetPrice = fuelPrice.Price
			found = true
		}
	}

	if !found {
		return 0, fmt.Errorf("данные не найдены для: %s", fuelID)
	}

	return targetPrice, nil
}

func IsCodeActual(code string) bool {
	_, ok := fuelDictionary[code]

	return ok
}
