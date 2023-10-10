FROM nginx:latest as production-stage

COPY ./nginx.conf /etc/nginx/conf.d/default.conf
