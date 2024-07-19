# How To Start

Brief description or introduction of the project.

Setup Environment
COPY env.example to .env and set up your .env file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=your_database_password


1. Install Composer Dependencies
composer install

2. Generate autoload files
composer dump-autoload

3. Run Database Migrations
php artisan migrate

4. Run DB SEED
php artisan db:seed


RUN 
php artisan serve


