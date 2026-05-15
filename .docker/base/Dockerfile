# Use uma imagem base que suporte ARM64
FROM php:8.3.16-fpm

# Localizar e configurar mirrors alternativos
COPY /.docker/base/resolv.conf /etc/resolv.conf

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    build-essential \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    unixodbc-dev \
    libaio1 \
    freetds-bin \
    firebird-dev \
    unzip \
    git \
    curl \
    wget \
    zip \
    libpq-dev \
    libnsl-dev \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libmagickwand-dev \
    libjpeg-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install soap
RUN docker-php-ext-install opcache
RUN docker-php-ext-install curl
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install gd
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install xml
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip

RUN docker-php-ext-enable \
    curl \
    gd \
    soap \
    zip

# Instalação das extensões PECL separadamente e habilitação
RUN pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instalação pacote ODBC necessário para sqlserver
# Instalação do ODBC Driver para SQL Server
RUN apt-get update \
    && apt-get install -y gnupg unixodbc-dev \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/10/prod.list | tee /etc/apt/sources.list.d/msprod.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql17

RUN pecl install sqlsrv \
    && docker-php-ext-enable sqlsrv

RUN pecl install pdo_sqlsrv \
    && docker-php-ext-enable pdo_sqlsrv

# Instalar a extensão pdo_firebird
RUN docker-php-ext-install pdo_firebird && \
    docker-php-ext-enable pdo_firebird

# Download Oracle Instant Client files
ENV INSTANTCLIENT_BASIC_URL=https://download.oracle.com/otn_software/linux/instantclient/2340000/instantclient-basic-linux.x64-23.4.0.24.05.zip
ENV INSTANTCLIENT_SDK_URL=https://download.oracle.com/otn_software/linux/instantclient/2340000/instantclient-sdk-linux.x64-23.4.0.24.05.zip
ENV INSTANTCLIENT_SQLPLUS_URL=https://download.oracle.com/otn_software/linux/instantclient/2340000/instantclient-sqlplus-linux.x64-23.4.0.24.05.zip

RUN mkdir -p /tmp/oci && \
    wget --no-check-certificate --no-cookies --header "Cookie: oraclelicense=accept-securebackup-cookie" -O /tmp/oci/oci-basic.zip "$INSTANTCLIENT_BASIC_URL" && \
    wget --no-check-certificate --no-cookies --header "Cookie: oraclelicense=accept-securebackup-cookie" -O /tmp/oci/oci-sdk.zip "$INSTANTCLIENT_SDK_URL" && \
    wget --no-check-certificate --no-cookies --header "Cookie: oraclelicense=accept-securebackup-cookie" -O /tmp/oci/oci-sqlplus.zip "$INSTANTCLIENT_SQLPLUS_URL" && \
    unzip -o /tmp/oci/oci-basic.zip -d /usr/local/ && \
    unzip -o /tmp/oci/oci-sdk.zip -d /usr/local/ && \
    unzip -o /tmp/oci/oci-sqlplus.zip -d /usr/local/ && \
    ln -sf /usr/local/instantclient_23_4 /usr/local/instantclient && \
    ln -sf /usr/local/instantclient_23_4/libclntsh.so.23.1 /usr/local/instantclient/libclntsh.so && \
    ln -sf /usr/local/instantclient_23_4/sdk/include /usr/local/instantclient/include && \
    ln -sf /usr/local/instantclient_23_4/sdk/lib /usr/local/instantclient/lib && \
    rm -rf /tmp/oci

ENV LD_LIBRARY_PATH=/usr/local/instantclient
ENV ORACLE_HOME=/usr/local/instantclient
ENV PATH=/usr/local/instantclient:$PATH

RUN pecl channel-update pecl.php.net && \
    echo 'instantclient,/usr/local/instantclient' | pecl install oci8 && \
    docker-php-ext-enable oci8

RUN rm -rf /tmp/*.zip && rm -rf /tmp/oci

# Install IBM DB2 driver
RUN mkdir -p /opt/ibm \
    && curl -sL -o v11.5fp11_linuxx64_server_t.tar.gz https://public.dhe.ibm.com/ibmdl/export/pub/software/data/db2/drivers/odbc_cli/linuxx64_odbc_cli.tar.gz \
    && tar -xzf v11.5fp11_linuxx64_server_t.tar.gz -C /opt/ibm \
    && rm v11.5fp11_linuxx64_server_t.tar.gz \
    && echo "/opt/ibm/clidriver/lib" > /etc/ld.so.conf.d/db2.conf \
    && ldconfig

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Customizações e alias
COPY /.docker/base/aliases.sh /root/aliases.sh

# Copia os aliases de atalhos
RUN echo "" >> ~/.bashrc && \
    echo "# Load Custom Aliases" >> ~/.bashrc && \
    cat ~/aliases.sh >> ~/.bashrc

# Reinstação e reconfiguração da biblioteca GD
RUN apt-get update && \
    apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg=/usr/include/ --with-webp && \
    docker-php-ext-install gd && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuração do arquivo ini do php
COPY /.docker/base/php-custom.ini /usr/local/etc/php/conf.d/
