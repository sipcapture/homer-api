<?php


Server::create(WEBROOT.'api/v1', 'RestApi\Auth') //base entry points `/admin`
    ->setDebugMode(true) //prints the debug trace, line number and file if a exception has been thrown.
    
/**
*  @api {get} /api/v1/session get current session
*  @apiName get session
*  @apiGroup Auth
*    
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X GET \
*   "http://localhost/api/v1/session"
*    
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.username username of user
*  @apiSuccess (700) {String} data.gid gid of User
*  @apiSuccess (700) {String} data.grp  groups of User
*   
*  @apiSuccessExample Success-Response:
*      HTTP/1.1 200 OK
*	{
*	    "sid": "tcuass65ejl2lifoopuuurpmq7",
*	    "auth": "true",
*	    "status": 200,
*	    "data": {
*	        "username": "admin",
*	        "gid": "10",
*	        "grp": "users,admins"
*	    } 
* 
*  @apiErrorExample Error-Response:
*	  HTTP/1.1 200 OK
*	  Set-Cookie: tcuass65ejl2lifoopuuurpmq7; path=/
*	  Content-Type: application/json; charset=UTF-8
*
*	  {
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*          }    		     			
*   
**/    

    ->addGetRoute('session', 'getSession') // => /api/v1/session

/**
*  @api {post} /api/v1/session create a session
*  @apiName  create session
*  @apiGroup Auth
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"username":"admin","password":"test123"}' \
*   http://localhost/api/v1/session
*      
*  @apiParam {String} username Login for session creation.
*  @apiParam {String} password Password for session creation.
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.uid the user id
*  @apiSuccess (700) {String} data.username username of User
*  @apiSuccess (700) {String} data.gid gid of User
*  @apiSuccess (700) {String} data.grp  groups of User
*  @apiSuccess (700) {String} data.firstname Firstname of User
*  @apiSuccess (700) {String} data.lastname Lastname of user
*  @apiSuccess (700) {String} data.email User's email
*  @apiSuccess (700) {Date} data.lastvisit Last visit
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "tcuass65ejl2lifoopuuurpmq7",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": {
*	        "uid": "3",
*	        "username": "admin",
*	        "gid": "10",
*	        "grp": "users,admins",
*	        "firstname": "Alexandr",
*	        "lastname": "Dubovikov",
*	        "email": "admin@sipcapture.org",
*	        "lastvisit": "2015-06-18 08:25:55"
*	}   
* 
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/



    ->addPostRoute('session', 'doSession') // => /api/v1/session
/*
*  @api {delete} /api/v1/session delete current session
*  @apiName delete session
*  @apiGroup Auth
*    
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X DELETE \
*   "http://localhost/api/v1/session"
*    
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "session deleted","wrong-session"
*   
*  @apiSuccessExample Success-Response:
*      HTTP/1.1 200 OK
*	{
*	    "sid": "",
*	    "auth": "true",
*	    "status": 200,
*	    "message": "session deleted"
*	}
* 
*  @apiErrorExample Error-Response:
*	  HTTP/1.1 200 OK
*	  Set-Cookie: tcuass65ejl2lifoopuuurpmq7; path=/
*	  Content-Type: application/json; charset=UTF-8
*
*	  {
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*		"auth":"false",
*		"status":"wrong-session"
*          }    		     			
*   
**/    


    ->addDeleteRoute('session', 'doLogout') // => /admin/logout
/**
*  @api {get} /api/v1/logout delete current session
*  @apiName logout of session
*  @apiGroup Auth
*    
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X GET \
*   "http://localhost/api/v1/logout"
*    
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "session deleted","wrong-session"
*   
*  @apiSuccessExample Success-Response:
*      HTTP/1.1 200 OK
*	{
*	    "sid": "",
*	    "auth": "true",
*	    "status": 200,
*	    "message": "session deleted"
*	}
* 
*  @apiErrorExample Error-Response:
*	  HTTP/1.1 200 OK
*	  Set-Cookie: tcuass65ejl2lifoopuuurpmq7; path=/
*	  Content-Type: application/json; charset=UTF-8
*
*	  {
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*		"auth":"false",
*		"status":"wrong-session"
*          }    		     			
*   
**/    


    ->addGetRoute('logout', 'doLogout') // => /admin/logout
    
    ->addSubController('search', 'RestApi\Search') //adds a new sub entry point 'tools' => admin/tools
/**
*  @api {post} /api/v1/search/data do search
*  @apiName  search data
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"param":{"transaction":{"call":true},"limit":200,"search":{"ruri_user":"108"},"node":[{"id":"1","name":"homer01"}]},"timestamp":{"from":1433521859738,"to":1433529659738}}' \
*   http://localhost/api/v1/search/data
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {Number} param.limit call limit 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {String} param.search.from_user From user
*  @apiParam {String} param.search.to_user To user
*  @apiParam {String} param.search.ruri_user Ruri user
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String} param.search.callid_aleg Callid of Aleg  user
*  @apiParam {String} param.search.contact_user Contact user
*  @apiParam {String} param.search.pid_user P-Asserted-Identity user
*  @apiParam {String} param.search.auth_user Auth user
*  @apiParam {String} param.search.user_agent User-Agent
*  @apiParam {String} param.search.method method (INVITE, BYE)
*  @apiParam {String} param.search.cseq Cseq of message
*  @apiParam {String} param.search.reason Reason of Message (200, 180, 503)
*  @apiParam {String} param.search.msg Raw message
*  @apiParam {String} param.search.diversion Diversion
*  @apiParam {String} param.search.via_1 First via
*  @apiParam {String} param.search.source_ip Source IP of message
*  @apiParam {String} param.search.source_port Source port of message
*  @apiParam {String} param.search.destination_ip Destination IP of message
*  @apiParam {String} param.search.destination_port Destination port of message
*  @apiParam {String} param.search.uniq uniq packets
*  @apiParam {String} param.search.proto protocol of transport
*  @apiParam {String} param.search.family IP family (4, 6)
*  @apiParam {String} param.search.orand user OR login in search
*  @apiParam {String[]} param.search.node array of nodes
*  @apiParam {String} param.search.node.id id of node
*  @apiParam {String} param.search.node.name name of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.id record id of message
*  @apiSuccess (700) {String} data.date datetime of record
*  @apiSuccess (700) {String} data.milli_ts timestamp of record in milliseconds
*  @apiSuccess (700) {String} data.micro_ts timestamp of record in microseconds
*  @apiSuccess (700) {String} data.from_user From user
*  @apiSuccess (700) {String} data.from_domain From domain
*  @apiSuccess (700) {String} data.from_tag From tag
*  @apiSuccess (700) {String} data.to_user To user
*  @apiSuccess (700) {String} data.to_domain To domain
*  @apiSuccess (700) {String} data.to_tag To tag
*  @apiSuccess (700) {String} data.ruri_user Ruri user
*  @apiSuccess (700) {String} data.ruri_domain Ruri domain
*  @apiSuccess (700) {String} data.callid Callid user
*  @apiSuccess (700) {String} data.callid_aleg Callid of Aleg  user
*  @apiSuccess (700) {String} data.contact_user Contact user
*  @apiSuccess (700) {String} data.correlation_id Correlation ID of message
*  @apiSuccess (700) {String} data.pid_user P-Asserted-Identity user
*  @apiSuccess (700) {String} data.auth_user Auth user
*  @apiSuccess (700) {String} data.user_agent User-Agent
*  @apiSuccess (700) {String} data.method method (INVITE, BYE)
*  @apiSuccess (700) {String} data.cseq Cseq of message
*  @apiSuccess (700) {String} data.reply_reason reply reason of Message (OK, Ringing..)
*  @apiSuccess (700) {String} data.msg Raw message
*  @apiSuccess (700) {String} data.diversion Diversion
*  @apiSuccess (700) {String} data.via_1 First via
*  @apiSuccess (700) {String} data.via_1_branch First via branch
*  @apiSuccess (700) {String} data.source_ip Source IP of message
*  @apiSuccess (700) {String} data.source_port Source port of message
*  @apiSuccess (700) {String} data.destination_ip Destination IP of message
*  @apiSuccess (700) {String} data.destination_port Destination port of message
*  @apiSuccess (700) {String} data.contact_ip IP of contact
*  @apiSuccess (700) {String} data.contact_port port of contact
*  @apiSuccess (700) {String} data.originator_ip Originator IP of message
*  @apiSuccess (700) {String} data.originator_port Originator port of message
*  @apiSuccess (700) {String} data.rtp_stat Rtp statistic of call
*  @apiSuccess (700) {String} data.type encapsulation type 
*  @apiSuccess (700) {String} data.node store node of message 
*  @apiSuccess (700) {String} data.dbnode db node type of message (single)
*  @apiSuccess (700) {String} data.trans transaction type of message (call, registration)
*  @apiSuccess (700) {String} data.proto protocol of transport
*  @apiSuccess (700) {String} data.family IP family 
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "qbha61781lqpfpnodvkqbfeai4",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": [
*        	{
*			"id": "14588",
*			"date": "2015-06-05 18:39:02",
*			"milli_ts": "1433529542283",
*			"micro_ts": "1433529542283432",
*			"method": "401",
*			"reply_reason": "Unauthorized",
*			"ruri": "",
*			"ruri_user": "",
*	            	"ruri_domain": "",
*			"from_user": "lab",
*	            	"from_domain": "",
*			"from_tag": "1022317138",
*	            	"to_user": "lab",
*			"to_domain": "",
*	            	"to_tag": "1d24a28a0bded6c40d31e6db8aab9ac6.5494",
*			"pid_user": "",
*	            	"contact_user": "",
*			"auth_user": "",
*	            	"callid": "426690302",
*	            	"callid_aleg": "",
*	            	"via_1": "SIP\/2.0\/UDP 192.168.1.23:5060;received=87.210.62.235;rport=5060;branch=z9hG4bK148935884",
*			"via_1_branch": "z9hG4bK148935884",
*	            	"cseq": "1521 REGISTER",
*	            	"diversion": "",
*	            	"reason": "",
*	            	"content_type": "",
*	            	"auth": "",
*	            	"user_agent": "",
*	            	"source_ip": "188.226.157.55",
*	            	"source_port": "5060",
*	            	"destination_ip": "87.210.62.235",
*	            	"destination_port": "5060",
*	            	"contact_ip": "",
*	            	"contact_port": "0",
*	            	"originator_ip": "",
*	            	"originator_port": "0",
*	            	"correlation_id": "",
*	            	"proto": "1",
*	            	"family": "2",
*			"rtp_stat": "",
*	            	"type": "2",
*			"node": "homer01:2001",
*	           	"trans": "call",
*			"dbnode": "single"
*	        }, 
*		...				
*	    ],
*	    "count": 200
*      }
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/


      ->addPostRoute('data', 'doSearchData') // => /api/v1/session
/**
*  @api {post} /api/v1/search/method method by id
*  @apiName  search method
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"timestamp":{"from":1433529542283,"to":1433529542283},"param":{"search":{"id":14588,"callid":"426690302"},"location":{"node":"single"},"transaction":{"call":true,"registration":false,"rest":false}}}' \
*   http://localhost/api/v1/search/method
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {Number} param.search.id ID of message
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String[]} param.search.location array of location
*  @apiParam {String} param.search.location.node id of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.id record id of message
*  @apiSuccess (700) {String} data.date datetime of record
*  @apiSuccess (700) {String} data.milli_ts timestamp of record in milliseconds
*  @apiSuccess (700) {String} data.micro_ts timestamp of record in microseconds
*  @apiSuccess (700) {String} data.from_user From user
*  @apiSuccess (700) {String} data.from_domain From domain
*  @apiSuccess (700) {String} data.from_tag From tag
*  @apiSuccess (700) {String} data.to_user To user
*  @apiSuccess (700) {String} data.to_domain To domain
*  @apiSuccess (700) {String} data.to_tag To tag
*  @apiSuccess (700) {String} data.ruri_user Ruri user
*  @apiSuccess (700) {String} data.ruri_domain Ruri domain
*  @apiSuccess (700) {String} data.callid Callid user
*  @apiSuccess (700) {String} data.callid_aleg Callid of Aleg  user
*  @apiSuccess (700) {String} data.contact_user Contact user
*  @apiSuccess (700) {String} data.correlation_id Correlation ID of message
*  @apiSuccess (700) {String} data.pid_user P-Asserted-Identity user
*  @apiSuccess (700) {String} data.auth_user Auth user
*  @apiSuccess (700) {String} data.user_agent User-Agent
*  @apiSuccess (700) {String} data.method method (INVITE, BYE)
*  @apiSuccess (700) {String} data.cseq Cseq of message
*  @apiSuccess (700) {String} data.reply_reason reply reason of Message (OK, Ringing..)
*  @apiSuccess (700) {String} data.msg Raw message
*  @apiSuccess (700) {String} data.diversion Diversion
*  @apiSuccess (700) {String} data.via_1 First via
*  @apiSuccess (700) {String} data.via_1_branch First via branch
*  @apiSuccess (700) {String} data.source_ip Source IP of message
*  @apiSuccess (700) {String} data.source_port Source port of message
*  @apiSuccess (700) {String} data.destination_ip Destination IP of message
*  @apiSuccess (700) {String} data.destination_port Destination port of message
*  @apiSuccess (700) {String} data.contact_ip IP of contact
*  @apiSuccess (700) {String} data.contact_port port of contact
*  @apiSuccess (700) {String} data.originator_ip Originator IP of message
*  @apiSuccess (700) {String} data.originator_port Originator port of message
*  @apiSuccess (700) {String} data.rtp_stat Rtp statistic of call
*  @apiSuccess (700) {String} data.type encapsulation type 
*  @apiSuccess (700) {String} data.node store node of message 
*  @apiSuccess (700) {String} data.dbnode db node type of message (single)
*  @apiSuccess (700) {String} data.trans transaction type of message (call, registration)
*  @apiSuccess (700) {String} data.proto protocol of transport
*  @apiSuccess (700) {String} data.family IP family 
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "qbha61781lqpfpnodvkqbfeai4",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": [
*        	{
*			"id": "14588",
*			"date": "2015-06-05 18:39:02",
*			"milli_ts": "1433529542283",
*			"micro_ts": "1433529542283432",
*			"method": "401",
*			"reply_reason": "Unauthorized",
*			"ruri": "",
*			"ruri_user": "",
*	            	"ruri_domain": "",
*			"from_user": "lab",
*	            	"from_domain": "",
*			"from_tag": "1022317138",
*	            	"to_user": "lab",
*			"to_domain": "",
*	            	"to_tag": "1d24a28a0bded6c40d31e6db8aab9ac6.5494",
*			"pid_user": "",
*	            	"contact_user": "",
*			"auth_user": "",
*	            	"callid": "426690302",
*	            	"callid_aleg": "",
*	            	"via_1": "SIP\/2.0\/UDP 192.168.1.23:5060;received=87.210.62.235;rport=5060;branch=z9hG4bK148935884",
*			"via_1_branch": "z9hG4bK148935884",
*	            	"cseq": "1521 REGISTER",
*	            	"diversion": "",
*	            	"reason": "",
*	            	"content_type": "",
*	            	"auth": "",
*	            	"user_agent": "",
*	            	"source_ip": "188.226.157.55",
*	            	"source_port": "5060",
*	            	"destination_ip": "87.210.62.235",
*	            	"destination_port": "5060",
*	            	"contact_ip": "",
*	            	"contact_port": "0",
*	            	"originator_ip": "",
*	            	"originator_port": "0",
*	            	"correlation_id": "",
*	            	"proto": "1",
*	            	"family": "2",
*			"rtp_stat": "",
*	            	"type": "2",
*			"node": "homer01:2001",
*	           	"trans": "call",
*			"dbnode": "single"
*	        }
*	    ],
*	    "count": 1
*      }
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/


      ->addPostRoute('method', 'doSearchMethod') // => /api/v1/session
/**
*  @api {post} /api/v1/search/message message by callid
*  @apiName  search message(s)
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"timestamp":{"from":1433529542283,"to":1433529542283},"param":{"search":{"id":14588,"callid":"426690302"},"location":{"node":"single"},"transaction":{"call":true,"registration":false,"rest":false}}}' \
*   http://localhost/api/v1/search/message
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {Number} param.search.id ID of message
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String[]} param.search.location array of location
*  @apiParam {String} param.search.location.node id of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.id record id of message
*  @apiSuccess (700) {String} data.date datetime of record
*  @apiSuccess (700) {String} data.milli_ts timestamp of record in milliseconds
*  @apiSuccess (700) {String} data.micro_ts timestamp of record in microseconds
*  @apiSuccess (700) {String} data.from_user From user
*  @apiSuccess (700) {String} data.from_domain From domain
*  @apiSuccess (700) {String} data.from_tag From tag
*  @apiSuccess (700) {String} data.to_user To user
*  @apiSuccess (700) {String} data.to_domain To domain
*  @apiSuccess (700) {String} data.to_tag To tag
*  @apiSuccess (700) {String} data.ruri_user Ruri user
*  @apiSuccess (700) {String} data.ruri_domain Ruri domain
*  @apiSuccess (700) {String} data.callid Callid user
*  @apiSuccess (700) {String} data.callid_aleg Callid of Aleg  user
*  @apiSuccess (700) {String} data.contact_user Contact user
*  @apiSuccess (700) {String} data.correlation_id Correlation ID of message
*  @apiSuccess (700) {String} data.pid_user P-Asserted-Identity user
*  @apiSuccess (700) {String} data.auth_user Auth user
*  @apiSuccess (700) {String} data.user_agent User-Agent
*  @apiSuccess (700) {String} data.method method (INVITE, BYE)
*  @apiSuccess (700) {String} data.cseq Cseq of message
*  @apiSuccess (700) {String} data.reply_reason reply reason of Message (OK, Ringing..)
*  @apiSuccess (700) {String} data.msg Raw message
*  @apiSuccess (700) {String} data.diversion Diversion
*  @apiSuccess (700) {String} data.via_1 First via
*  @apiSuccess (700) {String} data.via_1_branch First via branch
*  @apiSuccess (700) {String} data.source_ip Source IP of message
*  @apiSuccess (700) {String} data.source_port Source port of message
*  @apiSuccess (700) {String} data.destination_ip Destination IP of message
*  @apiSuccess (700) {String} data.destination_port Destination port of message
*  @apiSuccess (700) {String} data.contact_ip IP of contact
*  @apiSuccess (700) {String} data.contact_port port of contact
*  @apiSuccess (700) {String} data.originator_ip Originator IP of message
*  @apiSuccess (700) {String} data.originator_port Originator port of message
*  @apiSuccess (700) {String} data.rtp_stat Rtp statistic of call
*  @apiSuccess (700) {String} data.type encapsulation type 
*  @apiSuccess (700) {String} data.node store node of message 
*  @apiSuccess (700) {String} data.dbnode db node type of message (single)
*  @apiSuccess (700) {String} data.trans transaction type of message (call, registration)
*  @apiSuccess (700) {String} data.proto protocol of transport
*  @apiSuccess (700) {String} data.family IP family 
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "qbha61781lqpfpnodvkqbfeai4",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": [
*        	{
*			"id": "14588",
*			"date": "2015-06-05 18:39:02",
*			"milli_ts": "1433529542283",
*			"micro_ts": "1433529542283432",
*			"method": "401",
*			"reply_reason": "Unauthorized",
*			"ruri": "",
*			"ruri_user": "",
*	            	"ruri_domain": "",
*			"from_user": "lab",
*	            	"from_domain": "",
*			"from_tag": "1022317138",
*	            	"to_user": "lab",
*			"to_domain": "",
*	            	"to_tag": "1d24a28a0bded6c40d31e6db8aab9ac6.5494",
*			"pid_user": "",
*	            	"contact_user": "",
*			"auth_user": "",
*	            	"callid": "426690302",
*	            	"callid_aleg": "",
*	            	"via_1": "SIP\/2.0\/UDP 192.168.1.23:5060;received=87.210.62.235;rport=5060;branch=z9hG4bK148935884",
*			"via_1_branch": "z9hG4bK148935884",
*	            	"cseq": "1521 REGISTER",
*	            	"diversion": "",
*	            	"reason": "",
*	            	"content_type": "",
*	            	"auth": "",
*	            	"user_agent": "",
*	            	"source_ip": "188.226.157.55",
*	            	"source_port": "5060",
*	            	"destination_ip": "87.210.62.235",
*	            	"destination_port": "5060",
*	            	"contact_ip": "",
*	            	"contact_port": "0",
*	            	"originator_ip": "",
*	            	"originator_port": "0",
*	            	"correlation_id": "",
*	            	"proto": "1",
*	            	"family": "2",
*			"rtp_stat": "",
*	            	"type": "2",
*			"node": "homer01:2001",
*	           	"trans": "call",
*			"dbnode": "single"
*	        },
*		...          
*	    ],
*	    "count": 10
*      }
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/
      
      ->addPostRoute('message', 'doSearchMessage') // => /api/v1/session
/**
*  @api {post} /api/v1/search/transaction search trasaction
*  @apiName  search transaction(s)
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"timestamp":{"from":1433529545834,"to":1433529659738},"param":{"search":{"id":14600,"callid":["188735396@127.0.1.1"]},"location":{"node":[]},"transaction":{"call":true,"registration":false,"rest":false}}}' \
*   http://localhost/api/v1/search/transaction
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {Number} param.search.id ID of message
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String[]} param.search.location array of location
*  @apiParam {String} param.search.location.node id of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String[]} data.info array
*  @apiSuccess (700) {String} data.info.totdur total duration of transaction
*  @apiSuccess (700) {Number} data.info.status call status
*  @apiSuccess (700) {String[]} data.hosts unique hosts
*  @apiSuccess (700) {String[]} data.uac User-Agent information
*  @apiSuccess (700) {String[]} data.rtpinfo Rtp stats information
*  @apiSuccess (700) {String[]} data.to_user To user
*  @apiSuccess (700) {String} data.calldata Array
*  @apiSuccess (700) {String} data.calldata.id ID of message
*  @apiSuccess (700) {String} data.calldata.method method (INVITE, BYE)
*  @apiSuccess (700) {String} data.calldata.src_port Source port of message
*  @apiSuccess (700) {String} data.calldata.dst_port Destination port of message
*  @apiSuccess (700) {String} data.calldata.ruri_user Ruri user
*  @apiSuccess (700) {String} data.calldata.callid Callid user
*  @apiSuccess (700) {Number} data.calldata.milli_ts Timestamp in milliseconds
*  @apiSuccess (700) {Number} data.calldata.micro_ts Timestamp in microseconds
*  @apiSuccess (700) {String} data.calldata.method_text Method 
*  @apiSuccess (700) {String} data.calldata.msg_color Color of message for Callflow 
*  @apiSuccess (700) {String} data.calldata.destination Destination vector
*  @apiSuccess (700) {String} data.calldata.trans transaction type of message (call, registration)
*  @apiSuccess (700) {String} data.calldata.node store node of message 
*  @apiSuccess (700) {String} data.calldata.dbnode db node type of message (single)
*  @apiSuccess (700) {Number} data.calldata.count number of messages
*  @apiSuccess (700) {String} count number of elements
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "qbha61781lqpfpnodvkqbfeai4",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": {
*	        "info": {
*	            "callid": [
*	                
*        	    ],
*	            "totdur": "00:00:00",
*	            "statuscall": 1
*	        },
*	        "hosts": {
*	            "127.0.0.1:5060": 0,
*	            "127.0.0.1:5062": 1,
*	            "127.0.0.1:6268": 2
*	        },
*	        "uac": {
*	            "127.0.0.1:5060": {
*	                "image": "sipgateway",
*	                "agent": ""
*	            }
*	        },
*        	"rtpinfo": [
*            
*	        ],
*	        "calldata": [
*        	    {
*	                "id": "14600",
*                	"method": "OPTIONS",
*	                "src_port": "5060",
*	                "dst_port": "5062",
*	                "trans": "call",
*	                "callid": "188735396@127.0.1.1",
*	                "node": "homer01:2001",
*	                "dbnode": "single",
*	                "micro_ts": "1433529546134940",
*	                "ruri_user": "nagios",
*	                "src_id": "127.0.0.1:5060",
*	                "dst_id": "127.0.0.1:5062",
*	                "milli_ts": 1433529546134,
*	                "method_text": "OPTIONS ",
*	                "msg_color": "purple",
*	                "destination": 1
*	            },
*		    ...
*        	    
*	        ],
*        	"count": 3
*	    },
*	    "count": 6
*	}
*
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/

      ->addPostRoute('transaction', 'doSearchTransaction') // => /api/v1/session
      ->addPostRoute('sharelink', 'doShareLink') // => /api/v1/session
      ->addPostRoute('share/transaction', 'doSearchShareTransaction') // => /api/v1/session
      ->addPostRoute('share/export/pcap', 'doPcapExportById') // => /api/v1/session
      ->addPostRoute('share/export/text', 'doTextExportById') // => /api/v1/session
/**
*  @api {post} /api/v1/search/export/pcap pcap export
*  @apiName  pcap of transaction
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"timestamp":{"from":1433529545834,"to":1433529659738},"param":{"search":{"id":14600,"callid":["188735396@127.0.1.1"]},"location":{"node":[]},"transaction":{"call":true,"registration":false,"rest":false}}}' \
*   http://localhost/api/v1/search/export/pcap
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {Number} param.search.id ID of message
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String[]} param.search.location array of location
*  @apiParam {String} param.search.location.node id of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {Binary} PCAP FILE
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*       [BINARY DATA]
*
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/


      ->addPostRoute('export/pcap', 'doPcapExport') // => /api/v1/session
/**
*  @api {post} /api/v1/search/export/text text export
*  @apiName  text of transaction
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"timestamp":{"from":1433529545834,"to":1433529659738},"param":{"search":{"id":14600,"callid":["188735396@127.0.1.1"]},"location":{"node":[]},"transaction":{"call":true,"registration":false,"rest":false}}}' \
*   http://localhost/api/v1/search/export/text
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {Number} param.search.id ID of message
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String[]} param.search.location array of location
*  @apiParam {String} param.search.location.node id of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} TEXT FILE
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*       [TEXT DATA]
*
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/



      ->addPostRoute('export/text', 'doTextExport') // => /api/v1/session
/**
*  @api {post} /api/v1/search/export/data/pcap pcap export of messages
*  @apiName  pcap export of messages
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"param":{"transaction":{"call":true},"limit":200,"search":{"ruri_user":"108"},"node":[{"id":"1","name":"homer01"}]},"timestamp":{"from":1433521859738,"to":1433529659738}}' \
*   http://localhost/api/v1/search/export/data/pcap
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {Number} param.limit call limit 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {String} param.search.from_user From user
*  @apiParam {String} param.search.to_user To user
*  @apiParam {String} param.search.ruri_user Ruri user
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String} param.search.callid_aleg Callid of Aleg  user
*  @apiParam {String} param.search.contact_user Contact user
*  @apiParam {String} param.search.pid_user P-Asserted-Identity user
*  @apiParam {String} param.search.auth_user Auth user
*  @apiParam {String} param.search.user_agent User-Agent
*  @apiParam {String} param.search.method method (INVITE, BYE)
*  @apiParam {String} param.search.cseq Cseq of message
*  @apiParam {String} param.search.reason Reason of Message (200, 180, 503)
*  @apiParam {String} param.search.msg Raw message
*  @apiParam {String} param.search.diversion Diversion
*  @apiParam {String} param.search.via_1 First via
*  @apiParam {String} param.search.source_ip Source IP of message
*  @apiParam {String} param.search.source_port Source port of message
*  @apiParam {String} param.search.destination_ip Destination IP of message
*  @apiParam {String} param.search.destination_port Destination port of message
*  @apiParam {String} param.search.uniq uniq packets
*  @apiParam {String} param.search.proto protocol of transport
*  @apiParam {String} param.search.family IP family (4, 6)
*  @apiParam {String} param.search.orand user OR login in search
*  @apiParam {String[]} param.search.node array of nodes
*  @apiParam {String} param.search.node.id id of node
*  @apiParam {String} param.search.node.name name of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {Binary} PCAP FILE
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*       [BINARY DATA]
*
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/

      ->addPostRoute('export/data/pcap', 'doPcapExportData') // => /api/v1/session
/**
*  @api {post} /api/v1/search/export/data/text text export of messages
*  @apiName  text export of messages
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X POST -H "Content-Type: application/json" \
*   -d '{"param":{"transaction":{"call":true},"limit":200,"search":{"ruri_user":"108"},"node":[{"id":"1","name":"homer01"}]},"timestamp":{"from":1433521859738,"to":1433529659738}}' \
*   http://localhost/api/v1/search/export/data/text
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {Number} param.limit call limit 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*  @apiParam {String[]} param.search array
*  @apiParam {String} param.search.from_user From user
*  @apiParam {String} param.search.to_user To user
*  @apiParam {String} param.search.ruri_user Ruri user
*  @apiParam {String} param.search.callid Callid user
*  @apiParam {String} param.search.callid_aleg Callid of Aleg  user
*  @apiParam {String} param.search.contact_user Contact user
*  @apiParam {String} param.search.pid_user P-Asserted-Identity user
*  @apiParam {String} param.search.auth_user Auth user
*  @apiParam {String} param.search.user_agent User-Agent
*  @apiParam {String} param.search.method method (INVITE, BYE)
*  @apiParam {String} param.search.cseq Cseq of message
*  @apiParam {String} param.search.reason Reason of Message (200, 180, 503)
*  @apiParam {String} param.search.msg Raw message
*  @apiParam {String} param.search.diversion Diversion
*  @apiParam {String} param.search.via_1 First via
*  @apiParam {String} param.search.source_ip Source IP of message
*  @apiParam {String} param.search.source_port Source port of message
*  @apiParam {String} param.search.destination_ip Destination IP of message
*  @apiParam {String} param.search.destination_port Destination port of message
*  @apiParam {String} param.search.uniq uniq packets
*  @apiParam {String} param.search.proto protocol of transport
*  @apiParam {String} param.search.family IP family (4, 6)
*  @apiParam {String} param.search.orand user OR login in search
*  @apiParam {String[]} param.search.node array of nodes
*  @apiParam {String} param.search.node.id id of node
*  @apiParam {String} param.search.node.name name of node
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} TEXT FILE
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*       [TEXT DATA]
*
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/

      ->addPostRoute('export/data/text', 'doTextExportData') // => /api/v1/session      
/**
*  @api {get} /api/v1/search/data get search
*  @apiName  get search data
*  @apiGroup Search
*
*  @apiParam (cookie) {String} HOMERSESSID cookie session id   
* 
*  @apiExample Example usage:
*   curl -v --cookie "HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/" -X GET -H "Content-Type: application/json" \
*   -d '{"param":{"transaction":{"call":true},"limit":200,"node":[{"id":"1","name":"homer01"}]},"timestamp":{"from":1433521859738,"to":1433529659738}}' \
*   http://localhost/api/v1/search/data
*      
*  @apiParam {String[]} timestamp array 
*  @apiParam {Number} timestamp.from search from this time
*  @apiParam {Number} timestamp.to search to this time
*  @apiParam {String[]} param array 
*  @apiParam {Number} param.limit call limit 
*  @apiParam {String[]} param.transaction  array
*  @apiParam {Boolean} param.transaction.call search for call transaction
*  @apiParam {Boolean} param.transaction.registration search for registration transaction
*  @apiParam {Boolean} param.transaction.rest search for rest transaction
*
* 
*  @apiSuccessTitle (700) Response 
*  
*  @apiSuccess (700) {String} sid session UUID. This should be used in cookie request for each next API call
*  @apiSuccess (700) {Number} status The current status:  200, 403 (HTTP code)
*  @apiSuccess (700) {String} message The current message: "ok","wrong-session"
*  @apiSuccess (700) {Boolean} auth Is a request authorized ? 
*  @apiSuccess (700) {String[]} data response array 
*  @apiSuccess (700) {String} data.id record id of message
*  @apiSuccess (700) {String} data.date datetime of record
*  @apiSuccess (700) {String} data.milli_ts timestamp of record in milliseconds
*  @apiSuccess (700) {String} data.micro_ts timestamp of record in microseconds
*  @apiSuccess (700) {String} data.from_user From user
*  @apiSuccess (700) {String} data.from_domain From domain
*  @apiSuccess (700) {String} data.from_tag From tag
*  @apiSuccess (700) {String} data.to_user To user
*  @apiSuccess (700) {String} data.to_domain To domain
*  @apiSuccess (700) {String} data.to_tag To tag
*  @apiSuccess (700) {String} data.ruri_user Ruri user
*  @apiSuccess (700) {String} data.ruri_domain Ruri domain
*  @apiSuccess (700) {String} data.callid Callid user
*  @apiSuccess (700) {String} data.callid_aleg Callid of Aleg  user
*  @apiSuccess (700) {String} data.contact_user Contact user
*  @apiSuccess (700) {String} data.correlation_id Correlation ID of message
*  @apiSuccess (700) {String} data.pid_user P-Asserted-Identity user
*  @apiSuccess (700) {String} data.auth_user Auth user
*  @apiSuccess (700) {String} data.user_agent User-Agent
*  @apiSuccess (700) {String} data.method method (INVITE, BYE)
*  @apiSuccess (700) {String} data.cseq Cseq of message
*  @apiSuccess (700) {String} data.reply_reason reply reason of Message (OK, Ringing..)
*  @apiSuccess (700) {String} data.msg Raw message
*  @apiSuccess (700) {String} data.diversion Diversion
*  @apiSuccess (700) {String} data.via_1 First via
*  @apiSuccess (700) {String} data.via_1_branch First via branch
*  @apiSuccess (700) {String} data.source_ip Source IP of message
*  @apiSuccess (700) {String} data.source_port Source port of message
*  @apiSuccess (700) {String} data.destination_ip Destination IP of message
*  @apiSuccess (700) {String} data.destination_port Destination port of message
*  @apiSuccess (700) {String} data.contact_ip IP of contact
*  @apiSuccess (700) {String} data.contact_port port of contact
*  @apiSuccess (700) {String} data.originator_ip Originator IP of message
*  @apiSuccess (700) {String} data.originator_port Originator port of message
*  @apiSuccess (700) {String} data.rtp_stat Rtp statistic of call
*  @apiSuccess (700) {String} data.type encapsulation type 
*  @apiSuccess (700) {String} data.node store node of message 
*  @apiSuccess (700) {String} data.dbnode db node type of message (single)
*  @apiSuccess (700) {String} data.trans transaction type of message (call, registration)
*  @apiSuccess (700) {String} data.proto protocol of transport
*  @apiSuccess (700) {String} data.family IP family 
*
* 
*  @apiSuccessExample Success-Response:
*	HTTP/1.1 200 OK
*	{
*	    "status": 200,
*	    "sid": "qbha61781lqpfpnodvkqbfeai4",
*	    "auth": "true",
*	    "message": "ok",
*	    "data": [
*        	{
*			"id": "14588",
*			"date": "2015-06-05 18:39:02",
*			"milli_ts": "1433529542283",
*			"micro_ts": "1433529542283432",
*			"method": "401",
*			"reply_reason": "Unauthorized",
*			"ruri": "",
*			"ruri_user": "",
*	            	"ruri_domain": "",
*			"from_user": "lab",
*	            	"from_domain": "",
*			"from_tag": "1022317138",
*	            	"to_user": "lab",
*			"to_domain": "",
*	            	"to_tag": "1d24a28a0bded6c40d31e6db8aab9ac6.5494",
*			"pid_user": "",
*	            	"contact_user": "",
*			"auth_user": "",
*	            	"callid": "426690302",
*	            	"callid_aleg": "",
*	            	"via_1": "SIP\/2.0\/UDP 192.168.1.23:5060;received=87.210.62.235;rport=5060;branch=z9hG4bK148935884",
*			"via_1_branch": "z9hG4bK148935884",
*	            	"cseq": "1521 REGISTER",
*	            	"diversion": "",
*	            	"reason": "",
*	            	"content_type": "",
*	            	"auth": "",
*	            	"user_agent": "",
*	            	"source_ip": "188.226.157.55",
*	            	"source_port": "5060",
*	            	"destination_ip": "87.210.62.235",
*	            	"destination_port": "5060",
*	            	"contact_ip": "",
*	            	"contact_port": "0",
*	            	"originator_ip": "",
*	            	"originator_port": "0",
*	            	"correlation_id": "",
*	            	"proto": "1",
*	            	"family": "2",
*			"rtp_stat": "",
*	            	"type": "2",
*			"node": "homer01:2001",
*	           	"trans": "call",
*			"dbnode": "single"
*	        }, 
*		...				
*	    ],
*	    "count": 200
*      }
*
*  @apiErrorExample Error-Response:
*	HTTP/1.1 200 OK
*	Set-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/
*	Content-Type: application/json; charset=UTF-8
*
*	{
*		"sid":"tcuass65ejl2lifoopuuurpmq7"
*	 	"auth":"false",
*		"status":"wrong-session"
*	}    		     			
**/

      ->addGetRoute('data', 'getSearchData') // => /api/v1/session
    ->done()

    /* statistic */    
    ->addSubController('statistic', 'RestApi\Statistic') //adds a new sub entry point 'tools' => admin/tools
      ->addGetRoute('method', 'getStatisticMethod') // => /api/v1/session
      ->addPostRoute('method', 'doStatisticMethod') // => /api/v1/session
      ->addPostRoute('data', 'doStatisticData') // => /api/v1/session
      ->addGetRoute('data', 'getStatisticData') // => /api/v1/session
      ->addPostRoute('ip', 'doStatisticIP') // => /api/v1/session
      ->addGetRoute('ip', 'getStatisticIP') // => /api/v1/session
      ->addPostRoute('useragent', 'doStatisticUserAgent') // => /api/v1/session
      ->addGetRoute('useragent', 'getStatisticUserAgent') // => /api/v1/session
    ->done()

    /* alarm */    
    ->addSubController('alarm', 'RestApi\Alarm') //adds a new sub entry point 'tools' => admin/tools
      ->addGetRoute('config/get', 'getAlarmConfig') // => /api/v1/session
      ->addPostRoute('config/new', 'doNewAlarmConfig') // => /api/v1/session
      ->addPostRoute('config/edit', 'doEditAlarmConfig') // => /api/v1/session
      ->addDeleteRoute('config/delete/([0-9]+)', 'deleteAlarmConfig')      
      ->addGetRoute('list/get', 'getAlarmList') // => /api/v1/session      
      ->addPostRoute('list/edit', 'doEditAlarmList') // => /api/v1/session      
      ->addGetRoute('method', 'getAlarmMethod') // => /api/v1/session
      ->addPostRoute('method', 'doAlarmMethod') // => /api/v1/session
      ->addPostRoute('ip', 'doAlarmIP') // => /api/v1/session
      ->addGetRoute('ip', 'getAlarmIP') // => /api/v1/session
      ->addPostRoute('useragent', 'doAlarmUserAgent') // => /api/v1/session
      ->addGetRoute('useragent', 'getAlarmUserAgent') // => /api/v1/session
    ->done()

    /* report */    
    ->addSubController('report', 'RestApi\Report') //adds a new sub entry point 'tools' => admin/tools
      ->addPostRoute('rtcp', 'doRTCPReport') // => /api/v1/session      
      ->addPostRoute('log', 'doLogReport') // => /api/v1/session
      ->addPostRoute('quality/([A-Za-z]+)', 'doQualityReport') // => /api/v1/session
    ->done()

    /* admin */    
    ->addSubController('admin', 'RestApi\Admin') //adds a new sub entry point 'tools' => admin/tools
      ->addGetRoute('user/get', 'getUser') // => /api/v1/session
      ->addGetRoute('user/get/([0-9A-Za-z_])', 'getUserById') // => /api/v1/session
      ->addPostRoute('user/new', 'doNewUser') // => /api/v1/session
      ->addPostRoute('user/edit', 'doEditUser') // => /api/v1/session
      ->addDeleteRoute('user/delete/([0-9]+)', 'deleteUser')      

      /* alias */          
      ->addGetRoute('alias/get', 'getAlias') // => /api/v1/session
      ->addGetRoute('user/get/([0-9A-Za-z_])', 'getAliasById') // => /api/v1/session
      ->addPostRoute('alias/new', 'doNewAlias') // => /api/v1/session
      ->addPostRoute('alias/edit', 'doEditAlias') // => /api/v1/session
      ->addDeleteRoute('user/delete/([0-9]+)', 'deleteAlias')          
      /* nodes */
      ->addGetRoute('node/get', 'getNode') // => /api/v1/session
      ->addGetRoute('node/get/([0-9A-Za-z_])', 'getNodeById') // => /api/v1/session
      ->addPostRoute('node/new', 'doNewNode') // => /api/v1/session
      ->addPostRoute('node/edit', 'doEditNode') // => /api/v1/session
      ->addDeleteRoute('node/delete/([0-9]+)', 'deleteNode')                
      /* alarms */
      ->addGetRoute('useragent', 'getAlarmUserAgent') // => /api/v1/session
    ->done()

         
    ->addSubController('profile', 'RestApi\Profile') //adds a new sub entry point 'tools' => admin/tools
      ->addPostRoute('store/([0-9A-Za-z_-]+)', 'postIdProfile')
      ->addPostRoute('store', 'postProfile')
      ->addGetRoute('store/([0-9A-Za-z_-]+)', 'getIdProfile')      
      ->addGetRoute('store', 'getProfile')
      ->addDeleteRoute('store/([0-9A-Z_-]+)', 'deleteIdProfile')      
      ->addDeleteRoute('store', 'deleteProfile')
    ->done()
    
    ->addSubController('dashboard', 'RestApi\Dashboard') //adds a new sub entry point 'tools' => admin/tools
      ->addPostRoute('store/([0-9A-Z_-]+)', 'postIdDashboard')
      ->addPostRoute('store', 'postDashboard')
      ->addPostRoute('upload', 'uploadDashboard')
      ->addGetRoute('store/1)', 'newDashboard')      
      ->addPostRoute('menu/([0-9A-Z_-]+)', 'postMenuDashboard')
      ->addGetRoute('node', 'getNode')
      ->addGetRoute('store/([0-9A-Za-z_-]+)', 'getIdDashboard')      
      ->addGetRoute('store', 'getDashboard')
      ->addDeleteRoute('store/([0-9A-Z_-]+)', 'deleteIdDashboard')      
      ->addDeleteRoute('store', 'deleteDashboard')
    ->done()
    
->run();

?>