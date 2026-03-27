package handlers

import (
	"sync"
	"time"
)

type Item[T any] struct {
	Value      T
	Expiration int64
}

type Cache[T any] struct {
	mu    sync.RWMutex
	store map[string]Item[T]
}

func NewCache[T any]() *Cache[T] {
	return &Cache[T]{
		store: make(map[string]Item[T]),
	}
}

func (c *Cache[T]) Set(key string, value T, ttl time.Duration) {
	c.mu.Lock()
	defer c.mu.Unlock()

	c.store[key] = Item[T]{
		Value:      value,
		Expiration: time.Now().Add(ttl).UnixNano(),
	}
}

func (c *Cache[T]) Get(key string) (T, bool) {
	c.mu.RLock()
	defer c.mu.RUnlock()

	item, found := c.store[key]

	if !found || time.Now().UnixNano() > item.Expiration {
		var zero T

		return zero, false
	}

	return item.Value, true
}
