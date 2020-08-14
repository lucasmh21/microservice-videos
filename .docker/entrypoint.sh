#!/bin/bash

ls -la
chmod -R 777 .
composer install
dockerize -wait tcp://db:3306 -timeout 40s
ls -la
php-fpm
