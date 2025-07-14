## Complete datatables-demo project, Symfony 7.0

### Requirements

* PHP 8.4 with Sqlite
* composer
* MeiliSearch
* Symfony CLI

With docker, installing meilisearch is easy.  

```bash
sudo docker run --rm --name meili -d -p 7700:7700 -v $(pwd)/../meili_data:/meili_data getmeili/meilisearch:latest meilisearch
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

## Idea: Workflow for new datasets

```bash
wget https://github.com/algolia/datasets/raw/refs/heads/master/wine/bordeaux.json -O data/wine.json
c inspect:json --dto src/Dto/Wine.php 
# Add Facet and other attributes to Wine.php
c meili:index Wine data/wine.json --update-settings --import
symfony open:local /meili/wine

```

## With Survos Bundle Development
```bash
git clone git@github.com:survos/survos ../survos
cd ../survos && composer install && cd ../dt-demo
../survos/link . 
```

@todo: meili-bundle component


## Jeopardy

https://github.com/jwolle1/jeopardy_clue_dataset/raw/refs/heads/main/combined_season1-40.tsv

or https://github.com/jwolle1/jeopardy_clue_dataset/archive/refs/heads/main.zip

or kaggle, but can't find direct download link

