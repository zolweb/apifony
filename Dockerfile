FROM composer:2.8.8
COPY . /app
WORKDIR /app
RUN composer install
ENTRYPOINT ["php", "apifony"]