# bookmarks-service
Cервис общедоступных закладок

#### **Инструкция по запуску проекта**

Запускаем контейнеры:

`docker-compose up -d`

Устанавливаем composer пакеты

`docker-compose exec app composer install`

в файле .env указывает ссылку на БД

DATABASE_URL=postgresql://webmaster:webmaster@postgres/bookmarks?serverVersion=11&charset=utf8

Создаем схему БД

`docker-compose exec app doctrine:schema:create`

Загружаем тестовые данные из фикстур

`docker-compose exec app doctrine:fixtures:load`

Доступ администратора

user: admin

password: admin



