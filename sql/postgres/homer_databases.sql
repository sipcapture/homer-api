SET default_tablespace = homer;

CREATE DATABASE homer_data;
CREATE DATABASE homer_configuration;
CREATE DATABASE homer_statistic;

GRANT ALL PRIVILEGES ON DATABASE homer_configuration TO homer_user;
GRANT ALL PRIVILEGES ON DATABASE homer_statistic TO homer_user;
GRANT ALL PRIVILEGES ON DATABASE homer_data TO homer_user;
