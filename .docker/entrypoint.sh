#!/bin/bash

dockerize -wait tcp://db:3306 -timeout 40s
php-fpm
