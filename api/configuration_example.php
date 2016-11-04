<?php

if(!defined('HOMER_CONFIGURATION')):
define('HOMER_CONFIGURATION', 1);

/*********************************************************************************/
/* AUTH DB homer. User and Configuration */
define('DB_HOSTNAME', "localhost");
define('DB_PORT', 3306);
define('DB_USERNAME', "homer_user");
define('DB_PASSWORD', "homer_password");
define('DB_CONFIGURATION', "homer_configuration");
define('DB_STATISTIC', "homer_statistic");
define('DB_HOMER', "homer_data");
define('SINGLE_NODE', 1);
define('DATABASE_DRIVER',"mysql");

/*********************************************************************************/

/* webHomer Settings 
*  Adjust to reflect your system preferences
*/

define('PCAPDIR', ROOT."/tmp/");
define('WEBPCAPLOC',"/tmp/");

/* Tshark settings for ISUP analyse */
define('TSHARK_ENABLED',1);
define('TSHARK_PATH','/usr/bin/tshark');
define('EGREP','/bin/egrep');

/* INCLUDE preferences */

include_once("preferences.php");

endif;

?>
