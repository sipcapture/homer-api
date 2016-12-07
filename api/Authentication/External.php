<?php
/*
 * HOMER API
 * Homer's external auth
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

class External extends Authentication {
	
	private $encrypt = true;
	private $group_column = "gid";
	private $id_column = "uid";
	private $pass_column = "password";
	private $user_column = "username";
	private $user_table = "user";
	private $user_level = "userlevel"; 
	private $db;
	private $_instance = null;

	function __construct($utable = NULL)
	{
		if($utable != NULL) $this->user_table = $utable;
        }

	function logIn($param) {

        	$key =  "";
		
        	if(EXTERNAL_AUTH_REQUEST_TYPE == "cookie") 
        	{
			$key = $_COOKIE[EXTERNAL_AUTH_REQUEST_KEY];
			$param = preg_replace('[KEY]', $key, EXTERNAL_AUTH_PARAM);
        	}
        	else if(EXTERNAL_AUTH_REQUEST_TYPE == "get") 
        	{
			$key = $_GET[EXTERNAL_AUTH_REQUEST_KEY];
			$param = preg_replace('[KEY]', $key, EXTERNAL_AUTH_PARAM);
        	}
		else if(EXTERNAL_AUTH_REQUEST_TYPE == "password") 
        	{
			$param = array_intersect_key($param, array_flip(array('username', 'password')));
        	} else {
			$param = "";
		}
        	       	
        	$ch = curl_init();
        	
        	if(EXTERNAL_AUTH_METHOD == "POST") 
        	{
			curl_setopt($ch, CURLOPT_POST, 1);
        		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        		curl_setopt($ch, CURLOPT_URL, EXTERNAL_AUTH_URI);
        		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");        	
        	}
        	else {        	        	
			
		        curl_setopt($ch, CURLOPT_URL, EXTERNAL_AUTH_URI."?".$param);
		        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		}
				
        	curl_setopt($ch, CURLOPT_HEADER, 0);
        	if(defined(EXTERNAL_AUTH_USERNAME) && EXTERNAL_AUTH_USERNAME  != "") 
        	{
		        curl_setopt($ch, CURLOPT_USERPWD, EXTERNAL_AUTH_USERNAME . ":" . EXTERNAL_AUTH_PASSWORD);
		}
		
        	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        	
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper(EXTERNAL_AUTH_METHOD));        	
	        $result = curl_exec($ch);
	        $info = curl_getinfo($ch);
        	curl_close($ch);
	        $data = json_decode($result);

	        if($info['http_code'] == EXTERNAL_AUTH_POSITIVE_REPLY && count($data) > 0)
	        {

        	        $_SESSION['loggedin']   = $data->uid;
	                $_SESSION['uid']        = $data->uid;
        	        $_SESSION['username']   = $data->username;
	                $_SESSION['gid']        = $data->gid;
	                $_SESSION['grp']        = $data->grp;
	                                                
	                $user['uid']     = $data->uid;
        	        $user['username'] = $data->username;
        	        $user['gid']        = $data->gid;   
	                $user['grp']      = $data->grp;   
        	        $user['firstname']  = $data->firstname;
        	        $user['lastname']   = $data->lastname; 
	                $user['email']      = $data->email;    
        	        $user['lastvisit']  = $data->lastvisit;
        	        
        	        $_SESSION['extra'] = $user;
        	        
	                return $user;
		                    
		} else{
	               $_SESSION['loggedin'] = "-1";
        	       return array();
	        }
        }
        
        //logout function 
        function logOut(){
                //$_SESSION['loggedin'] = '-1';
         	session_destroy();
         	$_SESSION = array(); 
                return;
	}

	function checkSession () {
	
        	if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
		if($_SESSION['loggedin'] == "-1") 
		{
			$user = $this->logIn($param);		
			if(empty($user)) return false;		
			else return true;        
		}
		
	        return true;
	}
	
	function checkAdmin () {
		if(preg_match('/admins/',$_SESSION['grp'])) return true;
	        else return false;
	}
	
	function getUser() {

           if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
           if($_SESSION['loggedin'] == "-1") return array();

           $user = $_SESSION['extra'];
           return $user;
        }

}
?>
