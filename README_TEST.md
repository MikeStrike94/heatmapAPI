Build application commands (from application root):

mysql -u root -p
create database myheatmap;

// Create db data
php artisan migrate
php artisan db:seed

// Rollback db data
php artisan migrate:rollback

To test application please use test.http from root folder

**** test.http content ****
POST http://127.0.0.1:8000/api/storeVisit/
Content-Type: application/json

{
    "customer" : "123",
    "url" : "https://www.myStore.ro/category/3",
    "type" : "category"
}

### 

GET http://127.0.0.1:8000/api/countLinkHits/?from=2022-03-26&to=2022-03-27&link=https://www.myStore.ro/category/51
Content-Type: application/json

###

GET http://127.0.0.1:8000/api/countTypeHits/?from=2022-03-26&to=2022-03-27
Content-Type: application/json

###
GET http://127.0.0.1:8000/api/listCustomerJourney/?customer_id=123331&from=2022-03-26&to=2022-03-27
Content-Type: application/json

###
GET http://127.0.0.1:8000/api/listCustomersWithSimilarJourney
Content-Type: application/json

**** end test.http content ****

