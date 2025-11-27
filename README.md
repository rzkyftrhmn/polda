## How To Install

1. Clone the repository
2. Run `composer install`
3. Run `cp .env.example .env`
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `php artisan laravolt:indonesia:seed`
7. Run `php artisan storage:link`