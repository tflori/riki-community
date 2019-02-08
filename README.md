# tflori/riki-community 

Community website for the riki framework. Conceptual ideas at https://github.com/tflori/riki-concepts

## Installation

Copy the docker-compose.example.yml to docker-compose.yml and adjust it to your needs.

```console
$ docker-compose pull
$ docker-compose run --rm --entrypoint sh -u 0 npm -c "chown <UID> -R /home/node/.npm"
$ docker-compose run --rm --entrypoint sh -u 0 composer -c "chown <UID> -R /composer/cache"
$ docker-compose run --rm composer install
$ docker-compose build
$ composer start
```
