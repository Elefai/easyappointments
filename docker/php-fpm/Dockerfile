ARG PHP_VARIANT=8.2
FROM php:${PHP_VARIANT}-fpm

WORKDIR "/var/www/html"

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends git zip unzip ca-certificates; \
    curl -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions; \
    chmod +x /usr/local/bin/install-php-extensions; \
    install-php-extensions \
        gd intl ldap mbstring mysqli pdo pdo_mysql soap sockets xml zip xdebug exif sqlite3 gettext bcmath redis; \
    docker-php-ext-enable xdebug; \
    curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -; \
    apt-get install -y nodejs; \
    npm install -g npm; \
    apt-get install -q -y ssmtp mailutils; \
    echo "hostname=localhost.localdomain" > /etc/ssmtp/ssmtp.conf; \
    echo "root=root@example.org" >> /etc/ssmtp/ssmtp.conf; \
    echo "mailhub=mailpit:1025" >> /etc/ssmtp/ssmtp.conf; \
    echo "sendmail_path=/usr/sbin/ssmtp -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini; \
    echo "alias ll=\"ls -al\"" >> /root/.bashrc; \
    echo "export XDEBUG_TRIGGER=1" >> /root/.bashrc; \
    echo "export PHP_IDE_CONFIG=\"serverName=host.docker.internal\"" >> /root/.bashrc; \
    apt-get -y autoremove; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["bash", "docker/php-fpm/start-container"]
