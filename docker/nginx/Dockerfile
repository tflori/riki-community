FROM iras/nginx:1

COPY public /community/public
COPY docker/nginx/server.conf /etc/nginx/conf.d/server/community.conf

ENV DOCUMENT_ROOT /community/public
