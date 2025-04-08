

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

Дадим разрешение записывать в папку runtime кеш, логи и т.д.
```rb
chmod 777 runtime
```

Создаём env-файл.
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

В контейнере запускаем миграции
```rb
./yii migrate
```