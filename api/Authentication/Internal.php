<?php
/*
 * HOMER API
 * Homer's internal auth
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

class Internal extends Authentication {
	
	private $encrypt = true;
	private $group_column = "gid";
	private $id_column = "uid";
	private $pass_column = "password";
	private $user_column = "username";
	private $user_table = "user";
	private $user_level = "userlevel"; 
	private $db;
	protected $_instance = array();	    

	function __construct($utable = NULL)
	{
		if($utable != NULL) $this->user_table = $utable;
        }

        /*
	public function getContainer()
	{
        	if ($this->_instance === null) { 
	            // extract name of the storage class
        	    $containerClass = "Database\\".DATABASE_CONNECTOR;
	            $this->_instance = new $containerClass(1, DB_HOSTNAME, DB_PORT, DB_CONFIGURATION, DB_USERNAME, DB_PASSWORD);
        	}
        	
	        return $this->_instance;
	}
	*/

	public function getContainer($name)
	{
        	if (!$this->_instance || !isset($this->_instance[$name]) || $this->_instance[$name] === null) {
			//$config = \Config::factory('configs/config.ini', APPLICATION_ENV, 'auth');
			if($name == "db") $containerClass = sprintf("Database\\".DATABASE_CONNECTOR);
			else if($name == "layer") $containerClass = sprintf("Database\\Layer\\".DATABASE_DRIVER);
			$this->_instance[$name] = new $containerClass();
	        }

	        return $this->_instance[$name];
	}


	function logIn($param) {

           $mydb = $this->getContainer('db');
           $mydb->select_db(DB_CONFIGURATION);
           $mydb->dbconnect();
                                
           /* get our DB Abstract Layer */
           $layer = $this->getContainer('layer');
                               
           $query  = $mydb->makeQuery("SELECT * FROM ".$layer->getTableName($this->user_table)." WHERE ".$this->user_column."='?' AND ".$this->pass_column." = ".$layer->getPassword($param['password'], $this->pass_column).";" , $param['username']);
           $rows = $mydb->loadObjectList($query);

           if(count($rows) > 0) {
                $row = $rows[0]; 
                $_SESSION['loggedin']   = $row->uid;
                $_SESSION['uid']        = $row->uid;
                $_SESSION['username']   = $param['username'];
                $_SESSION['gid']        = $row->gid;
                $_SESSION['grp']        = $row->grp;
                
                
                //update lastvisit
                $query = "UPDATE ".$layer->getTableName($this->user_table)." SET lastvisit = NOW() WHERE ".$this->id_column."='".$row->uid."'";
                $mydb->executeQuery($query);

                $user['uid']     = $row->uid;
                $user['username'] = $row->username;
                $user['gid']        = $row->gid;   
                $user['grp']      = $row->grp;   
                $user['firstname']  = $row->firstname;
                $user['lastname']   = $row->lastname; 
                $user['email']      = $row->email;    
                $user['lastvisit']  = $row->lastvisit;

                return $user;
                  
           } else{
               $_SESSION['loggedin'] = "-1";
               return array();
           }
        }
        
        function checkKey($authkey, $ip) {

           $mydb = $this->getContainer('db');
           $mydb->select_db(DB_CONFIGURATION);
           $mydb->dbconnect();
                                
           /* get our DB Abstract Layer */
           $layer = $this->getContainer('layer');
                               
           $query  = $mydb->makeQuery("SELECT id, userobject FROM api_auth_key WHERE authkey = '?' AND NOW() BETWEEN `startdate` AND `stopdate` AND (source_ip = '0.0.0.0' OR source_ip = '?') AND enable = 1;" , $authkey, $ip);
           $rows = $mydb->loadObjectList($query);

           if(count($rows) > 0) {
           	$data = $rows[0];           
                $uobj = json_decode($data->userobject, true);
                
                $_SESSION['loggedin']   = $uobj['uid'];
                $_SESSION['uid']        = $uobj['uid'];
                $_SESSION['username']   = $uobj['username'];
                $_SESSION['gid']        = $uobj['gid'];
                $_SESSION['grp']        = $uobj['grp'];
                                
                $query = "UPDATE userobject SET lastvisit = NOW() WHERE id=".$data->id;
                $mydb->executeQuery($query);

                $user['uid']     = $uobj['uid'];
                $user['username'] = $uobj['username'];
                $user['gid']        = $uobj['gid'];   
                $user['grp']      = $uobj['grp'];   
                $user['firstname']  = $uobj['firstname'];
                $user['lastname']   = $uobj['lastname']; 
                $user['email']      = $uobj['email'];    
                $user['lastvisit']  = $uobj['lastvisit'];

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
		if($_SESSION['loggedin'] == "-1") return false;        
	        return true;
	}
	
	function checkAdmin () {
		if(preg_match('/admins/',$_SESSION['grp'])) return true;
	        else return false;
	}
	
	function getUser() {

           if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
           if($_SESSION['loggedin'] == "-1") return array();

           $mydb = $this->getContainer('db');
           $mydb->select_db(DB_CONFIGURATION);
           $mydb->dbconnect();
                                
           /* get our DB Abstract Layer */
           $layer = $this->getContainer('layer');

           $query  = $mydb->makeQuery("SELECT uid, gid, username, `grp`, firstname, lastname, email, lastvisit,department  FROM ".$layer->getTableName($this->user_table)." WHERE ".$this->id_column." = ? limit 1;", $_SESSION['loggedin']);
           $rows = $mydb->loadObjectList($query);
           
           if(count($rows)) {
                $row = $rows[0];
                $user['uid']     = $row->uid;
                $user['username'] = $row->username;
                $user['gid']        = $row->gid;
                $user['grp']      = $row->grp;
                $user['firstname']  = $row->firstname;
                $user['lastname']   = $row->lastname;
                $user['email']      = $row->email;
                $user['lastvisit']  = $row->lastvisit;
                $user['department']  = $row->department;
                
                return $user;
           }                                
           else return array();
        }
        
        function updateUser($param) {

           if(!isset($_SESSION['loggedin'])) $_SESSION['loggedin'] = '-1';
           if($_SESSION['loggedin'] == "-1") return array();

           $mydb = $this->getContainer('db');
           $mydb->select_db(DB_CONFIGURATION);
           $mydb->dbconnect();
                                
           /* get our DB Abstract Layer */
           $layer = $this->getContainer('layer');

           $data = array();
	   $search = array();
	   $callwhere = array();  
	   $calldata = array();
         
	   $update['department'] = getVar('department', '', $param, 'string');
	   $update['email'] = getVar('email', '', $param, 'string');
	   $update['firstname'] = getVar('firstname', '', $param, 'string');
	   $update['lastname'] = getVar('lastname', '', $param, 'string');
	   $password = getVar('password', '', $param, 'string');
	   $uid = getVar('uid', 0, $param, 'int');
     
	   $exten = "";
	   $callwhere = generateWhere($update, 1, $mydb, 0);
	   if(count($callwhere)) {
                if(strlen($password) > 0) $exten = "`password` = ".$layer->setPassword($password).",";
                $exten .= implode(", ", $callwhere);
           }

	   $query = $mydb->makeQuery("UPDATE ".$layer->getTableName($this->user_table)." SET ".$exten. " WHERE ".$this->id_column." = ? limit 1;", $_SESSION['loggedin']);
	   $mydb->executeQuery($query);       	   
	   return $this->getUser();
        }

	
	//reset password
	function passwordReset($username, $user_table, $pass_column, $user_column){

		//generate new password
		$newpassword = $this->createPassword();		
		
		$mydb = $this->getContainer('db');
		$mydb->select_db(DB_CONFIGURATION);
		$mydb->dbconnect();
		
		//update database with new password
		$query = "UPDATE ".$layer->getTableName($this->user_table)." SET ".$this->pass_column."=".$layer->setPassword($newpassword)." WHERE ".$this->user_column."='".stripslashes($username)."'";
		
		if(!$mydb->executeQuery($query)) {
			die("No update possible");		
			exit;		
		}
		
		$to = stripslashes($username);
		//some injection protection
		$illigals=array("n", "r","%0A","%0D","%0a","%0d","bcc:","Content-Type","BCC:","Bcc:","Cc:","CC:","TO:","To:","cc:","to:");
		$to = str_replace($illigals, "", $to);
		$getemail = explode("@",$to);
		
		//send only if there is one email
		if(sizeof($getemail) > 2){
			return false;	
		}else{
			//send email
			$from = $_SERVER['SERVER_NAME'];
			$subject = "Password Reset: ".$_SERVER['SERVER_NAME'];
			$msg = "<p>Your new password is: ".$newpassword."</p>";
			
			//now we need to set mail headers
			$headers = "MIME-Version: 1.0 rn" ;
			$headers .= "Content-Type: text/html; rn" ;
			$headers .= "From: $from  rn" ;
			
			//now we are ready to send mail
			$sent = mail($to, $subject, $msg, $headers);
			if($sent){
				return true; 
			}else{
				return false;	
			}
		}
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
