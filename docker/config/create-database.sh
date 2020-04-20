#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE USER riki WITH PASSWORD '${POSTGRES_PASSWORD}';
    CREATE DATABASE riki_community;
    GRANT ALL PRIVILEGES ON DATABASE riki_community TO riki;
EOSQL
