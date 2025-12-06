## Добавление роли
- В App\Services\Roles создать новый обработчик роли, унаследовать его от \App\Services\Roles\UserRole
- Переопределить методы из родительского класса, добавить свою логику доступности пермишнов в данных методах
- Зарегистрировать роль в \App\Enums\Roles\Entities
- Добавить нужным пользователям ID роли, зарегистрированный в \App\Enums\Roles\Entities

## Добавление нового permission для роли
- Зарегистрировать новый Permission в \App\Enums\Roles\Permissions
- Добавить метод соответствующего Permission в \App\Services\Roles\UserRole для определения логики доступности/недоступности для определенной роли
- Добавить отношение {ID permission} => {метод обработки доступности} в маппинг метода hasPermission класса \App\Services\Roles\UserRole
- Указывать для запросов новый permission так: Route::controller(UserController::class)->middleware('permission.has:' . Permissions::{Элемент из \App\Enums\Roles\Permissions}->value)->... в api.php

## Установка и запуск проекта

### Laravel
- Требования: PHP 8.2, Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer и XML

- git clone https://github.com/kolyaudav/OnlineGasStation.git
- cd OnlineGasStation
- composer install
- cp .env.example .env
- в .env добавить DB_USERNAME и DB_PASSWORD
- php artisan key:generate
- php artisan migrate --seed
- php artisan serve (при необходимости, если не настроен Nginx, Apache и т.п.) - http://127.0.0.1:8000

#### Go

Для корректной работы всего приложения необходимо также запустить дополнительные сервисы на Go.
Если Go еще не установлен в вашей системе, вы можете скачать его с [официального сайта](go.dev).

 - Перейдите в директорию Go-сервисов `go_services`
 - cd go_services/promotions_service
 - go mod tidy
 - go build -o promotions_service

    