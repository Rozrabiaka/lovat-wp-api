# Lovat_Api

## Description
WordPress WooCommerce REST API Lovat Api

## Установка плагина

Загрузите расширение в виде ZIP-файла из этого репозитория по директории wp-content/plugins

Далее активируйте плагин и получите ключь доступа в настройках.

Авторизацию запросов используйте 

```
Authorization: Bearer Token
```
Параметры `from` `to` `p`

`from` `to` -> формат даты

`p` -> integer (пагинация), по дефолту = 1

URL запроса 
```
/wp-json/v1/orders?from=date&to=date&p=integer
```
 
Пример URL запроса 

```
http://localhost/wp-json/v1/orders?from=15.08.2020&to=30.08.2020&p=1
```

Поиск происходит по таким статусам как `completed` и `refunded`