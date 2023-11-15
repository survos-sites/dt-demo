bin/console importmap:install && bin/console asset-map:compile
bin/console doctrine:migrations:migrate -n --allow-no-migration
