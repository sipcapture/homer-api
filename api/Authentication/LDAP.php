<?php
/*
 * HOMER API
 * Homer's LDAP auth
 *
 * Copyright (C) 2011-2014 Alexandr Dubovikov <alexandr.dubovikov@gmail.com>
 * Copyright (C) 2011-2012 Lorenzo Mangani <lorenzo.mangani@gmail.com>
 *
 * The Initial Developers of the Original Code are
 *
 * Alexandr Dubovikov <alexandr.dubovikov@gmail.com>
 * Lorenzo Mangani <lorenzo.mangani@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
*/

namespace Authentication;

defined( '_HOMEREXEC' ) or die( 'Restricted access' );

class LDAP extends Authentication {

    private $_instance = null;

    function __construct($utable = NULL)
    {
        if($utable != NULL) $this->user_table = $utable;
    }

    public function getContainer()
    {
        if ($this->_instance === null) {
            // extract name of the storage class
        }

        return $this->_instance;
    }

    function logIn($param) {

        $ds=ldap_connect(LDAP_HOST,LDAP_PORT);

        $_SESSION['loggedin'] = "-1";

        // Set LDAP Version, Default is Version 2
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, ( LDAP_VERSION) ? LDAP_VERSION : 2);
        // Referrals are disabled
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0 );

        // Enable TLS Encryption
        if(LDAP_ENCRYPTION == "tls") {

            // Documentation says - set to never
            putenv('LDAPTLS_REQCERT=never') or die('Failed to setup the env');
            ldap_start_tls($ds);
        }

        if (defined('LDAP_BIND_USER') && defined('LDAP_BIND_PASSWORD')) {
            if (!ldap_bind( $ds, LDAP_BIND_USER, LDAP_BIND_PASSWORD)) {
                return array();
            }
        }

        $r=ldap_search( $ds, LDAP_BASEDN, LDAP_USERNAME_ATTRIBUTE."=".$param['username']);
        if ($r) {
            $result = ldap_get_entries( $ds, $r);

            if ($result[0]) {
                if (ldap_bind( $ds, $result[0]['dn'], $param['password']) ) {
                    if($result[0] != NULL) {
                         if (!$this->check_filegroup_membership($ds, (defined("LDAP_GROUP_ARRAY") && LDAP_GROUP_ARRAY) ? $result[0][LDAP_GROUP_USER][0] : $result[0][LDAP_USERNAME_ATTRIBUTE])) {
                                return false;
                            }
                        
                        if(array_key_exists(LDAP_UID, $result[0])) $user['uid'] =  $result[0][LDAP_UID][0];
                        else $user['uid'] =  base_convert($param['username'], 16, 10);                        
                        
                        if(array_key_exists(LDAP_GID, $result[0])) $user['gid'] = $result[0][LDAP_GID][0];
                        else $user['gid'] = 10;
                                                
                        if(array_key_exists(LDAP_FIRSTNAME, $result[0])) $user['firstname']  = $result[0][LDAP_FIRSTNAME][0];
                        else $user['firstname']  = $param['username'];
                        
                        if(array_key_exists(LDAP_LASTNAME, $result[0]))  $user['lastname']   = $result[0][LDAP_LASTNAME][0];
                        else $user['lastname']   = $param['username'];
                        
                        if(array_key_exists(LDAP_EMAIL, $result[0])) $user['email']      = $result[0][LDAP_EMAIL][0];
                        else $user['email'] = "no@exist.com";
                        
                        $user['username'] = $param['username'];
                        $user['grp']      = "users";
                        $user['lastvisit']  = date('c');                        
                        $_SESSION['uid'] = $user['uid'];
                        $_SESSION['loggedin'] = $user['username'];
                        $_SESSION['userlevel'] = LDAP_USERLEVEL;
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['gid'] = $user['gid'];
                        $_SESSION['grp'] = "users";
                        
                        $_SESSION['data'] = $user;
                        
                        

                        // Assigne Admin Privs, should be read from the LDAP Directory in the future
                        #$ADMIN_USER = split(",", LDAP_ADMIN_USER);
                        foreach($ADMIN_USER as &$value) {

                            if ($value == $param['username']) {
                                $_SESSION['userlevel'] = 1; # LDAP_ADMINLEVEL;
				$user['grp'] = "users,admins";
				$_SESSION["grp"] = "users,admins";
                            }
                        }
                        return $user;
                    }
                }
            }
        }

        return array();
    }

    /* posixGroup schema, rfc2307 */
    function check_filegroup_membership($ds, $uid) {
	    foreach (LDAP_GROUPS as $ldap_group){
		$dn = "cn=".$ldap_group.",".LDAP_GROUP_BASE;
		$attr = LDAP_GROUP_ATTRIBUTE;
		foreach ($uid as $ldap_user){
			$result = ldap_compare($ds, $dn, $attr, $ldap_user);
		}
	       	if ($result === true) return true;
		else return false;
		
	    }
    }

    //logout function
    function logOut(){
        $_SESSION['loggedin'] = '-1';
        session_destroy();

        return;
    }

    function checkSession () {
        if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
        if($_SESSION['loggedin'] == "-1") return false;
        return true;
    }

    function checkAdmin () {
        if(preg_match('/admins/',$_SESSION['grp'])) return true;
        else return false;
    }

    function updateUser($param) {
        $this->getUser();
    }

    function getUser() {

        if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
        if($_SESSION['loggedin'] == "-1") return array();

        return $_SESSION['data'];
    }


    //create random password with 8 alphanumerical characters

    function createPassword() {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        while ($i <= 7) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }
}

?>
