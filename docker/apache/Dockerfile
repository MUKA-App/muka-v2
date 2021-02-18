FROM httpd:2.4-alpine
RUN apk update; \
    apk upgrade;

COPY ./docker/apache/muka.conf /usr/local/apache2/conf/muka.conf
# Copy SSL certificate and private key
COPY ./docker/apache/config/muka.local.crt /usr/local/apache2/conf/server.crt
COPY ./docker/apache/config/muka.local.key /usr/local/apache2/conf/server.key

RUN sed -i '/LoadModule rewrite_module/s/^#//g' /usr/local/apache2/conf/httpd.conf

RUN echo "Include /usr/local/apache2/conf/muka.conf" \
    >> /usr/local/apache2/conf/httpd.conf
