package requests

import (
	"fmt"
	"net/http"
	"strconv"
	"time"
)

type GetParams struct {
	FuelType  string
	Sum       float64
	UserID    int
	Quantity  int
	CreatedAt time.Time
}

func ParseGetParams(r *http.Request) (GetParams, error) {
	query := r.URL.Query()

	var parsedParams GetParams

	for key, val := range query {
		switch key {
		case "user_id":
			uid, err := ToInt(val[0])

			if err != nil {
				return GetParams{}, err
			}

			parsedParams.UserID = uid

		case "sum":
			sum, err := ToFloat(val[0])

			if err != nil {
				return GetParams{}, err
			}

			parsedParams.Sum = sum

		case "quantity":
			quantity, err := ToInt(val[0])

			if err != nil {
				return GetParams{}, err
			}

			parsedParams.Quantity = quantity

		case "created_at":
			datetime, err := ToDateTime(val[0])

			if err != nil {
				return GetParams{}, err
			}

			parsedParams.CreatedAt = datetime

		case "fuel_type":
			parsedParams.FuelType = val[0]
		}
	}

	return parsedParams, nil
}

func ToInt(s string) (int, error) {
	n, err := strconv.Atoi(s)

	if err != nil {
		return 0, fmt.Errorf("error converting type: %s", err)
	}

	return n, nil
}

func ToFloat(s string) (float64, error) {
	f, err := strconv.ParseFloat(s, 64)

	if err != nil {
		return 0.0, fmt.Errorf("error converting type: %s", err)
	}

	return f, nil
}

func ToDateTime(s string) (time.Time, error) {
	const layout = "2006-01-02 15:04:05"
	d, err := time.Parse(layout, s)

	if err != nil {
		return time.Time{}, fmt.Errorf("error converting type: %w", err)
	}

	return d, nil
}
