Simple blog app in Symfony

Run:

```bash
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php bin/console d:m:m


Testing
https://symfony.com/doc/current/testing.html#configuring-a-database-for-tests
add DATABASE_URL in .env.test for test database

Testing:
bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
docker-compose exec php bin/console doctrine:migrations:migrate --env=test
docker-compose exec php bin/phpunit
docker-compose exec php ./vendor/bin/behat

Migrations:
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate

GraphQL:
http://localhost:8080/api/graphql

OpenAPI:
http://localhost:8080/api/docs

API Platform Admin:
docker-compose exec -it node10 bash 
npm run dev
http://localhost:5173

PHPStan:
docker-compose exec php vendor/bin/phpstan analyse 

Added phpstan-doctrine to fix issue - Auto-Generated ID is never assigned int
https://github.com/phpstan/phpstan-doctrine/issues/610

Grafana: http://localhost:3000 (login with admin/admin initially)
Prometheus: http://localhost:9090
Query: app_api_calls_total