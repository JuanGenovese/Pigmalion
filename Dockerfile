FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    bash

# Get official Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app
