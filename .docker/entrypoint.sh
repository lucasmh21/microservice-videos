#!/bin/bash

echo teste
dockerize -template ./.docker/app/.env:.env -wait tcp://db:3306 -timeout 40s
php-fpm
