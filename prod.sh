#!/bin/bash
sudo php app/console cache:clear --env=prod
sudo chmod -R 777 app/logs/
sudo chmod -R 777 app/cache/
php app/console assetic:dump --env=prod
php app/console assets:install web --symlink

