

Клонируем код из репозитория
```rb
git clone git@github.com:LineyKin/intro.git
```

Переходим в корневую папку приложения
```rb
cd intro/
```

В корень проекта помещаем файл test_db_data.sql и test_db_structure.sql

Создаём env-файл. и копируем в него содержимое .env.example
```rb
touch .env
```
Запускаем приложение
```rb
docker compose up -d
```

Заходим в контейнер приложения
```rb
docker exec -ti intro_app bash
```

В контейнере запускаем composer
```rb
composer install
```

В контейнере запускаем миграции
```rb
./yii migrate
```

Проект доступен по адресу
```rb
http://localhost:8000/orders/
```