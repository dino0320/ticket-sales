#!/bin/bash

set -exo pipefail

psql -U postgres -d $POSTGRES_DB -w <<-EOSQL
    CREATE USER "user" WITH PASSWORD 'password';
    GRANT CREATE ON SCHEMA public TO "user";
    CREATE DATABASE "test_database";
    GRANT ALL ON DATABASE "test_database" TO "user";
EOSQL