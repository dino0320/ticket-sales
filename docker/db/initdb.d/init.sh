#!/bin/bash

set -exo pipefail

psql -U postgres -d $POSTGRES_DB -w <<-EOSQL
    CREATE DATABASE "test_database";
    
    CREATE USER "user" WITH PASSWORD 'password';
    GRANT CREATE ON SCHEMA public TO "user";
    GRANT ALL ON DATABASE "test_database" TO "user";
EOSQL

psql -U postgres -d test_database -w <<-EOSQL
    GRANT CREATE ON SCHEMA public TO "user";
EOSQL