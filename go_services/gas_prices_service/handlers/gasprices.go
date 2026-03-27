package handlers

import (
	"encoding/json"
	"fmt"
	"net/http"
	"slices"
	"time"
)

var apiCache = NewCache[float64]()

var fuelDictionary = map[string]string{
	"pba":     "fcd6579c-f36b-1410-8375-00d07e0cc298",
	"dtMin32": "bcba81ae-f46b-1410-8375-00d07e0cc298",
	"dt":      "371b479c-f36b-1410-8375-00d07e0cc298",
	"ai-98":   "4598469c-f36b-1410-8375-00d07e0cc298",
	"ai-95":   "b690469c-f36b-1410-8375-00d07e0cc298",
	"ai-92":   "f788469c-f36b-1410-8375-00d07e0cc298",
	"dt-eco":  "21cb559c-f36b-1410-8375-00d07e0cc298",
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

	for _, fuelPrice := range fuelPrices {
		apiCache.Set(fuelPrice.ID, fuelPrice.Price, time.Hour*24)
	}

	targetPriceIdx := slices.IndexFunc(fuelPrices, func(f FuelPrice) bool {
		return f.ID == fuelID
	})

	if targetPriceIdx == -1 {
		return 0, fmt.Errorf("данные не найдены по индексу, индекс = -1")
	}

	fuelPrice := fuelPrices[targetPriceIdx]
	price := fuelPrice.Price

	return price, nil
}

func IsCodeActual(code string) bool {
	_, ok := fuelDictionary[code]

	return ok
}
