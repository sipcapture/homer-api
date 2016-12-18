#!/bin/bash

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")

function create_tablespace {
    su -c "psql -f $SCRIPTPATH/../../sql/postgres/homer_tablespace.sql" postgres
}

function create_databases {
    su -c "psql -f $SCRIPTPATH/../../sql/postgres/homer_databases.sql" postgres
}

function create_user {
    su -c "psql -f $SCRIPTPATH/../../sql/postgres/homer_user.sql" postgres
}

echo "Creating /data/homer dir..."
mkdir -p /data/homer
echo "Grant permission to /data..."
chown -R postgres:postgres /data
echo "Creating homer_user on postgres..."
create_user
echo "Creating tablespace homer..."
create_tablespace
echo "Creating databases (homer_*)..."
create_databases
echo "Done."
