## Complete datatables-demo project, Symfony 7.0

gh repo clone survos-sites/dt-demo
cd dt-demo
composer install
bin/console d:sch:update --force 
bin/console app:load-data

### Recreate the demo

Use 6.4 beta, add stimulus, api-platform, doctrine-futures
Later we'll add the survos bundles

```bash
rm -rf all-demo
symfony new all-demo --webapp --version=next && cd all-demo
composer req symfony/asset-mapper api symfony/stimulus-bundle:^2.x-dev
composer req survos/scraper-bundle survos/bootstrap-bundle
composer req survos/maker-bundle --dev
composer config extra.symfony.allow-contrib true

bin/console make:controller AppController
sed -i "s|Route('/app'|Route('/'|" src/Controller/AppController.php
sed -i "s|'app_app'|'app_homepage'|" src/Controller/AppController.php

bin/console survos:make:menu 

cat > templates/base.html.twig <<END
{% extends "@SurvosBootstrap/%s/base.html.twig"|format(theme_option('theme')) %}

{% block javascripts %}
    {{ importmap('app') }}
{% endblock %}
END

# add bootstrap csss
echo "import 'bootstrap/dist/css/bootstrap.min.css'" >> assets/app.js

cat > templates/app/index.html.twig <<END
{% extends 'base.html.twig' %}
{% block body %}
    Some Grid Demos

    {% set url = 'https://jsonplaceholder.typicode.com/users' %}
    {% set users = request_data(url) %}

    {% set columns = users[0]|keys %}
    {{ dump(users[0]) }}
{#    <twig:grid :data="users" :columns="columns" useDatatables="false">#}
{#        <twig:block name="id">#}
{#            {{ row.id }}#}
{#        </twig:block>#}
{#    </twig:grid>#}
{% endblock %}
END

composer req symfony/stimulus-bundle:2.x-dev


composer require orm-fixtures --dev 

echo "DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db" > .env.local
```

### Business Logic: Official/Term Entities

```bash
echo "firstName,string,16,yes," | sed "s/,/\n/g"  | bin/console -a make:entity Official
echo "lastName,string,32,no," | sed "s/,/\n/g"  | bin/console make:entity Official
echo "officialName,string,48,no," | sed "s/,/\n/g"  | bin/console make:entity Official
echo "birthday,date_immutable,yes," | sed "s/,/\n/g"  | bin/console make:entity Official
echo "gender,string,1,yes," | sed "s/,/\n/g"  | bin/console make:entity Official

# terms 
echo "offical,ManyToOne,Official,no,yes,terms,yes," | sed "s/,/\n/g"  | bin/console -a make:entity Term -a
echo "type,string,16,yes," | sed "s/,/\n/g"  | bin/console make:entity Term
echo "stateAbbreviation,string,2,yes," | sed "s/,/\n/g"  | bin/console make:entity Term
echo "party,string,8,yes," | sed "s/,/\n/g"  | bin/console make:entity Term
echo "district,string,8,yes," | sed "s/,/\n/g"  | bin/console make:entity Term
echo "startDate,date_immutable,yes," | sed "s/,/\n/g"  | bin/console make:entity Term
echo "endDate,date_immutable,yes," | sed "s/,/\n/g"  | bin/console make:entity Term

```

### Populate the datatbase

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:update --complete --force
bin/console make:fixtures CongressFixtures

cat > src/DataFixtures/AppFixtures.php < 'END'
bin/console doctrine:fixtures:load -n 
```

### Generate CRUD Controllers
echo "Official,CongressCrudController,no" | sed "s/,/\n/g"  | bin/console make:crud
echo "Term,TermCrudController,no" | sed "s/,/\n/g"  | bin/console make:crud

### 
bin/console importmap:require bootstrap
@todo: have the recipe import these lines to app.js, twig.yaml
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap';
twig form themes.

bin/console make:controller AppController
sed -i "s|Route('/app'|Route('/'|" src/Controller/AppController.php
sed -i "s|'app_app'|'app_homepage'|" src/Controller/AppController.php

cat > templates/app/index.html.twig <<END
{% extends 'base.html.twig' %}
END

### Authentication

We have a basic CRUD application, not unattractive, but anyone can edit the data.

Symfony has some great RAD tools for create a basic login system.

```bash
bin/console make:user User --is-entity --identity-property-name=email --with-password -n
#sed -i "s|public function getEmail| public function getUsername() { return \$this->getEmail(); }\n\n public function getEmail|" src/Entity/User.php

sed -i "s|# MAILER_DSN|MAILER_DSN|" .env


echo "1,AppAuthenticator,SecurityController,/logout," | sed "s/,/\n/g"  | bin/console make:auth
sed -i "s|// For example.*;|return new RedirectResponse(\$this->urlGenerator->generate('app_homepage'));|" src/Security/AppAuthenticator.php
sed -i "s|throw new \\Exception\('TODO\: provide a valid redirect inside '\.__FILE__\);||" src/Security/AppAuthenticator.php
```

We need to add an admin user.  We could modify the fixtures, or we can use a bundle that gives us a console command to create the user.

```bash
composer req survos/auth-bundle
bin/console survos:user:create admin@example.com password --roles=ROLE_ADMIN
```

* 
* 
* No navigation.

We can add security and then modify the new/edit/delete controllers to have a #[IsGranted('ROLE_ADMIN')] attribute.  And the we can modify the twig file to make sure those links are only displayed when an admin is logged in.  And we can add a menu at the top.

To do that, we going to introduce some survos bundles.

### Symfony Authentication

### Survos Authentication

We need to add an admin user to the database.  Install the survos/auth-bundle for console command to do that
composer req survos/auth-bundle
bin/console survos:create:user
kbond p4ssw0rd -r ROLE_EDITOR -r ROLE_ADMIN

composer req survos/datatables-bundle

## Deploying to github pages

bin/console asset-map:compile
bin/console -e prod cache:clear
bin/console -e prod stenope:build ./docs --base-url /dt-demo/
rm public/assets/ -rf 

bin/console -e prod cache:clear
bin/console -e prod stenope:build ~/pages/dt-demo
cd ~/pages/dt-demo
php -S localhost:9000 -t=.

