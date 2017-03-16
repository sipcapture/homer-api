CREATE USER homer_user WITH PASSWORD 'homer_password';

grant postgres to homer_user;

-- please activate pgcrypto as the DBA (postgress)
-- psql homer_configuration
-- CREATE EXTENSION pgcrypto;
