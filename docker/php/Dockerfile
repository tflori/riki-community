FROM iras/php7-fpm:2

COPY public /community/public
COPY src /community/src
COPY app /community/app
COPY bin /community/bin
COPY vendor /community/vendor
COPY resources /community/resources
COPY storage /community/storage
COPY ["composer.json", "composer.lock", "/community/"]

WORKDIR /community

COPY docker/php/install.sh /tmp/install.sh
RUN /bin/sh /tmp/install.sh && rm /tmp/install.sh
ENV PATH /community/bin:$PATH
ENV APP_ENV testing
