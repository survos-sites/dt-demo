## Complete datatables-demo project, Symfony 7.0

### Requirements

* PHP 8.2 with Sqlite
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
bin/console d:sch:update --force --complete
bin/console cache:pool:clear cache.app
bin/console app:load-data --limit 50 --details -vv
symfony server:start -d
symfony open:local --path /congress/grid
```

## With Survos Bundle Development
```bash
git clone git@github.com:survos/survos ../survos
cd ../survos && composer install && cd ../dt-demo
../survos/link . 
```

Now changes made in files such as ../survos/packages/api-grid-bundle/assets/src/datatables-plugins.js are loaded when you refresh the page..

uncomment the searchbuilder lines in datatable-plugins.


### Recreate the demo

To recreate the bulk of the demo, see (doc/create-demo.md).

## Developer Notes

The recommended way to work on a Survos bundle is to attach the bundle source, located in survos/survos/packages, to the dt-demo application in PHPStorm.  That gives easy access to the stimulus controllers.

The key stimulus controllers for datatables are located in survos/packages/api-grid-bundle/assets/src/controllers:

* api_grid_controller.js
* grid_controller.js

the 'grid' component/controller gets its data from HTML, the 'api-grid' components get its data from API Platform.  Within API Platform, there are 2 providers, doctrine and meili.  Doctrine uses the database (usually postgres or sqlite), and meili uses the meili search server, which itself is populated usually (but not always) from the database via doctrine, by serializing the data.

See the example usage in the [api-grid-bundle] README.

