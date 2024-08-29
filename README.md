# Supply Depot API

## Зависимости

- [Laravel](https://laravel.com/) v11.x
- [PHP](https://www.php.net/) v8.3.x
- [PostgreSQL](https://www.postgresql.org/) v16.x

## Первичные требования

- [Docker](https://www.docker.com/) >= 22.x

## Порядок установки

### Первый раз
- `git clone https://github.com/shirokovnv/supply_depot_api.git`
- `cd supply_depot_api`
- `docker compose up -d --build`
- `docker compose exec php bash`
- `chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/storage/logs`
- `chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/storage/logs`
- `composer setup`

### В остальных случаях
- `docker compose up -d`

### Глоссарий

Типы документов:

- `income` - приход
- `outcome` - расход
- `inventory` - инвентаризация

### API

**1. _Проведение документа_**

POST http://localhost/api/v1/products/documents

```json
{
    "type": "income",
    "performed_at": "2024-08-25 19:00:00",
    "items": [
        {
            "product_id": 1,
            "product_name": "Some product",
            "value": 1,
            "cost": 20
        },
        {
            "product_id": 2,
            "value": 1,
            "cost": 10
        }
    ]
}
```

**2. _История движения по товарам_**

GET http://localhost/api/v1/products/history

**3. _История движения по конкретному товару_**

GET http://localhost/api/v1/products/{product_ID}/history

**4. _Просмотр результатов инвентаризации за указанную дату_**

GET http://localhost/api/v1/products/inventory?performed_at=2024-08-25

### Структура БД

Таблица `products`

| ID  | name  | created_at | updated_at |
|:----|:------|:-----------|:-----------|
| 1   |Shoes  | 2024-08-29 | 2024-08-29 |

Таблица `documents`

| ID  | type | performed_at        |
|:----|:-----|:--------------------|
| 1   |income| 2024-08-25 10:00:00 | 

Таблица `document_product`

| ID  | document_id | product_id | value  | inv_error | inv_error_cash | remains  | remains_cash | cost |
|:----|:------------|:-----------|:-------|:----------|:---------------|:---------|:-------------|:-----|
| |ID проведенного документа| ID продукта, связанного с документом | Значение "приход", "расход" или "инв."| Ошибка инв. в шт.| Ошибка инв. в рубл. | Остаток в шт. | Остаток в рубл. | стоимость прихода |

Таблица `product_remains`

| ID  | product_id  | remains                     |
|:----|:------------|:----------------------------|
|     | ID продукта | Текущий остаток по продукту | 
