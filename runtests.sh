#!/bin/sh

# migrate
echo "Migrating..."
php artisan migrate:refresh --database=mysql_testing --seed -q
echo "Migrations finished."

# run tests
vendor/bin/phpunit

# reset 
echo "Resetting testing database..."
php artisan migrate:reset --database=mysql_testing -q
echo "Done"
