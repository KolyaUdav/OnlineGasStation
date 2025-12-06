package promotions

import (
	"context"
	"database/sql"
	"time"
)

type UserInfo struct {
	CreatedAt time.Time
	Balance   float64
}

type UserCache struct {
	data map[int]UserInfo
}

func NewUserCache() *UserCache {
	return &UserCache{data: make(map[int]UserInfo)}
}

func (c *UserCache) GetUserInfo(ctx context.Context, userID int, db *sql.DB) (UserInfo, error) {
	if info, ok := c.data[userID]; ok {
		return info, nil
	}

	createdAt, err := GetUserCreatedAt(ctx, userID, db)

	if err != nil {
		return UserInfo{}, err
	}

	balance, err := GetUserBalance(ctx, userID, db)

	if err != nil {
		return UserInfo{}, err
	}

	info := UserInfo{
		CreatedAt: createdAt,
		Balance:   balance,
	}

	c.data[userID] = info

	return info, nil
}
