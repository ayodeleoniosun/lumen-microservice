#!/bin/bash
set -e

GREEN=$(tput setaf 2)

echo "${GREEN}Running apigateway tests ..."

docker-compose exec apigateway ./vendor/bin/phpunit

echo "${GREEN}Running postservice tests ..."

docker-compose exec postservice ./vendor/bin/phpunit

echo "${GREEN}Running commentservice tests ..."

docker-compose exec commentservice ./vendor/bin/phpunit
