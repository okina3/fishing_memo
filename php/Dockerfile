# PHP 8.2とApacheをベースイメージとして使用
FROM php:8.2-apache

# Apache用のポートを公開
EXPOSE 5173

# パッケージを更新し、依存関係をインストール
RUN apt update \
   && apt install -y --no-install-recommends \
   g++ \  
   libicu-dev \
   libpq-dev \
   libzip-dev \
   libpng-dev \
   libjpeg-dev \
   zip \
   zip unzip \
   zlib1g-dev \
   npm \
   nodejs \
   vim \
   && docker-php-ext-configure gd --with-jpeg \ 
   && docker-php-ext-install \
   intl \
   opcache \
   pdo \
   pdo_pgsql \
   pgsql \
   pdo_mysql \
   bcmath \ 
   -j$(nproc) \ 
   gd 

# Composerをグローバルにインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ワークディレクトリを/var/www/fishing_memoに設定
WORKDIR /var/www/fishing_memo