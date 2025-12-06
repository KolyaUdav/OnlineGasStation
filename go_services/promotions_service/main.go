package main

import (
	"net/http"

	"github.com/joho/godotenv"

	"promotions_service/handlers"
)

func init() {
	godotenv.Load()
}

func main() {
	http.HandleFunc("/api/check-promotions", handlers.PromotionCheckHandler)
	http.ListenAndServe(":8080", nil)
}
