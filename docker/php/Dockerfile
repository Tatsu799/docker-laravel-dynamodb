FROM php:8.1-fpm

# 必要な拡張をインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql xml

# Composerのインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# old
# # 使用する PHP バージョンを指定
# FROM php:8.1-fpm

# # 必要な PHP 拡張モジュールをインストール
# RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git && \
#     docker-php-ext-configure gd --with-freetype --with-jpeg && \
#     docker-php-ext-install gd pdo pdo_mysql

# # Composer のインストール
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# WORKDIR /var/www/html

# # Laravel アプリケーションの依存関係をインストール
# COPY . /var/www/html

# # composer install を実行して Laravel の依存関係をインストール
# RUN composer install --no-interaction --prefer-dist