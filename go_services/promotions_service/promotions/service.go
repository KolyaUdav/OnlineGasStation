package promotions

import (
	"context"
	"database/sql"
	"fmt"
	"promotions_service/requests"
	"slices"
	"time"
)

func GetActualPromotions(ctx context.Context, db *sql.DB) ([]Promotion, error) {
	query := `SELECT id, sale_percent, conditions
	          FROM promotions 
	          WHERE date_start <= ? AND date_end > ?`

	now := time.Now()

	rows, err := db.QueryContext(ctx, query, now, now)

	if err != nil {
		return nil, fmt.Errorf("query failed: %w", err)
	}

	defer rows.Close()

	var promotions []Promotion

	for rows.Next() {
		var p Promotion

		if err := rows.Scan(&p.ID, &p.SalePercent, &p.Conditions); err != nil {
			return nil, fmt.Errorf("scan failed: %w", err)
		}

		promotions = append(promotions, p)
	}

	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("rows iteration failed: %w", err)
	}

	return promotions, nil
}

func GetUserCreatedAt(ctx context.Context, userID int, db *sql.DB) (time.Time, error) {
	var createdAt string

	query := `SELECT created_at FROM users WHERE id = ? LIMIT 1`

	err := db.QueryRowContext(ctx, query, userID).Scan(&createdAt)

	if err != nil {
		return time.Time{}, fmt.Errorf("query failed: %w", err)
	}

	layout := "2006-01-02T15:04:05Z"
	regTime, err := time.Parse(layout, createdAt)

	if err != nil {
		return time.Time{}, fmt.Errorf("query failed: %w", err)
	}

	return regTime, nil
}

func GetUserBalance(ctx context.Context, userID int, db *sql.DB) (float64, error) {
	var amount float64

	query := `SELECT amount FROM balances WHERE user_id = ? LIMIT 1`

	err := db.QueryRowContext(ctx, query, userID).Scan(&amount)

	if err != nil {
		return 0.0, fmt.Errorf("query failed: %w", err)
	}

	return amount, nil
}

func GetMaxSalePercent(promos []Promotion, mConds map[int]bool) int {
	maxRuleSale := 0
	maxGeneralSale := 0

	for _, p := range promos {
		if ok := mConds[p.ID]; ok {
			if p.SalePercent > maxRuleSale {
				maxRuleSale = p.SalePercent
			}
		} else {
			if p.SalePercent > maxGeneralSale {
				maxGeneralSale = p.SalePercent
			}
		}
	}

	if maxRuleSale > 0 && maxGeneralSale < maxRuleSale {
		return maxRuleSale
	}

	return maxGeneralSale
}

func IsMatchCondition(ctx context.Context, db *sql.DB, c Condition, params requests.GetParams, cache *UserCache) (bool, error) {
	userInfo, err := cache.GetUserInfo(ctx, params.UserID, db)

	if err != nil {
		return false, err
	}

	if raw, ok := c.ParsedCond["min_reg_date"]; ok {
		rawStr, ok := raw.(string)

		if !ok {
			return false, fmt.Errorf("min_reg_date должен быть строкой")
		}

		minRegDate, err := time.Parse("2006-01-02 15:04:05", rawStr)

		if err != nil {
			return false, fmt.Errorf("неверный формат min_reg_date: %w", err)
		}

		if !userInfo.CreatedAt.After(minRegDate) {
			return false, nil
		}
	}

	if raw, ok := c.ParsedCond["min_balance"]; ok {
		minBal, ok := raw.(float64)

		if !ok {
			return false, fmt.Errorf("min_balance должен быть числом")
		}

		if userInfo.Balance < minBal {
			return false, nil
		}
	}

	if raw, ok := c.ParsedCond["min_order_sum"]; ok {
		minSum, ok := raw.(float64)

		if !ok {
			return false, fmt.Errorf("min_order_sum должен быть числом")
		}

		if params.Sum < minSum {
			return false, nil
		}
	}

	if raw, ok := c.ParsedCond["fuel_types"]; ok {
		cFuelTypes, err := ConvertInterfaceSliceToStringSlice(raw)

		if err != nil {
			return false, err
		}

		if !slices.Contains(cFuelTypes, params.FuelType) {
			return false, nil
		}
	}

	if raw, ok := c.ParsedCond["quantity"]; ok {
		var requiredQuantity int

		switch v := raw.(type) {
		case float64:
			requiredQuantity = int(v)
		case int:
			requiredQuantity = v
		default:
			return false, fmt.Errorf("quantity должен быть числом")
		}

		if params.Quantity < requiredQuantity {
			return false, nil
		}
	}

	return true, nil
}

func ConvertInterfaceSliceToStringSlice(data interface{}) ([]string, error) {
	if interfaceSlice, ok := data.([]interface{}); ok {
		stringSlice := make([]string, len(interfaceSlice))

		for i, v := range interfaceSlice {
			if str, ok := v.(string); ok {
				stringSlice[i] = str
			} else {
				return nil, fmt.Errorf("элемент индекса %d не является строкой", i)
			}
		}

		return stringSlice, nil
	}

	if stringSlice, ok := data.([]string); ok {
		return stringSlice, nil
	}

	return nil, fmt.Errorf("не удалось привести тип к []interface{} или []string. Фактический тип: %T", data)
}
