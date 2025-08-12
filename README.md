<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


## About Pullus-Test-Backend (API Service)

Pullus: this was an assessment assigned to me, in order to implement a todo app service, where the client can create, update, view and delete todo items.
## Installtion

### Prequisite

Install Docker (Mac OS or Windows), 
PHP 8.2, 
Laravel 12 
& Composer
Ensure Docker is running

### Clone Repository
Clone repo using the https link.
```
git clone https://github.com/khollinzx/pullus-test.git 
```
OR SSH link
```
git clone git@github.com:khollinzx/pullus-test.git 
```

### Set Up

```
Run cd pullus-test
cp .env.example .env
Run "composer install"
Edit docker.compose.yml  replace "${APP_PORT:-80}:80" with "8091:80"
Run "alias sail='bash vendor/bin/sail'"
Run "sail up"
If there is an error with mysql starting, 
change FORWARD_DB_PORT=3306 to 3307 and run "sail down"
then Run "sail up"
Run "sail artisan migrate:fresh"
Run "sail artisan passport:key"
Run "sail artisan passport:client --personal"
```

### Postman

```
Exported Postman Json on google drive

https://drive.google.com/file/d/1b4usIg-MJ9-uuT2m8JsnEQ49lu9MPcS6/view?usp=drive_link
```
