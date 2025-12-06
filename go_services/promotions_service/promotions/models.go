package promotions

import "encoding/json"

type Promotion struct {
	ID          int
	SalePercent int
	Conditions  *json.RawMessage
}

type Condition struct {
	ID         int
	ParsedCond map[string]any
}
