# TODO APP (In progress)

Для разработки server используется Docker version 24.0.2

# Client (In progress)

# Backend
## Базовые настройки
#### 1. Билд образов
```bash
docker compose build
```
#### 2. Установка зависимостей
```bash
docker compose run --rm --no-deps php-fpm composer install
```
#### 3. Запуск контейнеров в тихом режиме
```bash
docker compose up -d --force-recreate
```
#### 4. Запуск контейнера с php-fpm
```bash
docker compose exec php-fpm bash
```
#### Все последующие команды выполняются внутри контейнера php-fpm
#### 5. Создание бд
```bash
bin/console doctrine:database:create
```
#### 6. Установка миграций
```bash
bin/console doctrine:migrations:migrate
```
#### 7. Установка фикстур
```bash
bin/console doctrine:fixtures:load
```
