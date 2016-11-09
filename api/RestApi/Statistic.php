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

class Statistic {

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

    public function doStatisticMethod($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();

        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['method'] = getVar('method', NULL, $filter, 'string');
            $search[$key]['cseq'] = getVar('cseq', NULL, $filter, 'string');
            $search[$key]['auth'] = getVar('auth', -1, $filter, 'int');
            $search[$key]['totag'] = getVar('totag', -1, $filter, 'int');

            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $order = "";

        //if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

	$layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_method";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['replace'] = "auth";
        $layerHelper['fields']['time'] = "to_date";

        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, method,cseq,COUNT(id) as cnt,SUM(total) as total";
	        $layerHelper['group']['by'] = "method, cseq, auth, totag";
	}
	else
	{
		$layerHelper['values'][] = "id, method, cseq, totag, total";

               //$fields = "id, UNIX_TIMESTAMP(`from_date`) as from_ts, UNIX_TIMESTAMP(`to_date`) as to_ts, method, REPLACE(REPLACE(auth, 0,'N'),1,'A') AS auth, cseq, COUNT(id) as cnt, SUM(total) as total";
               //$order .= " GROUP BY method, cseq, auth, totag";
               //$fields = "id, UNIX_TIMESTAMP(`from_date`) as from_ts, UNIX_TIMESTAMP(`to_date`) as to_ts, method, REPLACE(REPLACE(auth, 0,'N'),1,'A') AS auth, cseq, totag, total";
        }

	$query = $layer->querySearchData($layerHelper);
        $noderows = $db->loadObjectArray($query);
        $data = $db->loadObjectArray($query);

        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


    public function getStatisticMethod($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticMethod($timestamp, $param);
    }

    public function doStatisticAlarm($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();

        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['type'] = getVar('type', NULL, $filter, 'string');

            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        //if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "alarm_data";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'create_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'create_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "create_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, source_ip, type, COUNT(id) as cnt,SUM(total) as total";
	        $layerHelper['group']['by'] = "type";
	}
	else
	{
		$layerHelper['values'][] = "id, source_ip, description, type, total";
        }

	$query = $layer->querySearchData($layerHelper);

        $data = $db->loadObjectArray($query);

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


    public function getStatisticAlarm($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticAlarm($timestamp, $param);
    }

    /* api: statistic/data */

    public function doStatisticData($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();
        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');



        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['type'] = getVar('type', NULL, $filter, 'string');
            $search[$key]['source_ip'] = getVar('source_ip', NULL, $filter, 'string');
            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_data";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, type, COUNT(id) as cnt,SUM(total) as total";
	        $layerHelper['group']['by'] = "type";
	}
	else
	{
		$layerHelper['values'][] = "id, type, total";
        }

	$query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);

        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


        return $answer;
    }


    public function getStatisticData($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticData($timestamp, $param);
    }


    /* do statistic generic */
    public function doStatisticGeneric($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();

        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            foreach($filter as $fk=>$fd) {
                /* remove all not needed characters */
                $nfk = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $fk);
                $search[$key][$nfk] = getVar($nfk, NULL, $filter, 'string');
            }

            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_generic";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, type, COUNT(id) as cnt,SUM(total) as total";
	        $layerHelper['group']['by'] = "type";
	}
	else
	{
		$layerHelper['values'][] = "id, type, total";
        }

	$query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);

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


    public function getStatisticGeneric($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticGeneric($timestamp, $param);
    }


    /* api: statistic/ip */

    public function doStatisticIP($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();

        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['method'] = getVar('method', NULL, $filter, 'string');
            $search[$key]['source_ip'] = getVar('source_ip', NULL, $filter, 'string');
            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_ip";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, source_ip, method, COUNT(id) as cnt,SUM(total) as total";
	        $layerHelper['group']['by'] = "source_ip";
	}
	else
	{
		$layerHelper['values'][] = "id, source_ip, method, total";
        }

	$query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);

        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


        return $answer;
    }


    public function getStatisticIP($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticIP($timestamp, $param);
    }


    /* api/statictic/country */
    public function doStatisticCountry($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        if(SYSLOG_ENABLE == 1) openlog("homerlog", LOG_PID | LOG_PERROR, LOG_LOCAL0);                

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();
        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['method'] = getVar('method', NULL, $filter, 'string');
            $search[$key]['country'] = getVar('country', NULL, $filter, 'string');
            if($search[$key]['country'] == "ALL") $search[$key]['country'] = NULL;
            if($search[$key]['method'] == "ALL") $search[$key]['method'] = NULL;
            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_geo";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, COUNT(id) as cnt,SUM(total) as total, country, lat, lon, method";
	        $layerHelper['group']['by'] = "country,method";
	}
	else
	{
		$layerHelper['values'][] = "id, country, lat, lon, method, total";
        }

        $query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);
        if(SYSLOG_ENABLE == 1) syslog(LOG_WARNING,"Stats country query: ".$query); 
                                
        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


        return $answer;
    }


    public function getStatisticCountry($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticCountry($timestamp, $param);
    }

    /* api/statictic/destination */
    public function doStatisticDestination($timestamp, $param){

    /* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();
        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {

            $search[$key]['method'] = getVar('method', NULL, $filter, 'string');
            $search[$key]['prefix'] = getVar('prefix', NULL, $filter, 'string');
            $search[$key]['country'] = getVar('country', NULL, $filter, 'string');
            $search[$key]['status_code'] = getVar('status_code', NULL, $filter, 'string');
            if($search[$key]['country'] == "ALL") $search[$key]['country'] = NULL;
            if($search[$key]['prefix'] == "ALL") $search[$key]['prefix'] = NULL;
            if($search[$key]['method'] == "ALL") $search[$key]['method'] = NULL;
            if($search[$key]['status_code'] == "ALL") $search[$key]['status_code'] = NULL;
            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_dest_reply";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, COUNT(id) as cnt,SUM(total) as total, country, prefix, method, status_code";
	        $layerHelper['group']['by'] = "id,from_ts,to_ts,country,prefix,method,status_code";
	}
	else
	{
		$layerHelper['values'][] = "id, country, prefix, method, status_code, total";
        }

        $query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);

        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


    public function getStatisticDestination($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticDestination($timestamp, $param);
    }

    /* api: statistic/uac */

    public function doStatisticUserAgent($timestamp, $param){

	/* auth */
        if(count(($adata = $this->getLoggedIn()))) return $adata;

        /* get our DB */
        $db = $this->getContainer('db');
        $db->select_db(DB_STATISTIC);
        $db->dbconnect();

        /* get our DB Abstract Layer */
        $layer = $this->getContainer('layer');

        $data = array();

        $search = array();
        $callwhere = array();
        $calldata = array();
        $arrwhere = "";

        foreach($param['filter'] as $key=>$filter) {
            $search[$key]['method'] = getVar('method', NULL, $filter, 'string');
            $search[$key]['useragent'] = getVar('useragent', NULL, $filter, 'string');
            $callwhere = generateWhere($search[$key], 1, $db, 0);
            if(count($callwhere)) $calldata[] = "(". implode(" AND ", $callwhere). ")";
        }

        if(count($calldata)) $arrwhere = " AND (". implode(" OR ", $calldata). ")";

        $time['from'] = getVar('from', round((microtime(true) - 300) * 1000), $timestamp, 'long');
        $time['to'] = getVar('to', round(microtime(true) * 1000), $timestamp, 'long');
        $time['from_ts'] = intval($time['from']/1000);
        $time['to_ts'] = intval($time['to']/1000);

        $and_or = getVar('orand', NULL, $param['filter'], 'string');
        $limit = getVar('limit', 500, $param, 'int');
        $total = getVar('total', false, $param, 'bool');

        $layerHelper = array();
        $layerHelper['table'] = array();
        $layerHelper['order'] = array();
        $layerHelper['where'] = array();
        $layerHelper['fields'] = array();
        $layerHelper['values'] = array();
        $layerHelper['table']['base'] = "stats_useragent";
        $layerHelper['where']['type'] = $and_or ? "OR" : "AND";
        $layerHelper['where']['param'] = $calldata;
        $layerHelper['time'] = $time;

        $layerHelper['fields']['ts'] = array();
        $layerHelper['fields']['ts'][0]=array();
        $layerHelper['fields']['ts'][0]['name'] = 'from_date';
        $layerHelper['fields']['ts'][0]['alias'] = 'from_ts';
        $layerHelper['fields']['ts'][1]=array();
        $layerHelper['fields']['ts'][1]['name'] = 'to_date';
        $layerHelper['fields']['ts'][1]['alias'] = 'to_ts';

        $layerHelper['fields']['time'] = "to_date";
        $layerHelper['order']['by'] = "id";
        $layerHelper['order']['type'] = "DESC";

	if($total)
	{
		$layerHelper['values'][] = "id, COUNT(id) as cnt,SUM(total) as total, useragent, method ";
	        $layerHelper['group']['by'] = "useragent";
	}
	else
	{
		$layerHelper['values'][] = "id, useragent, method, total";
        }

        $query = $layer->querySearchData($layerHelper);
        $data = $db->loadObjectArray($query);

        /* sorting */
        //usort($data, create_function('$a, $b', 'return $a["micro_ts"] > $b["micro_ts"] ? 1 : -1;'));

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


        return $answer;
    }


    public function getStatisticUserAgent($raw_get_data){

        $timestamp = $raw_get_data['timestamp'];
        $param = $raw_get_data['param'];

        return $this->doStatisticUserAgent($timestamp, $param);
    }



    public function getContainer($name)
    {
        if (!$this->_instance || !isset($this->_instance[$name]) || $this->_instance[$name] === null) {
            //$config = \Config::factory('configs/config.ini', APPLICATION_ENV, 'auth');
            if($name == "auth") $containerClass = sprintf("Authentication\\".AUTHENTICATION);
            else if($name == "db") $containerClass = sprintf("Database\\".DATABASE_CONNECTOR);
            else if($name == "layer") $containerClass = sprintf("Database\\Layer\\".DATABASE_DRIVER);
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
