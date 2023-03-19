composer install
chmod -R 777 /var/log/dev.log
php bin/console cache:clear
php bin/console doctrine:database:create
php bin/console d:m:m --no-interaction
php bin/console app:create-user hello@creative-eye.fr admin
php bin/console cache:clear