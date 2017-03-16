SET default_tablespace = homer;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO homer_user;

CREATE TABLE IF NOT EXISTS alarm_config (
  id SERIAL NOT NULL,
  name varchar(200) NOT NULL DEFAULT '',
  startdate timestamp NOT NULL,
  stopdate timestamp NOT NULL,
  type varchar(50) NOT NULL DEFAULT '',
  value integer NOT NULL DEFAULT 0,
  notify smallint NOT NULL DEFAULT '1',
  email varchar(200) NOT NULL DEFAULT '',
  createdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  active smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX alarm_config_type ON "alarm_config" (type);

CREATE TABLE IF NOT EXISTS alarm_data (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL DEFAULT 0,
  source_ip varchar(150) NOT NULL DEFAULT '0.0.0.0',
  description varchar(256) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (id,create_date)
);

CREATE INDEX alarm_data_to_date ON "alarm_data" (create_date);
CREATE INDEX alarm_data_method ON "alarm_data" (type);
CREATE TABLE alarm_data_p2013082901() INHERITS (alarm_data);
ALTER TABLE alarm_data_p2013082901 ADD CONSTRAINT chk_alarm_data_p2013082901 CHECK (create_date < to_timestamp(1377734400)); 


CREATE TABLE IF NOT EXISTS alarm_data_mem (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL DEFAULT 0,
  source_ip varchar(150) NOT NULL DEFAULT '0.0.0.0',
  description varchar(256) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX alarm_data_mem_type ON "alarm_data_mem" (type,source_ip);
CREATE INDEX alarm_data_mem_to_date ON "alarm_data_mem" (create_date);
CREATE INDEX alarm_data_mem_method ON "alarm_data_mem" (type);


CREATE TABLE IF NOT EXISTS stats_data (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  type varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_data_datemethod ON "stats_data" (from_date,to_date,type);
CREATE INDEX stats_data_from_date ON "stats_data" (from_date);
CREATE INDEX stats_data_to_date ON "stats_data" (to_date);
CREATE INDEX stats_data_method ON "stats_data" (type);
CREATE TABLE stats_data_p2013082901() INHERITS (stats_data);
ALTER TABLE stats_data_p2013082901 ADD CONSTRAINT chk_stats_data_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 


CREATE TABLE IF NOT EXISTS stats_ip (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  method varchar(50) NOT NULL DEFAULT '',
  source_ip varchar(255) NOT NULL DEFAULT '0.0.0.0',
  total integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_ip_datemethod ON "stats_ip" (from_date,to_date,method,source_ip);
CREATE INDEX stats_ip_from_date ON "stats_ip" (from_date);
CREATE INDEX stats_ip_to_date ON "stats_ip" (to_date);
CREATE INDEX stats_ip_method ON "stats_ip" (method);
CREATE TABLE stats_ip_p2013082901() INHERITS (stats_ip);
ALTER TABLE stats_ip_p2013082901 ADD CONSTRAINT chk_stats_ip_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 



CREATE TABLE IF NOT EXISTS stats_ip_mem (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  method varchar(50) NOT NULL DEFAULT '',
  source_ip varchar(255) NOT NULL DEFAULT '0.0.0.0',
  total integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX stats_ip_mem_datemethod ON "stats_ip_mem" (method,source_ip);


CREATE TABLE IF NOT EXISTS stats_geo_mem (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  method varchar(50) NOT NULL DEFAULT '',
  country varchar(255) NOT NULL DEFAULT 'UN',
  lat float NOT NULL DEFAULT '0',
  lon float NOT NULL DEFAULT '0',
  total integer NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX stats_geo_mem_datemethod ON "stats_geo_mem" (method,country);

CREATE TABLE IF NOT EXISTS stats_geo (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  method varchar(50) NOT NULL DEFAULT '',
  country varchar(255) NOT NULL DEFAULT 'UN',
  lat float NOT NULL DEFAULT '0',
  lon float NOT NULL DEFAULT '0',
  total integer NOT NULL DEFAULT '0',
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_geo_datemethod ON "stats_geo" (from_date,to_date,method,country);
CREATE INDEX stats_geo_from_date ON "stats_geo" (from_date);
CREATE INDEX stats_geo_to_date ON "stats_geo" (to_date);
CREATE INDEX stats_geo_method ON "stats_geo" (method);
CREATE TABLE stats_geo_p2013082901() INHERITS (stats_geo);
ALTER TABLE stats_geo_p2013082901 ADD CONSTRAINT chk_stats_geo_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 


CREATE TABLE IF NOT EXISTS stats_method (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  method varchar(50) NOT NULL DEFAULT '',
  auth smallint NOT NULL DEFAULT '0',
  cseq varchar(100) NOT NULL DEFAULT '',
  totag smallint NOT NULL DEFAULT 0,
  total integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_method_datemethod ON "stats_method" (from_date,to_date,method,auth,totag,cseq);
CREATE INDEX stats_method_from_date ON "stats_method" (from_date);
CREATE INDEX stats_method_to_date ON "stats_method" (to_date);
CREATE INDEX stats_method_method ON "stats_method" (method);
CREATE INDEX stats_method_completed ON "stats_method" (cseq);
CREATE TABLE stats_method_p2013082901() INHERITS (stats_method);
ALTER TABLE stats_method_p2013082901 ADD CONSTRAINT chk_stats_method_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 


CREATE TABLE IF NOT EXISTS stats_method_mem (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  method varchar(50) NOT NULL DEFAULT '',
  auth smallint NOT NULL DEFAULT '0',
  cseq varchar(100) NOT NULL DEFAULT '',
  totag smallint NOT NULL DEFAULT 0,
  total integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX stats_method_mem_datemethod ON "stats_method_mem" (method,auth,totag,cseq);
CREATE INDEX stats_method_mem_from_date ON "stats_method_mem" (create_date);
CREATE INDEX stats_method_mem_method ON "stats_method_mem" (method);
CREATE INDEX stats_method_mem_completed ON "stats_method_mem" (cseq);

CREATE TABLE IF NOT EXISTS stats_useragent (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  useragent varchar(100) NOT NULL DEFAULT '',
  method varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL DEFAULT '0',
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_useragent_datemethod ON "stats_useragent" (from_date,to_date,method,useragent);
CREATE INDEX stats_useragent_from_date ON "stats_useragent" (from_date);
CREATE INDEX stats_useragent_to_date ON "stats_useragent" (to_date);
CREATE INDEX stats_useragent_useragent ON "stats_useragent" (useragent);
CREATE INDEX stats_useragent_method ON "stats_useragent" (method);
CREATE INDEX stats_useragent_total ON "stats_useragent" (total);
CREATE TABLE stats_useragent_p2013082901() INHERITS (stats_useragent);
ALTER TABLE stats_useragent_p2013082901 ADD CONSTRAINT chk_stats_useragent_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 


CREATE TABLE IF NOT EXISTS stats_useragent_mem (
  id BIGSERIAL NOT NULL,
  create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  useragent varchar(100) NOT NULL DEFAULT '',
  method varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX stats_useragent_mem_useragent ON "stats_useragent_mem" (useragent,method);


CREATE TABLE IF NOT EXISTS stats_generic (
  id BIGSERIAL NOT NULL,
  from_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  to_date timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  type varchar(50) NOT NULL DEFAULT '',
  tag varchar(50) NOT NULL DEFAULT '',
  total integer NOT NULL,
  PRIMARY KEY (id,from_date)
);

CREATE UNIQUE INDEX stats_generic_datemethod ON "stats_generic" (from_date,to_date,type,tag);
CREATE INDEX stats_generic_from_date ON "stats_generic" (from_date);
CREATE INDEX stats_generic_to_date ON "stats_generic" (to_date);
CREATE INDEX stats_generic_tag ON "stats_generic" (tag);
CREATE TABLE stats_generic_p2013082901() INHERITS (stats_generic);
ALTER TABLE stats_generic_p2013082901 ADD CONSTRAINT chk_stats_generic_p2013082901 CHECK (from_date < to_timestamp(1377734400)); 

