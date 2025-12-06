package promotions

import "encoding/json"

func ParseConditions(id int, cond json.RawMessage) (Condition, error) {
	var c Condition
	c.ID = id

	if len(cond) == 0 || string(cond) == "null" {
		c.ParsedCond = map[string]any{}

		return c, nil
	}

	var data map[string]any

	err := json.Unmarshal(cond, &data)

	if err != nil {
		return Condition{}, err
	}

	c.ParsedCond = data

	return c, nil
}
