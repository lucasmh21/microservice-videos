#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
dockerize -template ./.docker/app/.env:.env -wait tcp://db:3306 -timeout 40s
php-fpm
