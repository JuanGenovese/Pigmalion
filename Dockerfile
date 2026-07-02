FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    zip \
    unzip \
    git \
    bash

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
