#!/bin/bash

composer install
dockerize -wait tcp://db:3306 -timeout 40s
ls -la
php-fpm
