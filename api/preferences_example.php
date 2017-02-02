<?php

define('CONFIG_VERSION', "2.0.1"); /* Please ALWAYS include CONFIGVERSION */
define('WEBHOMER_VERSION', "5.1.3"); /* WEBHOMER VERSION */
define('HOMER_TIMEZONE', "Europe/Amsterdam"); /* Set a global application default timezone */

/* CFLOW Options */
define('CFLOW_HPORT', 2); /* Column/Host Mode = Plain: 0, +Port: 1, Auto-Select: 2 */
define('CFLOW_EPORT', 0); /* Enable Ephemeral port detection, experimental */
define('MESSAGE_POPUP',1); /* Modal type: 1, Browser popup: 2 */

/* Search Results Options */
define('RESULTS_ORDER', "asc"); 
define('AUTOCOMPLETE', 0);  /* Enables autocomplete in FROM & TO fiels- WARNING: db intensive */
define('FORMAT_DATE_RESULT', "H:i:s"); /* Controls the Date/Time output in search results, ie: "m-d H:i:s"  */

/* BLEG DETECTION */
define('BLEGDETECT', 1); /* always detect BLEG leg in CFLOW/PCAP*/
define('BLEGCID', "b2b"); /* options: x-cid, b2b */
define('BLEGTAIL', "_b2b-1"); /* session-ID correlation suffix, required for b2b mode */

/* Database: mysql. Moved to configuration.php */
if(!defined('DATABASE_DRIVER')) define('DATABASE_DRIVER',"mysql");

/* AUTH: CLASS NAME. i.e. Internal  */
define('AUTHENTICATION',"Internal");
// define('AUTHENTICATION_TEXT',"Please login with your credentials");

/* ALARM MAIL */
define('ALARM_FROMEMAIL',"homer@example.com");
define('ALARM_TOEMAIL',"admin@example.com");

/* configuration check */
define('NOCHECK', 0); /* set to 1, dont check config */

/* ACCESS LEVEL 3 - Users, 2 - Manager, 1 - Admin, 0 - nobody */
define('ACCESS_DASHBOARD', 3); /* ALARM FOR ALL:*/
define('ACCESS_ALARM', 3); /* ALARM FOR ALL:*/
define('ACCESS_SEARCH', 3); /* SEARCH FOR ALL:*/
define('ACCESS_TOOLBOX', 1); /* TOLBOX FOR ADMIN */
define('ACCESS_STATS', 3); /* STATS FOR ALL */
define('ACCESS_ADMIN', 1); /* ADMIN FOR ADMIN */
define('ACCESS_ACCOUNT', 3); /* ACCOUNT FOR ALL:*/

/* LOGGING. to enable set bigger as 0, if 10 == 10 days keep logs */
define('SEARCHLOG', 0);

/*DEFAULT SELECTED DB NODE */
define('DEFAULTDBNODE',1);
  
define('SESSION_NAME',"HOMERSESSID"); /* session ID name. */
/* session timer */
define('SESSION_TIME', 3600);

/* SQL SCHEMA VERSION */
define('SQL_SCHEMA_VERSION', 5); /* SQL SCHEMA VERSION. Default 5 */

/* database connector Class */
define('DATABASE_CONNECTOR', "PDOConnector");

/* fields */
define('FIELDS_CAPTURE', "id, date, floor(micro_ts /1000) as milli_ts, micro_ts,method,reply_reason,ruri,ruri_user,ruri_domain,from_user,from_domain,from_tag,
to_user,to_domain,to_tag,pid_user,contact_user,auth_user,callid,callid_aleg,via_1,via_1_branch,cseq,diversion,reason,content_type,auth,
user_agent,source_ip,source_port,destination_ip,destination_port,contact_ip,contact_port,originator_ip,originator_port,correlation_id,proto,family,rtp_stat,type,node");

define('ISUP_FIELDS_CAPTURE', "id, date, floor(micro_ts /1000) as milli_ts, micro_ts,correlation_id as callid, '' as callid_aleg, opc,dpc,cic,method,called_number,called_ton,called_npi,called_inn,calling_number,calling_ton,calling_npi,calling_ni,calling_restrict,calling_screened,calling_category,cause_standard,cause_itu_class,cause_itu_cause,event_num,source_ip,source_port,destination_ip,destination_port,correlation_id,proto,family,type,node");

/* web rtc */
define('WEBRTC_FIELDS_CAPTURE', "id, date, floor(micro_ts /1000) as milli_ts, micro_ts,session_id as callid, '' as callid_aleg, caller as from_user, callee as ruri_user, method,source_ip,source_port,destination_ip,destination_port,correlation_id,proto,family,type,node");

/* can be file or db */
define('PROFILE_STORE','db');
define('PROFILE_PARAM', ROOT.'/store/profile');

/* can be file or db */
define('DASHBOARD_STORE','db');
define('DASHBOARD_PARAM', ROOT.'/store/dashboard');

/* PUBLIC HOST FOR SHARE - LEAVE EMPTY IF SAME AS HOMER UI */
define('PUBLIC_SHARE_HOST',"");

/* LDAP SETTINGS */

/*
define('LDAP_HOST',"localhost");
define('LDAP_PORT',NULL);
define('LDAP_BASEDN',"dc=example,dc=com");
define('LDAP_REALM',"My Realm");
define('LDAP_USERNAME_ATTRIBUTE_OPEN',"uid=");
define('LDAP_USERNAME_ATTRIBUTE_CLOSE',"");
define('LDAP_USERLEVEL',3);
define('LDAP_UID',"uidnumber");
define('LDAP_USERNAME',"uid");
define('LDAP_GID',"gidnumber");
define('LDAP_FIRSTNAME',"givenname");
define('LDAP_LASTNAME',"sn");
define('LDAP_EMAIL',"mail");
define('LDAP_GROUPDN',true)
define('LDAP_GROUP_USER','uid')
define('LDAP_GROUP_ARRAY',false)
define('LDAP_GROUP_ATTRIBUTE','memberUid')
*/

/* external pcap storage. I.e can be cloudshark, dropbox or google drive */
define('CLOUD_STORAGE', 0);
define('CLOUD_STORAGE_API', "");
define('CLOUD_STORAGE_URI', "https://www.cloudshark.org");


define('REMOTE_LOG', 0);
define('REMOTE_LOG_INDEX', "homer");
define('REMOTE_LOG_URL', "http://10.0.0.1:9200");
define('REMOTE_LOG_DOC_TYPE', "sipcapture");
define('REMOTE_LOG_USERNAME', "root");
define('REMOTE_LOG_PASSWORD', "root"); 

/*********[EXTERNAL AUTH ]********/
define('EXTERNAL_AUTH_URI', "http://10.0.0.1/api/request");
define('EXTERNAL_AUTH_METHOD', "GET");
define('EXTERNAL_AUTH_PARAM', "param=[KEY]");
define('EXTERNAL_AUTH_USERNAME', "test");
define('EXTERNAL_AUTH_PASSWORD', "test");
define('EXTERNAL_AUTH_REQUEST_TYPE', "cookie");
define('EXTERNAL_AUTH_REQUEST_KEY', "extenalid");
define('EXTERNAL_AUTH_POSITIVE_REPLY', "200");
define('EXTERNAL_AUTH_REPLY_USER_INFO', "json");

/*** ARCHIVE DB ***/
define('ARCHIVE_DATABASE', "archive_homer_data");

/* number normalization */
define('NORMALIZE_NUMBER', 0);
define('MY_COUNTRY_CODE', '49');

/*syslog*/
define('SYSLOG_ENABLE', 0);
define('SYSLOG_LEVEL', 'ERROR');

define('API_AUTH_KEY', 0);
define('API_AUTH_KEY_TYPE', "json");
define('API_AUTH_KEY_NAME', "authkey");

/*Share*/
define('SHARE_MESSAGES', 0);

?>
