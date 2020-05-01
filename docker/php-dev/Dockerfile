FROM iras/php7:3

VOLUME /var/www/localhost/htdocs
VOLUME /var/log/php

ARG UID
ARG GID

RUN apk add --no-cache php7-fpm ca-certificates openssl wget bash vim strace php7-phpdbg \
 # configure php fpm
 && sed -i 's/^;daemonize = yes/daemonize = no/g' /etc/php7/php-fpm.conf \
 && sed -i 's/^;error_log = log\/php7\/error.log/error_log = log\/php\/php-error.log/g' /etc/php7/php-fpm.conf \
 && ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm \
 # add the user
 && addgroup -g ${GID-333} www-user \
 && adduser -G www-user -D -u ${UID-333} www-user \
 && mkdir -p /var/log/php \
 && chown -R www-user:www-user /var/log/php /home/www-user \
 # setup psysh config
 && mkdir -p /home/www-user/.config/psysh \
 && { \
        echo '<?php'; \
        echo ''; \
        echo 'return ['; \
        echo '  "pager" => "more",'; \
        echo '  "historySize" => 0,'; \
        echo '  "eraseDuplicates" => true,'; \
        echo '  "updateCheck" => "never",'; \
        echo '];'; \
        echo ''; \
    } > /home/www-user/.config/psysh/config.php \
 # download manual for psysh
 && mkdir -p /usr/local/share/psysh \
 && wget https://psysh.org/manual/en/php_manual.sqlite -qO /usr/local/share/psysh/php_manual.sqlite

# add xdebug commands
COPY xdebug.sh /usr/bin/xdebug
COPY xdebug-profile.sh /usr/bin/xdebug-profile
RUN chmod +x /usr/bin/xdebug /usr/bin/xdebug-profile

COPY fpm.conf /etc/php7/php-fpm.d/www.conf

WORKDIR /community

CMD ["php-fpm"]

# phpstorm helpers
RUN ln -s /usr/bin/phpdbg /phpdbg \
 && mkdir -p /opt/phpstorm-coverage \
 && chown www-user:www-user /opt/phpstorm-coverage

ENV APP_ENV testing
