SET default_tablespace = homer;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO homer_user;

CREATE TABLE IF NOT EXISTS alias (
  id SERIAL NOT NULL,
  gid INTEGER NOT NULL DEFAULT 0,
  ip varchar(80) NOT NULL DEFAULT '',
  port INTEGER NOT NULL DEFAULT '0', 
  capture_id varchar(100) NOT NULL DEFAULT '',
  alias varchar(100) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT 0,  
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);  

CREATE UNIQUE INDEX alias_id ON alias (id);
CREATE UNIQUE INDEX alias_id_port_captid ON alias (id,port,capture_id);
CREATE INDEX alias_ip_idx ON alias (ip);


INSERT INTO alias (id, gid, ip, port, capture_id, alias, status, created) VALUES
(1, 10, '192.168.0.30', 0, 'homer01', 'proxy01', 1, '2014-06-12 20:36:50'),
(2, 10, '192.168.0.4', 0, 'homer01', 'acme-234', 1, '2014-06-12 20:37:01'),
(22, 10, '127.0.0.1:5060', 0, 'homer01', 'sip.local.net', 1, '2014-06-12 20:37:01');


CREATE TABLE IF NOT EXISTS "group" (
  gid INTEGER NOT NULL DEFAULT 0,
  name varchar(100) NOT NULL DEFAULT ''
);

CREATE UNIQUE INDEX group_gid ON "group" (gid);
INSERT INTO "group" (gid, name) VALUES (10, 'Administrator');


CREATE TABLE IF NOT EXISTS link_share (
  id SERIAL NOT NULL,
  uid INTEGER NOT NULL DEFAULT 0,
  uuid varchar(120) NOT NULL DEFAULT '',
  data text NOT NULL,
  expire timestamp NOT NULL DEFAULT '2032-12-31 00:00:00',
  active smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
)  ;


CREATE TABLE IF NOT EXISTS node (
  id SERIAL NOT NULL,
  host varchar(80) NOT NULL DEFAULT '',
  dbname varchar(100) NOT NULL DEFAULT '',
  dbport varchar(100) NOT NULL DEFAULT '',
  dbusername varchar(100) NOT NULL DEFAULT '',
  dbpassword varchar(100) NOT NULL DEFAULT '',
  dbtables varchar(100) NOT NULL DEFAULT 'sip_capture',
  name varchar(100) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ;

CREATE UNIQUE INDEX node_id ON node (id);
CREATE UNIQUE INDEX node_host ON node (host);
CREATE INDEX node_host_idx ON node (host);

INSERT INTO node (id, host, dbname, dbport, dbusername, dbpassword, dbtables, name, status) VALUES
(1, '127.0.0.1', 'homer_data', '3306', 'homer_user', 'mysql_password', 'sip_capture', 'homer01', 1),
(21, '10.1.0.7', 'homer_data', '3306', 'homer_user', 'mysql_password', 'sip_capture', 'external', 1);


CREATE TABLE IF NOT EXISTS setting (
  id SERIAL NOT NULL,
  uid INTEGER NOT NULL DEFAULT '0',
  param_name varchar(120) NOT NULL DEFAULT '',
  param_value text NOT NULL,
  valid_param_from timestamp NOT NULL DEFAULT '2012-01-01 00:00:00',
  valid_param_to timestamp NOT NULL DEFAULT '2032-12-01 00:00:00',
  param_prio integer NOT NULL DEFAULT '10',
  active INTEGER NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX setting_id ON setting (uid,param_name);
CREATE INDEX setting_param_name ON setting (param_name);
CREATE INDEX setting_uid ON setting (uid);

INSERT INTO setting (id, uid, param_name, param_value, valid_param_from, valid_param_to, param_prio, active) VALUES
(1, 1, 'timerange', '{"from":"2015-05-26T18:34:42.654Z","to":"2015-05-26T18:44:42.654Z"}', '2012-01-01 00:00:00', '2032-12-01 00:00:00', 10, 1);

CREATE TABLE IF NOT EXISTS "user" (
  uid SERIAL NOT NULL,
  gid INTEGER NOT NULL DEFAULT '10',
  grp varchar(200) NOT NULL DEFAULT '',
  username varchar(50) NOT NULL DEFAULT '',
  password varchar(100) NOT NULL DEFAULT '',
  firstname varchar(250) NOT NULL DEFAULT '',
  lastname varchar(250) NOT NULL DEFAULT '',
  email varchar(250) NOT NULL DEFAULT '',
  department varchar(100) NOT NULL DEFAULT '',
  regdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  lastvisit timestamp NOT NULL,
  active smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (uid)
);

CREATE UNIQUE INDEX user_name ON "user" (username);

INSERT INTO "user" (uid, gid, grp, username, password, firstname, lastname, email, department, regdate, lastvisit, active) VALUES
(1, 10, 'users,admins', 'admin', crypt('test123', gen_salt('md5')), 'Admin', 'Admin', 'admin@test.com', 'Voice Enginering', '2012-01-19 00:00:00', '2015-05-29 07:17:35', 1),
(2, 10, 'users', 'noc', crypt('123test', gen_salt('md5')), 'NOC', 'NOC', 'noc@test.com', 'Voice NOC', '2012-01-19 00:00:00', '2015-05-29 07:17:35', 1);


CREATE TABLE IF NOT EXISTS user_menu (
  id varchar(125) NOT NULL DEFAULT '',
  name varchar(100) NOT NULL DEFAULT '',
  alias varchar(200) NOT NULL DEFAULT '',
  icon varchar(100) NOT NULL DEFAULT '',
  weight INTEGER NOT NULL DEFAULT '10',
  active INTEGER NOT NULL DEFAULT '1'
);

CREATE UNIQUE INDEX user_menu_id ON "user_menu" (id);

INSERT INTO user_menu (id, name, alias, icon, weight, active) VALUES
('_1426001444630', 'SIP Search', 'search', 'fa-search', 10, 1),
('_1427728371642', 'Home', 'home', 'fa-home', 1, 1),
('_1431721484444', 'Alarms', 'alarms', 'fa-warning', 20, 1);


--
-- Table structure for table `api_auth_key`
--
  
CREATE TABLE IF NOT EXISTS api_auth_key (
  id SERIAL NOT NULL,
  authkey varchar(200) NOT NULL DEFAULT '',
  source_ip varchar(200) NOT NULL DEFAULT '0.0.0.0',
  startdate timestamp NOT NULL DEFAULT '2012-01-01 00:00:00',
  stopdate timestamp NOT NULL DEFAULT '2031-01-01 00:00:00',
  userobject varchar(250) NOT NULL DEFAULT '',
  description varchar(200) NOT NULL DEFAULT '',
  lastvisit timestamp NOT NULL,
  enable smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX api_auth_key_authkey ON "api_auth_key" (authkey);
