# Этап 1: Сборка
FROM golang:1.26-alpine AS builder

# Устанавливаем git и необходимые сертификаты
RUN apk add --no-cache git ca-certificates

WORKDIR /app

# Копируем файлы зависимостей
COPY go.mod go.sum ./

ENV GO111MODULE=on
ENV GOPROXY=direct
RUN go mod download

# Копируем весь исходный код сервиса
COPY . .

# Собираем бинарник
RUN CGO_ENABLED=0 GOOS=linux go build -o main ./main.go

# Этап 2: Запуск
FROM alpine:latest

WORKDIR /root/
# Копируем только скомпилированный файл из первого этапа
COPY --from=builder /app/main .
# Копируем .env файл, если он нужен сервису для работы
COPY --from=builder /app/.env.example ./.env 

CMD ["./main"]
