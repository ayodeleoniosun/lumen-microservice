#!/bin/bash
set -e

GREEN=$(tput setaf 2)
PINK=$(tput setaf 5)

echo "${PINK}Installing composer packages ..."

# Setup env variables for apigateway and install composer dependencies
cd apps/apigateway
cp .env.example .env
composer install --quiet

# Setup env variables for postservice and install composer dependencies
cd ../ && cd postservice
cp .env.example .env
composer install --quiet

# Setup env variables for commentservice and install composer dependencies
cd ../ && cd commentservice
cp .env.example .env
composer install --quiet

# Setup env variables for docker compose
cd ../../
cp .env.example .env

echo "${PINK}Building docker images ..."

# Build docker images
docker-compose build

# Spring up docker containers in detached mode
docker-compose up -d --force-recreate

echo "${PINK}Updating APP_KEYs ..."

# Update APP_KEY across services
docker-compose exec apigateway php artisan update:env
docker-compose exec postservice php artisan update:env
docker-compose exec commentservice php artisan update:env

echo "${PINK}Running migrations ..."

# Run database migrations for each service alongside the seeders
docker-compose exec apigateway php artisan migrate:fresh --seed
docker-compose exec postservice php artisan migrate:fresh --seed
docker-compose exec commentservice php artisan migrate:fresh --seed

# Generate oauth secret
docker-compose exec apigateway php artisan passport:install --force

echo "${GREEN}Set up completed!"