package handlers

import (
	"encoding/json"
	"fmt"
	"net/http"
	"os"
	"time"
)

type FuelPrice struct {
	ID    string  `json:"id"`
	Price float64 `json:"price"`
}

func ApiHandle() ([]FuelPrice, error) {
	res, err := SendRequest()

	if err != nil {
		return nil, fmt.Errorf("ошибка отправки API-запроса: %w", err)
	}

	defer res.Body.Close()

	apiResponse, err := DecodeResponse(res)

	if err != nil {
		return nil, fmt.Errorf("ошибка декодирования ответа API: %w", err)
	}

	return apiResponse, nil
}

func SendRequest() (*http.Response, error) {
	url := os.Getenv("API_URL")

	req, err := http.NewRequest("GET", url, nil)

	if err != nil {
		return nil, fmt.Errorf("ошибка при формировании запроса %w", err)
	}

	req.Header.Add("Content-Type", "application/json")

	client := http.Client{
		Timeout: time.Second * 10,
	}

	res, err := client.Do(req)

	if err != nil {
		return nil, fmt.Errorf("ошибка соединения: %w", err)
	}

	if res.StatusCode < 200 || res.StatusCode > 300 {
		res.Body.Close()

		return res, fmt.Errorf("ошибка API: %d", res.StatusCode)
	}

	return res, nil
}

func DecodeResponse(res *http.Response) ([]FuelPrice, error) {
	var fuelPrices []FuelPrice

	err := json.NewDecoder(res.Body).Decode(&fuelPrices)

	if err != nil {
		return nil, fmt.Errorf("ошибка декодирования json из API-ответа: %w", err)
	}

	return fuelPrices, nil
}
