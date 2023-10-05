# ##Future Framwork

## Introduction
Framework ini dibuat dengan beberapa library php handal dan stabil,  yang juga banyak digunakan untuk membangun beberapa stack framework yang popular diantaranya laravel , symfony, lumen.

## Requirement 
- min php 8.1
- postgresql
- docker
- php library : symfony/http-foundation, symfony/http-kernel, symfony/routing, symfony/event-dispatcher, firebase/php-jwt, symfony/dotenv, illuminate/database,

## Development Enviroment 
- docker 

## How to run

```
$ git clone https://github.com/achmadi/6108d7a9038486c93c2d160d9e859dad.git future-framework && cd future-framework && composer install &&  docker-compose up 
```
## Demo Credentials

**User 1:** admin@gmail.com  
**Password:** password

**User:** client_1@gmail.com  
**Password:** password

## Testing 

Kami telah menyediakan  [Postman collection](https://github.com/achmadi/6108d7a9038486c93c2d160d9e859dad/blob/master/future-framework.postman_collection.json) pada repository ini yang siap anda import dan run.

## Sending Message Flows 

1. Pendaftaran user 
2. User memperoleh akun dan api_key
3. User generate token
4. User get email member account
5. User sending message with email recipient + token 
6. system  menyimpannya ke dalam db + mendaftarkan antrian notifikasi ke penerima kedalam message broker 
7. message broker mengirim notifikasi ke pengguna yang online 
8. pengguna membuka notifikasi dan membuat isi pesan dan menandai pesan telah dibuka. 
9. Selesai ...

## Demo Endpoints 