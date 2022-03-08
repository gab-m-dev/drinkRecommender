# drinkRecommender

PHP Slim App in directory /api-drinks:
--------------------------------------
To install dependencies, run:

composer update


Angular App in direcory /drinkRecommender
-----------------------------------------
To create build

ng build


Change URLs and credentials to use App localy:
-------------------

Change URLs in:
- drinkRecommender/src/app/get-drink.service.ts
- drinkRecommender/src/app/get-usage.service.ts
- drinkRecommender/src/app/auth.service.ts

Change DB Name in /api-drinks/src/Models

Change settings in /api-drinks/src/local-example.php and rename to local.php (Password Hash algorithm -> BCRYPT)

Data for DB under /db:
-------------------
MariaDB is needed
