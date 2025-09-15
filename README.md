# Laravel Payment App with Docker

This is a Laravel project for managing payments, set up to run using **Docker**. The project includes:

- Laravel 8/9/10 backend
- MySQL 8 database
- phpMyAdmin for database management
- Apache with PHP 8.3

---

## **Prerequisites**

Make sure you have installed:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## **Clone the Repository**

```bash
git clone https://github.com/Sandalanka/Payment-Notification-Payout-System-frontend.git 
cd Payment-Notification-Payout-System-frontend

Setup and Run with Docker
1. Build and start containers
   docker compose up --build -d

This will build the Docker images and start the following containers:

payment_app → Laravel app (port 8000)

mysql_db → MySQL database (port 3306)

phpmyadmin → phpMyAdmin (port 9090)

Set up Laravel environment
 docker exec -it payment_app cp .env.example .env

Update .env database credentials:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=payment
DB_USERNAME=laravel
DB_PASSWORD=secret

Install Composer dependencies
 docker exec -it payment_app composer install

Generate Laravel app key
 docker exec -it payment_app php artisan migrate

Run database migrations 
  docker exec -it payment_app php artisan migrate

Access the application

Laravel app: http://localhost:8000

phpMyAdmin: http://localhost:9090   Host: mysql  User: root Password: root

Stop Docker containers
  docker compose down


Rebuild Containers

If you update Dockerfile or dependencies:
   docker compose up --build -d
