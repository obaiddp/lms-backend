# Stage 1: Builder for dependencies
FROM php:8.2-fpm AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libmariadb-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libexif-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    opcache \
    intl \
    zip \
    bcmath \
    soap \
    exif \
    gd \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Stage 2: Production
FROM php:8.2-fpm AS production

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libmariadb-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libexif-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    netcat-openbsd \
    libfcgi-dev \
    fcgiwrap \
    libfcgi0ldbl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    opcache \
    intl \
    zip \
    bcmath \
    soap \
    exif \
    gd \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP-FPM health check script
RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

WORKDIR /var/www

# Copy the entire application
COPY . .

# Copy extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Use production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure PHP for larger uploads and longer execution times
RUN echo "upload_max_filesize = 100M" >> "$PHP_INI_DIR/php.ini" && \
    echo "post_max_size = 120M" >> "$PHP_INI_DIR/php.ini" && \
    echo "max_execution_time = 300" >> "$PHP_INI_DIR/php.ini" && \
    echo "max_input_time = 300" >> "$PHP_INI_DIR/php.ini" && \
    echo "memory_limit = 512M" >> "$PHP_INI_DIR/php.ini"

# Optimize OPcache for maximum performance
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.enable_cli=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.interned_strings_buffer=16" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.validate_timestamps=0" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.save_comments=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.fast_shutdown=1" >> "$PHP_INI_DIR/php.ini"

# Configure PHP-FPM to listen on all interfaces and add status endpoint
RUN echo 'listen = 0.0.0.0:9000' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.status_path = /status' >> /usr/local/etc/php-fpm.d/www.conf

# Optimize PHP-FPM for better performance and ensure proper network binding
RUN echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 4' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 2' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 6' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 20' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_requests = 1000' >> /usr/local/etc/php-fpm.d/www.conf

# Copy storage directory for initialization
COPY ./storage /var/www/storage-init

# Copy production entrypoint
COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Create all Laravel directories and set proper permissions
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/logs \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/testing \
    /var/www/storage/framework/views \  
    /var/www/bootstrap/cache \
    /var/www/public/storage \
    /var/www/resources/lang && \
    touch /var/www/storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/resources/lang && \
    chmod 664 /var/www/storage/logs/laravel.log

WORKDIR /var/www

# Ensure we run as root for permission handling
USER root

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm", "-F"]

# Stage 3: Development
FROM production AS development

ARG UID=1000
ARG GID=1000
ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER
ARG XDEBUG_LOG=/dev/stdout
ARG XDEBUG_LOG_LEVEL=0

# Set UID/GID as environment variables for runtime
ENV UID=${UID}
ENV GID=${GID}

# Add Xdebug only if enabled
RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=${XDEBUG_LOG}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=${XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
fi

# Override with development entrypoint (handled by docker-compose volume mount)
COPY ./docker/development/php-fpm/entrypoint.sh /usr/local/bin/dev-entrypoint.sh
RUN chmod +x /usr/local/bin/dev-entrypoint.sh

# Use development PHP configuration for better debugging
RUN mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini.bak" && \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Configure PHP for larger uploads and longer execution times (development)
RUN echo "upload_max_filesize = 100M" >> "$PHP_INI_DIR/php.ini" && \
    echo "post_max_size = 120M" >> "$PHP_INI_DIR/php.ini" && \
    echo "max_execution_time = 300" >> "$PHP_INI_DIR/php.ini" && \
    echo "max_input_time = 300" >> "$PHP_INI_DIR/php.ini" && \
    echo "memory_limit = 512M" >> "$PHP_INI_DIR/php.ini"

# Optimize OPcache for development (with file validation for hot reloading)
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.enable_cli=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.interned_strings_buffer=16" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.validate_timestamps=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.revalidate_freq=2" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.save_comments=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.fast_shutdown=1" >> "$PHP_INI_DIR/php.ini"

WORKDIR /var/www

# Ensure we run as root for permission handling in development
USER root

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm", "-F"]