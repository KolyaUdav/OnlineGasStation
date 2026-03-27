package main

import (
	"gas_prices_service/handlers"
	"net/http"

	"github.com/joho/godotenv"
)

func init() {
	godotenv.Load()
}

func main() {
	http.HandleFunc("/api/get-gas-prices", handlers.GasPricesHandler)
	http.ListenAndServe(":8080", nil)
}
