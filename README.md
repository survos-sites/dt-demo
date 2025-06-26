## Complete datatables-demo project, Symfony 7.0

### Requirements

* PHP 8.4 with Sqlite
* composer
* MeiliSearch
* Symfony CLI

With docker, installing meilisearch is easy.  

```bash
sudo docker run --rm --name meili -d -p 7700:7700 -v $(pwd)/../meili_data:/meili_data getmeili/meilisearch:v1.5 meilisearch
```

## Install the application

```bash
git clone git@github.com:survos-sites/dt-demo && cd dt-demo
composer install
symfony check:req
bin/console d:sch:update --force --complete
bin/console cache:pool:clear cache.app
bin/console app:load --limit 50 
bin/console meili:index App\\Entity\\Official
bin/console workflow:iterate App\\Entity\\Official --marking=new --transition=load
bin/console mess:consume async  

bin/console meili:index App\\Entity\\Official
symfony server:start -d
symfony open:local --path /congress/simple_datatables
```

## With Survos Bundle Development
```bash
git clone git@github.com:survos/survos ../survos
cd ../survos && composer install && cd ../dt-demo
../survos/link . 
```

@todo: meili-bundle component
