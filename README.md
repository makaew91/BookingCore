## Запуск проекта (Docker + PostgreSQL)

### Требования
- Docker и Docker Compose v2

### Быстрый старт
1. Скопируйте окружение для Docker:
   ```bash
   cp .env.docker.example .env
   ```
2. Соберите и поднимите контейнеры:
   ```bash
   docker compose up -d --build
   ```
3. Примените миграции внутри контейнера:
   ```bash
   docker compose exec app php artisan migrate --force
   ```
4. Приложение доступно на `http://localhost:8000`.

Замечание: в `docker-compose.yml` настроена БД PostgreSQL (db), а `Dockerfile` собирает PHP 8.3 с расширением `pdo_pgsql`.

## Модуль бронирований охотничьих туров

### Миграции и модели
- `guides` (`App\Models\Guide`): `name`, `experience_years`, `is_active`
- `hunting_bookings` (`App\Models\HuntingBooking`): `tour_name`, `hunter_name`, `guide_id`, `date`, `participants_count`
- Ограничения: `unique (guide_id, date)` — один гид не может иметь два бронирования на одну дату

### Бизнес-правила
- У гида не должно быть другого бронирования на ту же дату
- `participants_count <= 10`

### API
- GET `/api/guides` — список активных гидов
  - Параметры: `min_experience` (необязательно) — фильтр по минимальному опыту
  - Ответ: массив ресурсів `GuideResource` (`id`, `name`, `experience_years`)

- POST `/api/bookings` — создание нового бронирования
  - Тело запроса: `tour_name`, `hunter_name`, `guide_id`, `date(YYYY-MM-DD)`, `participants_count`
  - Валидация: `StoreHuntingBookingRequest`
  - Ответы:
    - 201 — успешно создано (`HuntingBookingResource`)
    - 404 — гид не найден или не активен
    - 422 — ошибки валидации (в т.ч. занят гид на дату, лимит участников)

### Примеры запросов
Список гидов (минимум 3 года опыта):
```bash
curl "http://localhost:8000/api/guides?min_experience=3"
```

Создание бронирования:
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "tour_name": "Duck Hunt",
    "hunter_name": "John",
    "guide_id": 1,
    "date": "2025-10-20",
    "participants_count": 3
  }'
```

### Тесты
Запуск тестов в контейнере:
```bash
docker compose exec app php artisan test --colors=always
```

## Коротко об интеграции в ядро BookingCore
- Вынести модуль в отдельный пакет/модуль (например, `modules/Hunting`), зарегистрировать `ServiceProvider`
- Подключить роуты модуля в общий API-роутер BookingCore (единый префикс и middleware)
- Применить политики/права доступа при необходимости (например, только авторизованные охотники создают бронирования)
- Настроить миграции модуля через авто-загрузку в провайдере (publish/auto-discovery)
- Обсудить кросс-модульные зависимости (например, пользователи/платежи) и использовать события/слушателей для слабой связности
