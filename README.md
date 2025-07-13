# Хотел упаковать в docker, но по техническим причинам не смог! И кэша нет.

## Требования

- PHP 8.2+
- SQLite3

## Установка
Клонируем репозиторий:
```
git clone https://github.com/eyekeen/kripto-app

cd kripto-app
```

Установка зависимостей:
```
composer install
```

## Запуск миграций
```
php migrate.php
```

база должна быть создана в папке storage/database

## Обновление данных
При загрузке страницы приложение берет данные из api.

Для обновления данных о криптовалютах выполните:
```
php update_crypto.php
```

## Настройка cron для автоматического обновления(если нужно)
1. Откройте crontab:
```
crontab -e
```

2. Добавьте строку для обновления каждые 5 минут:
```
*/5 * * * * cd /полный/путь/к/kripto-app && php update_crypto.php >> /полный/путь/к/kripto-app/storage/logs/cron.log 2>&1
```

## Запуск веб-сервера
```
php -S localhost:8000 -t public
```

Откройте в браузере: http://localhost:8000
