FROM php:7.4-fpm-buster

ARG USER_ID
ARG GROUP_ID

RUN apt-get update && apt-get upgrade -y

RUN apt-get install -y apt-transport-https sudo curl zip unzip pkg-config cron libonig-dev libcurl4-openssl-dev libssl-dev && \
    rm -r /var/lib/apt/lists/* && apt-get autoremove -y && apt-get clean all

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql bcmath

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi && \
    groupadd -g ${GROUP_ID} www-data && \
    useradd -l -u ${USER_ID} -g www-data www-data && \
    install -d -m 0755 -o www-data -g www-data /home/www-data && \
    chown --changes --silent --no-dereference --recursive \
          --from=33:33 ${USER_ID}:${GROUP_ID} \
        /home/www-data \
        /var/www/html \
    ;fi

ADD cron /etc/cron.d/cron
RUN chmod 0644 /etc/cron.d/cron && crontab /etc/cron.d/cron && touch /var/log/cron.log
RUN echo "%www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && architecture=$(uname -m) \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/$architecture/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

USER www-data

COPY startup.sh /var/bin/startup.sh

WORKDIR /var/www/html

EXPOSE 9000

CMD ["/bin/bash", "/var/bin/startup.sh"]
