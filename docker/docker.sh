chmod -R 777 templates
php bin/console doctrine:database:create
php bin/conssole make:migration
php bin/console doctrine:migrations:migrate
php bin/console app:user:create Kévin RIFA hello@creative-eye.fr admin
php bin/console cache:clear