# note --details removed until sais server is working
bin/console app:load-data --limit 50  -vv
bin/console grid:index
symfony server:start -d
symfony open:local
