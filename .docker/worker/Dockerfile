FROM php:8.3.16-fpm

# Set the working directory
WORKDIR /var/www/html

# Install Supervisor and cron
RUN apt-get update && apt-get install -y supervisor cron logrotate nano autoconf g++ make \
    && docker-php-ext-install pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy Logrotate configuration
COPY logrotate.d/work.conf /etc/logrotate.d/work.conf

# Ajusta permissões
RUN chmod 644 /etc/logrotate.d/work.conf

# Ensure Laravel cron is added to the crontab
RUN (echo "* * * * * /usr/local/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1" \
    && echo "0 0 * * * /usr/sbin/logrotate /etc/logrotate.d/work.conf >> /dev/null 2>&1") \
    | crontab -

ENV LD_LIBRARY_PATH=/usr/local/instantclient
ENV ORACLE_HOME=/usr/local/instantclient
ENV PATH=/usr/local/instantclient:$PATH

# Ensure cron and supervisord are both started
CMD service cron start && supervisord -c /etc/supervisor/conf.d/supervisord.conf
