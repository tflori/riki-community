FROM iras/php7-fpm:3

RUN apk add --no-cache ca-certificates openssl wget bash \

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
 && wget https://psysh.org/manual/en/php_manual.sqlite -qO /usr/local/share/psysh/php_manual.sqlite \

 # enable environment variables in php
 && echo 'env[APP_ENV] = $APP_ENV' >> /etc/php7/php-fpm.d/www.conf \
 && echo 'env[PATH] = $PATH' >> /etc/php7/php-fpm.d/www.conf

COPY public /community/public
COPY src /community/src
COPY app /community/app
COPY bin /community/bin
COPY vendor /community/vendor
COPY resources /community/resources
COPY storage /community/storage
COPY ["composer.json", "composer.lock", "/community/"]

WORKDIR /community

ENV PATH /community/bin:$PATH
ENV APP_ENV testing
