FROM nginx:alpine

COPY ./docker/nginx/vhost.conf.nginx /etc/nginx/conf.d/default.conf
COPY . /var/www/app

ENV NGINX_HOST=bookmarks.localhost

WORKDIR /var/www/app
