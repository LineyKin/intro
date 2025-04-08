

Клонируем код из репозитория
```rb
git clone git@github.com:LineyKin/intro.git
```

Переходим в корневую папку приложения
```rb
cd intro/
```

Собираем папку vendor на основе имеющегося файла composer.json
```rb
composer install
```

Создаём env-файл.
```rb
touch .env
```

В корень проект помещаем файл test_db_data.sql и test_db_structure.sql

Запускаем приложение
```rb
docker compose up -d
```
Заходим в контейнер приложения
```rb
docker exec -ti intro_app bash
```

В контейнере запускаем миграции
```rb
./yii migrate
```