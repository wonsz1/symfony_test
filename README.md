Simple blog app in Symfony

Run:

```bash
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php bin/console d:m:m


Testing
https://symfony.com/doc/current/testing.html#configuring-a-database-for-tests
add DATABASE_URL in .env.test for test database
run commands:
bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
docker-compose exec php bin/console doctrine:migrations:migrate --env=test

docker-compose exec php bin/phpunit
docker-compose exec php ./vendor/bin/behat