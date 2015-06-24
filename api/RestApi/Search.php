<?php
/*
 * HOMER API Engine
 *
 * Copyright (C) 2011-2015 Alexandr Dubovikov <alexandr.dubovikov@gmail.com>
 * Copyright (C) 2011-2015 Lorenzo Mangani <lorenzo.mangani@gmail.com> QXIP B.V.
 *
 * The Initial Developers of the Original Code are
 *
 * Alexandr Dubovikov <alexandr.dubovikov@gmail.com>
 * Lorenzo Mangani <lorenzo.mangani@gmail.com> QXIP B.V.
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

namespace RestApi;

class Search {
    
    protected $_instance = array();
   
    private $authmodule = true;

    function __construct()
    {

    }

    /**
    * Checks if a user is logged in.
    *
    * @return boolean
    */
    public function getLoggedIn(){

	$answer = array();

	if($this->authmodule == false) return $answer;

        if(!$this->getContainer('auth')->checkSession())
	{
		$answer['sid'] = session_id();
                $answer['auth'] = 'false';
                $answer['status'] = 403;
                $answer['message'] = 'bad session';
                $answer['data'] = array();
	}

	return $answer;
    }
    
    public function doSearchData($timestamp, $param){
    
	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;                

	$data = array();;

	$data = $this->doSearchMessagesData($timestamp, $param, false);

        $answer = array();

        if(empty($data)) {

                $answer['sid'] = session_id();
                $answer['auth'] = 'true';
                $answer['status'] = 200;
                $answer['message'] = 'no data';
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';
                $answer['message'] = 'ok';
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }

        return $answer;

                           
        $answer = array();          
                
        if(empty($data)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }
        
        return $answer;
    }
    
    
    public function getSearchData($raw_get_data){
    
	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;          
        
        /* get our DB */
        $db = $this->getContainer('db');
        
        $data = array();

        /* hack */
        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        $lnodes = $param['node'];                        
        $trans['call'] = getVar('call', false, $param["transaction"], 'bool');
        $trans['registration'] = getVar('registration', false, $param["transaction"], 'bool');
        $trans['rest'] = getVar('rest', false, $param["transaction"], 'bool');
                        
        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);        
        $limit_orig = getVar('limit', 100, $param, 'int');
        
        $callwhere = array();        
        if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";

	$nodes = array();
        if(SINGLE_NODE == 1) $nodes[] = array( "dbname" =>  DB_HOMER, "name" => "single", "name" => "single");
        else {
            foreach($lnodes as $lnd) $nodes[] = $this->getNode($lnd['name']);
        }

        foreach($nodes as $node)
        {
		$db->dbconnect_node($node);
		$limit = $limit_orig;

		for($ts = $time['from_ts']; $ts < $time['to_ts']; $ts+=86400) {        
    
			if($trans['call']) {
				if($limit < 1) break;
		                $order = " order by id DESC LIMIT ".$limit;
		                $table = "sip_capture_call_".gmdate("Ymd", $ts);            
			        $query = "SELECT ".FIELDS_CAPTURE.", 'call' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
		                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
		                $noderows = $db->loadObjectArray($query.$order);
                		$data = array_merge($data,$noderows);                
		                $limit -= count($noderows);
			}
            
			if($trans['registration']) 
			{                
				if($limit < 1) break;
				$order = " order by id DESC LIMIT ".$limit;
				$table = "sip_capture_registration_".gmdate("Ymd", $ts);            
				$query = "SELECT ".FIELDS_CAPTURE.", 'registration' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
				$noderows = $db->loadObjectArray($query.$order);     
				$data = array_merge($data,$noderows);                
				$limit -= count($noderows);
			}
            
			if($trans['rest']) 
			{    
				if($limit < 1) break;
				$order = " order by id DESC LIMIT ".$limit;
				$table = "sip_capture_rest_".gmdate("Ymd", $ts);            
				$query = "SELECT ".FIELDS_CAPTURE.", 'rest' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
				if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
				$noderows = $db->loadObjectArray($query.$order);                            
				$data = array_merge($data,$noderows);
				$limit -= count($noderows);
			}            
		}
	}

        /* sorting */
        usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));
                           
        $answer = array();          
                
        if(empty($data)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }
        
        return $answer;
    }

    public function doSearchMessagesData($timestamp, $param, $full = false){
    
        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();
        
        $data = array();        
        
        $trans['call'] = getVar('call', false, $param['transaction'], 'bool');
        $trans['registration'] = getVar('registration', false, $param['transaction'], 'bool');
        $trans['rest'] = getVar('rest', false, $param['transaction'], 'bool');

        $lnodes = $param['node'];
                        
        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');        
        $time['from_ts'] = floor($time['from']/1000);
        $time['to_ts'] = round($time['to']/1000);
        
        /* search fields */        
        $search['from_user'] = getVar('from_user', NULL, $param['search'], 'string');
        $search['to_user'] = getVar('to_user', NULL, $param['search'], 'string');
        $search['ruri_user'] = getVar('ruri_user', NULL, $param['search'], 'string');
        $search['callid'] = getVar('callid', NULL, $param['search'], 'string');
        $search['callid_aleg'] = getVar('callid_aleg', NULL, $param['search'], 'string');
        $search['contact_user'] = getVar('contact_user', NULL, $param['search'], 'string');
        $search['pid_user'] = getVar('pid_user', NULL, $param['search'], 'string');
        $search['auth_user'] = getVar('auth_user', NULL, $param['search'], 'string');
        $search['user_agent'] = getVar('user_agent', NULL, $param['search'], 'string');
        $search['method'] = getVar('method', NULL, $param['search'], 'string');
        $search['cseq'] = getVar('cseq', NULL, $param['search'], 'string');
        $search['reason'] = getVar('reason', NULL, $param['search'], 'string');        
        $search['msg'] = getVar('msg', NULL, $param['search'], 'string');
        $search['diversion'] = getVar('diversion', NULL, $param['search'], 'string');
        $search['via_1'] = getVar('via_1', NULL, $param['search'], 'string');
        $search['source_ip'] = getVar('source_ip', NULL, $param['search'], 'string');
        $search['source_port'] = getVar('source_port', NULL, $param['search'], 'string');
        $search['destination_ip'] = getVar('destination_ip', NULL, $param['search'], 'string');
        $search['destination_port'] = getVar('destination_port', NULL, $param['search'], 'string');        
        $search['node'] = getVar('node', NULL, $param['search'], 'string');
        $search['uniq'] = getVar('uniq', NULL, $param['search'], 'string');
        $search['proto'] = getVar('proto', NULL, $param['search'], 'string');
        $search['family'] = getVar('family', NULL, $param['search'], 'string');

        $and_or = getVar('orand', NULL, $param['search'], 'string');
        $b2b = getVar('b2b', false, $param['search'], 'bool');
        $limit_orig = getVar('limit', 100, $param, 'int');
      
        /* callid correlation */     
        
        $callwhere = array();                
        $callwhere = generateWhere($search, $and_or, $db, $b2b);
        
        $fields = FIELDS_CAPTURE;
        if($full) $fields.=", msg ";

        $nodes = array();
        if(SINGLE_NODE == 1) $nodes[] = array( "dbname" =>  DB_HOMER, "name" => "single");
        else {
            foreach($lnodes as $lnd) $nodes[] = $this->getNode($lnd['name']);
        }

        foreach($nodes as $node)
        {        
            
	    $db->dbconnect_node($node);                            
            $limit = $limit_orig;

            for($ts = $time['from_ts']; $ts < $time['to_ts']; $ts+=86400) {
                         
                if($trans['call']) {

                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_call_".gmdate("Ymd", $ts);                            
                    $query = "SELECT ".$fields.", 'call' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                    $noderows = $db->loadObjectArray($query.$order);
                    $data = array_merge($data,$noderows);                
                    $limit -= count($noderows);
                }

                if($trans['registration']) 
                {                
                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_registration_".gmdate("Ymd", $ts);            
                    $query = "SELECT ".$fields.", 'registration' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                    $noderows = $db->loadObjectArray($query.$order);     
                    $data = array_merge($data,$noderows);                
                    $limit -= count($noderows);                
                }

                if($trans['rest']) 
                {    
                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_rest_".gmdate("Ymd", $ts);            
                    $query = "SELECT ".$fields.", 'rest' as trans, '".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                    $noderows = $db->loadObjectArray($query.$order);                     
                    $data = array_merge($data,$noderows);
                    $limit -= count($noderows);
                }            
            }
        }

        /* sorting */
        usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));
                           
        return $data;
    }

    public function doPcapExportData($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $data = $this->doSearchMessagesData($timestamp, $param, true);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 0);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    
    
    public function doTextExportData($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $data = $this->doSearchMessagesData($timestamp, $param, true);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 1);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    
    
    
    public function doSearchMethod($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;            
        /* get our DB */        
        
        $data = $this->getMessagesByMethod($timestamp, $param);                
        
        $answer = array();          
                
        if(empty($data)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }
        
        return $answer;
    }



    public function getMessagesByMethod($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;            
        
        /* get our DB */        
        $db = $this->getContainer('db');        
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();
                         
        $data = array();
        
        $record_id = getVar('id', 0, $param['search'], 'int');
        $callid = getVar('callid', "", $param['search'], 'string');
        $trans['call'] = getVar('call', false, $param['transaction'], 'bool');
        $trans['registration'] = getVar('registration', false, $param['transaction'], 'bool');
        $trans['rest'] = getVar('rest', false, $param['transaction'], 'bool');
		
	$location = $param['location'];
                        
        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');        
        $time['from_ts'] = floor($time['from']/1000);
        $time['to_ts'] = round($time['to']/1000);
                
        $utils['logic_or'] = getVar('logic', false, array_key_exists('query', $param) ? $param['query'] : array(), 'bool');
        $and_or = $utils['logic_or'] ? " OR " : " AND ";
                                                  
        $limit = 1;
        $search['id'] = $record_id;                         
        $callwhere = generateWhere($search, $and_or, $db, 0);
        
        
        $nodes = array();
        if(SINGLE_NODE == 1) $nodes[] = array( "dbname" =>  DB_HOMER);
        else {
            $nodes[] = $this->getNode($location['node']);        
        }                
        
        foreach($nodes as $node)
        {        
            $ts = $time['from_ts']; 
            
            $db->dbconnect_node($node);
            
            if($trans['call']) {

                $order = " LIMIT ".$limit;
                $table = "sip_capture_call_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'call' as trans FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                $noderows = $db->loadObjectArray($query.$order);
                $data = array_merge($data,$noderows);                
                $limit -= count($noderows);
            }

            if($trans['registration']) 
            {                
                $order = " LIMIT ".$limit;
                $table = "sip_capture_registration_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'registration' as trans  FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                $noderows = $db->loadObjectArray($query.$order);                                                          
                $data = array_merge($data,$noderows);                
            }
        

            if($trans['rest']) 
            {    
                $order = " LIMIT ".$limit;
                $table = "sip_capture_rest_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'rest' as trans FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";
                $noderows = $db->loadObjectArray($query.$order);                     
                $data = array_merge($data,$noderows);
            }                                    
        }
                
        return $data;
    }
    
    
    public function doSearchMessage($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
    
        /* get our DB */
        $db = $this->getContainer('db');
        
        $node = array( "dbname" =>  DB_HOMER);        
        if(SINGLE_NODE == 1) $node = array( "dbname" =>  DB_HOMER);
        $db->dbconnect_node($node);
        
        $data = array();
        $search = array();
        $callwhere = array();        
        
        $trans['call'] = getVar('call', false, $param['transaction'], 'bool');
        $trans['registration'] = getVar('registration', false, $param['transaction'], 'bool');
        $trans['rest'] = getVar('rest', false, $param['transaction'], 'bool');
                        
        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');        
        $time['from_ts'] = floor($time['from']/1000);
        $time['to_ts'] = round($time['to']/1000);
        
        $limit = getVar('limit', 100, $param, 'int');

        $record_id = getVar('id', 0, $param['search'], 'int');
        $search['callid'] = getVar('callid', "", $param['search'], 'string');        
        $b2b = getVar('b2b', false, $param['search'], 'bool');
         
        $utils['logic_or'] = getVar('logic', false, array_key_exists('query', $param) ? $param['query'] : array(), 'bool');
        $and_or = $utils['logic_or'] ? " OR " : " AND ";
                                    
        $callwhere = generateWhere($search, $and_or, $db, $b2b);
        
        //if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";        
        
        $ts = $time['from_ts'];         

        for($ts = $time['from_ts']; $ts < $time['to_ts']; $ts+=86400) {
                         
            if($trans['call']) {

                if($limit < 1) break;
                $order = " order by id DESC LIMIT ".$limit;
                $table = "sip_capture_call_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'call' as trans FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                 
                $noderows = $db->loadObjectArray($query.$order);
                $data = array_merge($data,$noderows);                
                $limit -= count($noderows);
            }

            if($trans['registration']) 
            {                
                if($limit < 1) break;
                $order = " order by id DESC LIMIT ".$limit;
                $table = "sip_capture_registration_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'registration' as trans FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                 
                $noderows = $db->loadObjectArray($query.$order);                                                          
                $data = array_merge($data,$noderows);                
                $limit -= count($noderows);                
            }

                        
            if($trans['rest']) 
            {    
                if($limit < 1) break;
                $order = " order by id DESC LIMIT ".$limit;
                $table = "sip_capture_rest_".gmdate("Ymd", $ts);            
                $query = "SELECT *, 'rest' as trans FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                 
                $noderows = $db->loadObjectArray($query.$order);                     
                $data = array_merge($data,$noderows);
                $limit -= count($noderows);
            }            
        }

        /* sorting */
        usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));
                           
        $answer = array();          
                
        if(empty($data)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $data;
                $answer['count'] = count($data);
        }
        
        return $answer;
    }

    public function getMessagesForTransaction($timestamp, $param) {
    
        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();
        
        $trans = array();
        $data = array();
        
        $trans['call'] = getVar('call', false, $param['transaction'], 'bool');
        $trans['registration'] = getVar('registration', false, $param['transaction'], 'bool');
        $trans['rest'] = getVar('rest', false, $param['transaction'], 'bool');
        
        $location = $param['location'];
                        
        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');        
        $time['from_ts'] = floor($time['from']/1000);
        $time['to_ts'] = round($time['to']/1000);
        
        $limit_orig = getVar('limit', 100, $param, 'int');
        
        $record_id = getVar('id', 0, $param['search'], 'int');
        $callids = getVar('callid', array(), $param['search'], 'array');        
        $b2b = getVar('b2b', false, $param['search'], 'bool');
        
        $callwhere = array();        

        $utils['logic_or'] = getVar('logic', false, array_key_exists('query', $param) ? $param['query'] : array(), 'bool');
        $and_or = $utils['logic_or'] ? " OR " : " AND ";

        $search = array();        
        /* make array */                           
        $search['callid'] = implode(";", $callids);
        $callwhere = generateWhere($search, $and_or, $db, $b2b);
        
        $nodes = array();
        if(SINGLE_NODE == 1) $nodes[] = array( "dbname" =>  DB_HOMER, "name" => "single");
        else {
            foreach($lnodes as $lnd) $nodes[] = $this->getNode($lnd['name']);
        }
        
        foreach($nodes as $node)
        {                
        
            $db->dbconnect_node($node);
            $limit = $limit_orig;               
            $ts = $time['from_ts']; 
                    
            for($ts = $time['from_ts']; $ts < $time['to_ts']; $ts+=86400) {
        
                if($trans['call']) {
                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_call_".gmdate("Ymd", $ts);                            
                    $query = "SELECT *, 'call' as trans,'".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";                
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                 
                    $noderows = $db->loadObjectArray($query.$order);
                    $data = array_merge($data,$noderows);                
                    $limit -= count($noderows);
                }

                if($trans['registration']) 
                {                
                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_registration_".gmdate("Ymd", $ts);            
                    $query = "SELECT *, 'registration' as trans,'".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                                 
                    $noderows = $db->loadObjectArray($query.$order);                                                          
                    $data = array_merge($data,$noderows);                
                    $limit -= count($noderows);                
                }
                        
                if($trans['rest']) 
                {    
                    if($limit < 1) break;
                    $order = " order by id DESC LIMIT ".$limit;
                    $table = "sip_capture_rest_".gmdate("Ymd", $ts);            
                    $query = "SELECT *, 'rest' as trans,'".$node['name']."' as dbnode FROM ".$table." WHERE (`date` BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                    if(count($callwhere)) $query .= " AND ( " .implode(" AND ", $callwhere). ")";                 
                    $noderows = $db->loadObjectArray($query.$order);                     
                    $data = array_merge($data,$noderows);
                    $limit -= count($noderows);
                }            
            }
        }

        /* sorting */
        usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));
    
        return $data;
    }        

    public function doSearchTransaction($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $answer = array();          

        $hostcount = 0;
        $disconnect = 0;
        $inv_cseq = "";
        
        $hosts = array();
        $info = array();
        $uac = array();
        $min_ts = 0;
        $max_ts = 0;
        $statuscall = 1;
        $rtpinfo = array();
        $localdata = array();
           
        $data =  $this->getMessagesForTransaction($timestamp, $param);           
           
        foreach($data as $row) {   
                $localdata[] = $this->getSIPCflow((object) $row, $hosts, $info, $uac, $hostcount, $rtpinfo, false);
                if(!$min_ts) $min_ts = $row['micro_ts'];                
        }

	if(!$max_ts) {
		$max_ts_tmp = end($data);
		$max_ts = $max_ts_tmp['micro_ts'];
	}
        //if(!$max_ts) $max_ts = end($data)['micro_ts'];

        /* calculate total duration if it set */

        $totdur = gmdate("H:i:s", intval(($max_ts - $min_ts) / 1000000));

	$info['totdur'] = $totdur;	   
	$info['statuscall'] = $statuscall;
	$info['callid'] = array();		   

	$reply['info']=$info;
	$reply['hosts']=$hosts;
	$reply['uac']=$uac;
	$reply['rtpinfo']=$rtpinfo;
	$reply['calldata'] = $localdata;
	$reply['count'] = count($localdata);
           
        if(empty($localdata)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $reply;
                $answer['count'] = count($reply);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $reply;
                $answer['count'] = count($reply);
        }
        
        return $answer;
    }
    
    public function doSearchShareTransaction($param){
    
        $answer = array();          
        $hostcount = 0;
        $disconnect = 0;
        $inv_cseq = "";
        $hosts = array();
        $info = array();
        $uac = array();
        $min_ts = 0;
        $max_ts = 0;
        $statuscall = 1;
        $rtpinfo = array();
        $localdata = array();
        
        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();                            
        
        $uuid = getVar('transaction_id', "", $param, 'string');
                                
        $query = "SELECT data FROM link_share WHERE uuid='?' limit 1";
        $query  = $db->makeQuery($query, $uuid );
        $json = $db->loadObjectArray($query);
        
        if(!empty($json)) {
                                                 
            $djson = json_decode($json[0]['data'], true);             

            $timestamp = $djson['timestamp'];
            $param = $djson['param'];
                                         
            $data =  $this->getMessagesForTransaction($timestamp, $param);           
               
            foreach($data as $row) {   
                    $localdata[] = $this->getSIPCflow((object) $row, $hosts, $info, $uac, $hostcount, $rtpinfo, true);
                    if(!$min_ts) $min_ts = $row['micro_ts'];                
            }
            
            if(!$max_ts) {
		$max_ts_tmp = end($data);
		$max_ts = $max_ts_tmp['micro_ts'];
	    }

            /* calculate total duration if it set */

            $totdur = gmdate("H:i:s", intval(($max_ts - $min_ts) / 1000000));

            $info['totdur'] = $totdur;	   
            $info['statuscall'] = $statuscall;
            $info['callid'] = array();		   
        }

	$reply['info']=$info;
	$reply['hosts']=$hosts;
	$reply['uac']=$uac;
	$reply['rtpinfo']=$rtpinfo;
	$reply['calldata'] = $localdata;
	$reply['count'] = count($localdata);
           
        if(empty($localdata)) {
        
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['status'] = 200;                
                $answer['message'] = 'no data';                             
                $answer['data'] = $reply;
                $answer['count'] = count($reply);
        }                
        else {
                $answer['status'] = 200;
                $answer['sid'] = session_id();
                $answer['auth'] = 'true';             
                $answer['message'] = 'ok';                             
                $answer['data'] = $reply;
                $answer['count'] = count($reply);
        }
        
        return $answer;
    }
    
    
    function getSIPCflow($data, &$hosts, &$info, &$uac, &$hostcount, &$rtpinfo, $message_include) {
    
 		$calldata = array();
 		$arrow_step=1;
 		$host_step=1; 		                 

		$IPv6 = (strpos($data->source_ip, '::') === 0);

		// compress IPv6 addresses for UI
		if ($IPv6) {
		        $data->source_ip = inet_ntop(inet_pton($data->source_ip));
		        $data->destination_ip = inet_ntop(inet_pton($data->destination_ip));
		}

		if(preg_match('/[4-6][0-9][0-9]/',$data->method)) $statuscall = 1;
		else if($data->method == "CANCEL") $statuscall = 2;
		else if($data->method == "BYE") $statuscall = 3;
		else if($data->method == "200" && preg_match('/INVITE/',$data->cseq)) $statuscall = 4;
		else if(preg_match('/[3][0-9][0-9]/',$data->method)) $statuscall = 5;
		
		$calldata['id'] = $data->id;
		$calldata['method'] = $data->method;
		$calldata['src_port'] = $data->source_port;
		$calldata['dst_port'] = $data->destination_port;		
		$calldata['trans'] = $data->trans;
		$calldata['callid'] = $data->callid;
		$calldata['node'] = $data->node;
		$calldata['dbnode'] = $data->dbnode;
		$calldata['micro_ts'] = $data->micro_ts;
		$calldata['ruri_user'] = $data->ruri_user;
				
		if($message_include) {
		    $calldata['msg'] = $data->msg;
		    $calldata['source_ip'] = $data->source_ip;
		    $calldata['destination_ip'] = $data->destination_ip;
		}

		if(!array_key_exists('callid', $info)) $info['callid'] = array();

		//array_push($info['callid'], $data->callid);
		if(!in_array($data->callid, $info['callid'])) 
		{
		        array_push($info['callid'], $data->callid);
                }
  
		if (CFLOW_HPORT == true) {
		 
                        $src_id = $data->source_ip.":".$data->source_port;
                        $dst_id = $data->destination_ip.":".$data->destination_port;
		
		        if(!isset($hosts[$src_id])) { $hosts[$src_id] = $hostcount; $hostcount+=$host_step; }                        
		        if(!isset($hosts[$dst_id])) { $hosts[$dst_id] = $hostcount; $hostcount+=$host_step; }
		             		             		            		        
		        $ssrc = ":".$data->source_port;
		        
		} else {
		
		        $src_id = $data->source_ip;
		        $dst_id = $data->destination_ip;
		        
		        if(!isset($hosts[$src_id])) { $hosts[$src_id] = $hostcount; $hostcount+=$host_step;}
		        if(!isset($hosts[$dst_id])) { $hosts[$dst_id] = $hostcount; $hostcount+=$host_step;}
                      
		        $ssrc = "";		        
		}

		$calldata['src_id'] = $src_id;		
		$calldata['dst_id'] = $dst_id;
  
		/* RTP INFO */
		if(preg_match('/=/',$data->rtp_stat)) {
  
			$tmparray = array();
			$newArray = array();   

			if(substr_count($data->rtp_stat, ";") > substr_count($data->rtp_stat, ",")) $tmparray = preg_split('/\;/', $data->rtp_stat);
			else $tmparray = preg_split('/\,/', $data->rtp_stat);
        
			$newArray = array();
			$newArray['method']=$data->method;
			$newArray['id']=$data->id;
			$newArray['callid']=$data->callid;
			$newArray['date']=$data->date;
			$newArray['source'] = $data->source_ip.":".$data->source_port;
        
                        $reportArray = array();
			foreach ($tmparray as $lineNum => $line) {
        	        	list($key, $value) = explode("=", $line);
	        	        $reportArray[$key] = $value;
	        	}                       
	        	
	        	$newArray['report'] = $reportArray;
	        	
		        $rtpinfo[] = $newArray;
		}  

		// SIP SWITCHES

		if(preg_match('/asterisk/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "asterisk", "agent" => $data->user_agent);
		}
		else if(preg_match('/FreeSWITCH/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "freeswitch", "agent" => $data->user_agent);
		}
		else if(preg_match('/kamailio|openser|opensip|sip-router/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "openser", "agent" => $data->user_agent);
		}
		else if(preg_match('/softx/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "sipgateway", "agent" => $data->user_agent);
		}
		else if(preg_match('/sipXecs/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "sipxecs", "agent" => $data->user_agent);
		}

		// SIP ENDPOINTS

		else if(preg_match('/x-lite|Bria|counter-path/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "counterpath", "agent" => $data->user_agent);
		}
		else if(preg_match('/WG4k/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "worldgate", "agent" => $data->user_agent);
		}
		else if(preg_match('/Eki/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "ekiga", "agent" => $data->user_agent);
		}
		else if(preg_match('/snom/i', $data->user_agent)) {
		     $uac[$src_id] = array("image" => "snom", "agent" => $data->user_agent);
		}
		else {
		      $uac[$src_id] = array("image" => "sipgateway", "agent" => $data->user_agent);
		}

		//$timestamp = floor($data->micro_ts / 1000000);
		//$milliseconds = round( $data->micro_ts  - ($timestamp * 1000000) );
		//$tstamp =  date("Y-m-d H:i:s.".$milliseconds." T",$data->micro_ts / 1000000);
		$calldata['milli_ts'] = floor($data->micro_ts / 1000);
		$method_text = $data->method." ".$data->reply_reason;
		if(strlen($method_text) > 15) $method_text = substr($data->method." ".$data->reply_reason, 0, 22)."...";   

		//SDP ?
		$val = "content_type";
		if(preg_match('/sdp/i', $data->content_type)) $method_text .= " (SDP) ";		
		if(preg_match('/[0-9A-Za-z_-]/i', $data->auth_user)) $method_text .= " (AUTH)";		
		$calldata["method_text"] = $method_text;

		// MSG Temperature
		if(preg_match('/^40|50/', $method_text )) $msgcol = "red";
		else if(preg_match('/^30|SUBSCRIBE|OPTIONS|NOTIFY/', $method_text)) $msgcol = "purple";
		else if(preg_match('/^20/', $method_text)) $msgcol = "green";
		else if(preg_match('/^INVITE/', $method_text)) $msgcol = 'blue';
		else $msgcol = 'blue';
		$calldata["msg_color"] = $msgcol;
		
		/*IF */
		if($hosts[$src_id] > $hosts[$dst_id]) $calldata["destination"] = 2;
		else $calldata["destination"] = 1;		

		return $calldata;		
    }
    
    public function doPcapExport($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $data =  $this->getMessagesForTransaction($timestamp, $param);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 0);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    
    
    public function doTextExport($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $data =  $this->getMessagesForTransaction($timestamp, $param);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 1);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    

    public function doPcapExportById($param){
    
        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();                            
        
        $uuid = getVar('transaction_id', "", $param, 'string');
                                
        $query = "SELECT data FROM link_share WHERE uuid='?' limit 1";
        $query  = $db->makeQuery($query, $uuid );
        $json = $db->loadObjectArray($query);
         
        $djson = json_decode($json[0]['data'], true);             

        $timestamp = $djson['timestamp'];
        $param = $djson['param'];
                                                 
                           
        $data =  $this->getMessagesForTransaction($timestamp, $param);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 0);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    
    
    public function doTextExportById($param){
    

        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();                            
        
        $uuid = getVar('transaction_id', "", $param, 'string');
                                
        $query = "SELECT data FROM link_share WHERE uuid='?' limit 1";
        $query  = $db->makeQuery($query, $uuid );
        $json = $db->loadObjectArray($query);
         
        $djson = json_decode($json[0]['data'], true);             

        $timestamp = $djson['timestamp'];
        $param = $djson['param'];
                           
        $data =  $this->getMessagesForTransaction($timestamp, $param);           

        list($pcapfile, $fsize, $buf) = $this->generateHomerTextPCAP($data, 1);
           
        sendFile(200, "OK", $pcapfile, $fsize, $buf);
        
        return;        
    }    



    function generateHomerTextPCAP($results, $text) {

           date_default_timezone_set(HOMER_TIMEZONE);

           if(!count($results)) return array();

           /* GENERATE PCAP */	              
	   $size = array(
	         ethernet=>14,
	         ip => 20,
	         ip6 => 20,
	         udp => 8,
	         tcp => 20,
	         data => 0,
	         total => 0
           );
	   
	   //Write PCAP HEADER
	   $pcaphdr = array(
	       magic => 2712847316,
	       version_major => 2,
	       version_minor => 4,
	       thiszone => 0,
	       sigfigs => 0,
	       snaplen => 102400,
	       network => 1
           );
	
	   $buf="";
	   $pcap_packet = pack("lssllll", $pcaphdr['magic'], $pcaphdr['version_major'], $pcaphdr['version_minor'], $pcaphdr['thiszone'], $pcaphdr['sigfigs'], $pcaphdr['snaplen'], $pcaphdr['network']);

	   //Ethernet header
	   $eth_hdr = array( dest_mac => "020202020202", src_mac => "010101010101", type => "0800");           
	   $ethernet = pack("H12H12H4", $eth_hdr['dest_mac'], $eth_hdr['src_mac'], $eth_hdr['type']);

	   if(!$text) $buf=$pcap_packet;

           foreach($results as $result) {

 		$calldata = array();
		
		 $data=$result['msg'];
		 $size['data'] = strlen($data);
	         $ptk = '';

		 if($text) {

	             $sec = intval($result['micro_ts'] / 1000000);
        	     $usec = $result['micro_ts'] - ($sec*1000000);

	             $buf .= "U ".date("Y/m/d H:i:s", $sec).".".$usec." "
        		  .$result['source_ip'].":".$result['source_port']." -> "
	                  .$result['destination_ip'].":".$result['destination_port']."\r\n\r\n";
        	     $buf.=$data."\r\n";

	        } else {
	
        	     //Ethernet + IP + UDP
	             $size['total'] = $size['ethernet'] + $size['ip'] + $size['udp'] + $size['data'];

	             //Pcap record
        	     $pcaprec_hdr = array();
	             $pcaprec_hdr['ts_sec'] = intval($result['micro_ts'] / 1000000);  //4
	             $pcaprec_hdr['ts_usec'] = $result['micro_ts'] - ($pcaprec_hdr['ts_sec']*1000000); //4
	             $pcaprec_hdr['incl_len'] = $size['total']; //4
	             $pcaprec_hdr['orig_len'] = $size['total']; //4

	             $pcaprec_packet = pack("llll", $pcaprec_hdr['ts_sec'], $pcaprec_hdr['ts_usec'], $pcaprec_hdr['incl_len'], $pcaprec_hdr['orig_len']);	
        	     $buf.=$pcaprec_packet;

        	     //ethernet header
	             $buf.=$ethernet;

        	     //UDP
	             $udp_hdr = array();
	             $udp_hdr['src_port'] = $result['source_port'];
	             $udp_hdr['dst_port'] = $result['destination_port'];
        	     $udp_hdr['length'] = $size['udp'] + $size['data'];
	             $udp_hdr['checksum'] = 0;

        	     //Calculate UDP checksum
        	     $pseudo = pack("nnnna*", $udp_hdr['src_port'],$udp_hdr['dst_port'], $udp_hdr['length'], $udp_hdr['checksum'], $data);	                
        	     $udp_hdr['checksum'] = $this->pcapCheckSum($pseudo);

	             //IPHEADER
		     $ipv4_hdr = array();
		     $ip_ver = 4;
		     $ip_len = 5;
		     $ip_frag_flag = "010";
		     $ip_frag_oset = "0000000000000";
		     $ipv4_hdr['ver_len'] = $ip_ver . $ip_len;
		     $ipv4_hdr['tos'] = "00";
   		     $ipv4_hdr['total_len'] = $size['ip'] + $size['udp'] + $size['data'];
		     $ipv4_hdr['ident'] = 19245;
		     $ipv4_hdr['fl_fr'] = 4000;
		     $ipv4_hdr['ttl'] = 30;
		     $ipv4_hdr['proto'] = 17;
		     $ipv4_hdr['checksum'] = 0;
		     $ipv4_hdr['src_ip'] = ip2long($result['source_ip']);
		     $ipv4_hdr['dst_ip'] = ip2long($result['destination_ip']);
		     $pseudo = pack('H2H2nnH4C2nNN', $ipv4_hdr['ver_len'],$ipv4_hdr['tos'],$ipv4_hdr['total_len'], $ipv4_hdr['ident'],
		           $ipv4_hdr['fl_fr'], $ipv4_hdr['ttl'],$ipv4_hdr['proto'],$ipv4_hdr['checksum'], $ipv4_hdr['src_ip'], $ipv4_hdr['dst_ip']);
		     $ipv4_hdr['checksum'] = $this->pcapCheckSum($pseudo);

		     $pkt = pack('H2H2nnH4C2nNNnnnna*', $ipv4_hdr['ver_len'],$ipv4_hdr['tos'],$ipv4_hdr['total_len'], $ipv4_hdr['ident'],
			$ipv4_hdr['fl_fr'], $ipv4_hdr['ttl'],$ipv4_hdr['proto'],$ipv4_hdr['checksum'], $ipv4_hdr['src_ip'], $ipv4_hdr['dst_ip'],
			$udp_hdr['src_port'],$udp_hdr['dst_port'], $udp_hdr['length'], $udp_hdr['checksum'], $data);
	        }

	        $buf.=$pkt;
	   }     

	   $fileid = "sip_";
	   $fileid .= $result['callid'];
	   $pcapfile = "HOMER5_$fileid";
	   $pcapfile .= $text ? ".txt" : ".pcap";
	   
           return array($pcapfile, strlen($buf), $buf);
    }          


    public function doShareLink($timestamp, $param){
    
        if(count(($adata = $this->getLoggedIn()))) return $adata;        
                           
        $answer = array();          

        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();
        
        $uid = $_SESSION['uid'];        
         
        $data['timestamp'] = $timestamp;
        $data['param'] = $param; 
                                                                  
	$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000,
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));

        $json = json_encode($data, true);
        $query = "INSERT INTO link_share set uid='?', uuid='?', data='?', expire=CURDATE() + INTERVAL 14 DAY";
        $query  = $db->makeQuery($query, $uid, $uuid, $json );
        $db->executeQuery($query);
           
        $reply[0] = "http://homer5.sipcapture.org:88/share/#".$uuid;
           
        $answer['status'] = 200;
        $answer['sid'] = session_id();
        $answer['auth'] = 'true';             
        $answer['message'] = 'ok';                             
        $answer['data'] = $reply;
        $answer['count'] = 0;
        
        return $answer;
    }

    public function getNode($name){

        $db = $this->getContainer('db');
        $db->select_db(DB_CONFIGURATION);
        $db->dbconnect();

        $table = "node";
        $query = "SELECT * FROM ".$table." WHERE name='?' AND status = 1 LIMIT 1";
	$query  = $db->makeQuery($query, $name);
        $data = $db->loadObjectArray($query);
	if(empty($data)) return array();
	return $data[0];
    }
    
    public function getContainer($name)
    {
        if (!$this->_instance || !isset($this->_instance[$name]) || $this->_instance[$name] === null) {
            //$config = \Config::factory('configs/config.ini', APPLICATION_ENV, 'auth');
            if($name == "auth") $containerClass = sprintf("Authentication\\".AUTHENTICATION);
            else if($name == "db") $containerClass = sprintf("Database\\".DATABASE_CONNECTOR);            
            $this->_instance[$name] = new $containerClass();
        }
        return $this->_instance[$name];
    }

    /**
     * @param string $server
     * @url stats/([0-9]+)
     * @url stats
     * @return string
     */
    public function getStats($server = '1'){
        return $this->getServerStats($server);
    }
    
    
    public function pcapCheckSum($data) {

	if( strlen($data)%2 ) $data .= "\x00";
	$bit = unpack('n*', $data);
	$sum = array_sum($bit);
	while ($sum >> 16) $sum = ($sum >> 16) + ($sum & 0xffff);
	$sum = ~$sum;
	$sum = $sum & 0xffff;
	return $sum;    

    } 
    
}

?>
