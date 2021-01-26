# tflori/riki-community

[![Build Status](https://travis-ci.com/tflori/riki-community.svg?branch=master)](https://travis-ci.com/tflori/riki-community)
[![Maintainability](https://api.codeclimate.com/v1/badges/14c9aec9c2b0e9860c43/maintainability)](https://codeclimate.com/github/tflori/riki-community/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/14c9aec9c2b0e9860c43/test_coverage)](https://codeclimate.com/github/tflori/riki-community/test_coverage)

Community website for the riki framework. Conceptual ideas at https://github.com/tflori/riki-concepts

## Setup Development Environment

While you can still modify the ignored files (`.env` and `docker-compose.yml`) and work with them using docker-compose
commands, you can also let bin/dev handle most of the stuff.

Have a look at the command list:

```console
$ bin/dev list
bin/dev <command> [options]

Actions:
  start           Start the development environment (checks and prepares the environment first)
  status          Show the status of the development environment
  stop            Stop the development environment
  restart         Restart the development environment
  xdebug          Show xdebug status or enable/disable xdebug via http
  cli             execute a cli command (use --help for more info)
  sh              start a shell on on a container
  test            execute tests (todo)
  build           Build the docker image for the php containers
  composer        Execute a composer command
  npm             Execute a npm command
  env             Create missing environment variables
  list            Show the list of commands
```

When you want to know how to handle the stuff manually then please have a look at the file `bin/dev` to get a clue how
it works.

You should always use the composer and npm container to upgrade or add dependencies to ensure it is compatible with the
versions on production environments.
