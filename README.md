Let Me Sport Admin
================

- Чтобы запустить надо проделать инструкцию ниже и выполнить init
- Фротнэнд билдиться или в папку /prod или в папку /web (На сервере настроено все в папку /prod)
- Чтобы запросы обрабатывались из роуту myserver.com/api надо подвинуть туда файл index.php, те из /web в /web/api
- Пишешь доступ в свою бд в конфиге в /app/common-dev.php или common-prod.php
- Накатываешь дамп или запускаешь миграции php yii.php migrate
- Запускаешь api:
```
php yii.php serve --docroot="prod" или php yii.php serve --docroot="web"
```

С фронтэндом есть большая проблема на маке - из проблем с зависимостями не работает сборка, на винде все в пордке.  Чтобы запустить фронтжнд на маке нужно закоментить все импорты
в app.module.ts, запустить npm start, дождаться ошибки сборки, не закрывая консоль с вотчером
раскоментить обратно имортпы и все начнет работать после пересборки, инициированной вотчером.

API Documentation
-----------------

* [Docs](docs/README.md)

REQUIREMENTS
------------
* Node.js = 6.11.3 (не выше иначе сборка выдаёт ошибки)
* PHP >= 7.0
* PHP GD Extension

INSTALLATION
------------

```
php composer.phar global require "fxp/composer-asset-plugin:~1.1.1"
php composer.phar install
php init
```

The next steps:
```
php yii migrate
```

Than you may to create admin or user with console commands:
```
php yii user/create ADMIN-NAME ADMIN-EMAIL [ADMIN-PASSWORD]
```

UPGRADE
-------

```
php composer.phar --prefer-dist install
php yii migrate
php yii cache/flush-all
```

Console Commands
----------------

Apply all new migrations:
```
php yii migrate
```

Clear cache:
```
php yii cache/flush-all
```

Create admin:
```
php yii user/create ADMIN-NAME ADMIN-EMAIL [ADMIN-PASSWORD]
```

Yii console help:
```
php yii help
```
