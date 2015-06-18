define({ "api": [
  {
    "type": "post",
    "url": "/api/session",
    "title": "create a session",
    "name": "create_session",
    "group": "Auth",
    "parameter": {
      "fields": {
        "cookie": [
          {
            "group": "cookie",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "HOMERSESSID",
            "description": "<p>cookie session id</p> "
          }
        ],
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>Login for session creation.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "password",
            "description": "<p>Password for session creation.</p> "
          }
        ]
      }
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -v --cookie \"HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\" -X POST -H \"Content-Type: application/json\" \\\n-d '{\"username\":\"admin\",\"password\":\"test123\"}' \\\nhttp://localhost/api/session",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "700": [
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sid",
            "description": "<p>session UUID. This should be used in cookie request for each next API call</p> "
          },
          {
            "group": "700",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>The current status:  200, 403 (HTTP code)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "message",
            "description": "<p>The current message: &quot;ok&quot;,&quot;wrong-session&quot;</p> "
          },
          {
            "group": "700",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "auth",
            "description": "<p>Is a request authorized ?</p> "
          },
          {
            "group": "700",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "data",
            "description": "<p>response array</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.uid",
            "description": "<p>the user id</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.username",
            "description": "<p>username of User</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.gid",
            "description": "<p>gid of User</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.grp",
            "description": "<p>groups of User</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.firstname",
            "description": "<p>Firstname of User</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.lastname",
            "description": "<p>Lastname of user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.email",
            "description": "<p>User's email</p> "
          },
          {
            "group": "700",
            "type": "<p>Date</p> ",
            "optional": false,
            "field": "data.lastvisit",
            "description": "<p>Last visit</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"status\": 200,\n    \"sid\": \"tcuass65ejl2lifoopuuurpmq7\",\n    \"auth\": \"true\",\n    \"message\": \"ok\",\n    \"data\": {\n        \"uid\": \"3\",\n        \"username\": \"admin\",\n        \"gid\": \"10\",\n        \"grp\": \"users,admins\",\n        \"firstname\": \"Alexandr\",\n        \"lastname\": \"Dubovikov\",\n        \"email\": \"admin@sipcapture.org\",\n        \"lastvisit\": \"2015-06-18 08:25:55\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\tHTTP/1.1 200 OK\n\tSet-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\n\tContent-Type: application/json; charset=UTF-8\n\n\t{\n\t\t\"sid\":\"tcuass65ejl2lifoopuuurpmq7\"\n\t \t\"auth\":\"false\",\n       \t\"status\":\"wrong-session\"\n\t}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./doc.php",
    "groupTitle": "Auth"
  },
  {
    "type": "get",
    "url": "/api/session",
    "title": "get current session",
    "name": "get_session",
    "group": "Auth",
    "parameter": {
      "fields": {
        "cookie": [
          {
            "group": "cookie",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "HOMERSESSID",
            "description": "<p>cookie session id</p> "
          }
        ]
      }
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -v --cookie \"HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\" -X GET \\\n\"http://localhost/api/session\"",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "700": [
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sid",
            "description": "<p>session UUID. This should be used in cookie request for each next API call</p> "
          },
          {
            "group": "700",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>The current status:  200, 403 (HTTP code)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "message",
            "description": "<p>The current message: &quot;ok&quot;,&quot;wrong-session&quot;</p> "
          },
          {
            "group": "700",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "data",
            "description": "<p>response array</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.username",
            "description": "<p>username of user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.gid",
            "description": "<p>gid of User</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.grp",
            "description": "<p>groups of User</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "     HTTP/1.1 200 OK\n\t{\n\t    \"sid\": \"tcuass65ejl2lifoopuuurpmq7\",\n\t    \"auth\": \"true\",\n\t    \"status\": 200,\n\t    \"data\": {\n\t        \"username\": \"admin\",\n\t        \"gid\": \"10\",\n\t        \"grp\": \"users,admins\"\n\t    }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\t  HTTP/1.1 200 OK\n\t  Set-Cookie: tcuass65ejl2lifoopuuurpmq7; path=/\n\t  Content-Type: application/json; charset=UTF-8\n\n\t  {\n\t\t\"sid\":\"tcuass65ejl2lifoopuuurpmq7\"\n\t \t\"auth\":\"false\",\n       \t\"status\":\"wrong-session\"\n         }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./doc.php",
    "groupTitle": "Auth"
  },
  {
    "type": "get",
    "url": "/api/logout",
    "title": "delete current session",
    "name": "logout_of_session",
    "group": "Auth",
    "parameter": {
      "fields": {
        "cookie": [
          {
            "group": "cookie",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "HOMERSESSID",
            "description": "<p>cookie session id</p> "
          }
        ]
      }
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -v --cookie \"HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\" -X GET \\\n\"http://localhost/api/logout\"",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "700": [
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sid",
            "description": "<p>session UUID. This should be used in cookie request for each next API call</p> "
          },
          {
            "group": "700",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>The current status:  200, 403 (HTTP code)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "message",
            "description": "<p>The current message: &quot;session deleted&quot;,&quot;wrong-session&quot;</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "     HTTP/1.1 200 OK\n\t{\n\t    \"sid\": \"\",\n\t    \"auth\": \"true\",\n\t    \"status\": 200,\n\t    \"message\": \"session deleted\"\n\t}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\t  HTTP/1.1 200 OK\n\t  Set-Cookie: tcuass65ejl2lifoopuuurpmq7; path=/\n\t  Content-Type: application/json; charset=UTF-8\n\n\t  {\n\t\t\"sid\":\"tcuass65ejl2lifoopuuurpmq7\"\n\t\t\"auth\":\"false\",\n\t\t\"status\":\"wrong-session\"\n         }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./doc.php",
    "groupTitle": "Auth"
  },
  {
    "type": "post",
    "url": "/api/search/data",
    "title": "do search",
    "name": "search_data",
    "group": "Search",
    "parameter": {
      "fields": {
        "cookie": [
          {
            "group": "cookie",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "HOMERSESSID",
            "description": "<p>cookie session id</p> "
          }
        ],
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "timestamp",
            "description": "<p>array</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "timestamp.from",
            "description": "<p>search from this time</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "timestamp.to",
            "description": "<p>search to this time</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "param",
            "description": "<p>array</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "param.limit",
            "description": "<p>call limit</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "param.transaction",
            "description": "<p>array</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "param.transaction.call",
            "description": "<p>search for call transaction</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "param.transaction.registration",
            "description": "<p>search for registration transaction</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "param.transaction.rest",
            "description": "<p>search for rest transaction</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "param.search",
            "description": "<p>array</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.from_user",
            "description": "<p>From user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.to_user",
            "description": "<p>To user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.ruri_user",
            "description": "<p>Ruri user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.callid",
            "description": "<p>Callid user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.callid_aleg",
            "description": "<p>Callid of Aleg  user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.contact_user",
            "description": "<p>Contact user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.pid_user",
            "description": "<p>P-Asserted-Identity user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.auth_user",
            "description": "<p>Auth user</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.user_agent",
            "description": "<p>User-Agent</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.method",
            "description": "<p>method (INVITE, BYE)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.cseq",
            "description": "<p>Cseq of message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.reason",
            "description": "<p>Reason of Message (200, 180, 503)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.msg",
            "description": "<p>Raw message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.diversion",
            "description": "<p>Diversion</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.via_1",
            "description": "<p>First via</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.source_ip",
            "description": "<p>Source IP of message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.source_port",
            "description": "<p>Source port of message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.destination_ip",
            "description": "<p>Destination IP of message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.destination_port",
            "description": "<p>Destination port of message</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.uniq",
            "description": "<p>uniq packets</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.proto",
            "description": "<p>protocol of transport</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.family",
            "description": "<p>IP family (4, 6)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.orand",
            "description": "<p>user OR login in search</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "param.search.node",
            "description": "<p>array of nodes</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.node.id",
            "description": "<p>id of node</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "param.search.node.name",
            "description": "<p>name of node</p> "
          }
        ]
      }
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -v --cookie \"HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\" -X POST -H \"Content-Type: application/json\" \\\n-d '{\"param\":{\"transaction\":{\"call\":true},\"limit\":200,\"search\":{\"ruri_user\":\"108\"},\"node\":[{\"id\":\"1\",\"name\":\"homer01\"}]},\"timestamp\":{\"from\":1433521859738,\"to\":1433529659738}}' \\\nhttp://localhost/api/session",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "700": [
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sid",
            "description": "<p>session UUID. This should be used in cookie request for each next API call</p> "
          },
          {
            "group": "700",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>The current status:  200, 403 (HTTP code)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "message",
            "description": "<p>The current message: &quot;ok&quot;,&quot;wrong-session&quot;</p> "
          },
          {
            "group": "700",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "auth",
            "description": "<p>Is a request authorized ?</p> "
          },
          {
            "group": "700",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "data",
            "description": "<p>response array</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.id",
            "description": "<p>record id of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.date",
            "description": "<p>datetime of record</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.milli_ts",
            "description": "<p>timestamp of record in milliseconds</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.micro_ts",
            "description": "<p>timestamp of record in microseconds</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.from_user",
            "description": "<p>From user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.from_domain",
            "description": "<p>From domain</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.from_tag",
            "description": "<p>From tag</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.to_user",
            "description": "<p>To user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.to_domain",
            "description": "<p>To domain</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.to_tag",
            "description": "<p>To tag</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.ruri_user",
            "description": "<p>Ruri user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.ruri_domain",
            "description": "<p>Ruri domain</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.callid",
            "description": "<p>Callid user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.callid_aleg",
            "description": "<p>Callid of Aleg  user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.contact_user",
            "description": "<p>Contact user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.correlation_id",
            "description": "<p>Correlation ID of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.pid_user",
            "description": "<p>P-Asserted-Identity user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.auth_user",
            "description": "<p>Auth user</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.user_agent",
            "description": "<p>User-Agent</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.method",
            "description": "<p>method (INVITE, BYE)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.cseq",
            "description": "<p>Cseq of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.reply_reason",
            "description": "<p>reply reason of Message (OK, Ringing..)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.msg",
            "description": "<p>Raw message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.diversion",
            "description": "<p>Diversion</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.via_1",
            "description": "<p>First via</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.via_1_branch",
            "description": "<p>First via branch</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.source_ip",
            "description": "<p>Source IP of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.source_port",
            "description": "<p>Source port of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.destination_ip",
            "description": "<p>Destination IP of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.destination_port",
            "description": "<p>Destination port of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.contact_ip",
            "description": "<p>IP of contact</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.contact_port",
            "description": "<p>port of contact</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.originator_ip",
            "description": "<p>Originator IP of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.originator_port",
            "description": "<p>Originator port of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.rtp_stat",
            "description": "<p>Rtp statistic of call</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.type",
            "description": "<p>encapsulation type</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.node",
            "description": "<p>store node of message</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.dbnode",
            "description": "<p>db node type of message (single)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.trans",
            "description": "<p>transaction type of message (call, registration)</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.proto",
            "description": "<p>protocol of transport</p> "
          },
          {
            "group": "700",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "data.family",
            "description": "<p>IP family</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\tHTTP/1.1 200 OK\n\t{\n\t    \"status\": 200,\n\t    \"sid\": \"qbha61781lqpfpnodvkqbfeai4\",\n\t    \"auth\": \"true\",\n\t    \"message\": \"ok\",\n\t    \"data\": [\n       \t{\n\t\t\t\"id\": \"14588\",\n\t\t\t\"date\": \"2015-06-05 18:39:02\",\n\t\t\t\"milli_ts\": \"1433529542283\",\n\t\t\t\"micro_ts\": \"1433529542283432\",\n\t\t\t\"method\": \"401\",\n\t\t\t\"reply_reason\": \"Unauthorized\",\n\t\t\t\"ruri\": \"\",\n\t\t\t\"ruri_user\": \"\",\n\t            \t\"ruri_domain\": \"\",\n\t\t\t\"from_user\": \"lab\",\n\t            \t\"from_domain\": \"\",\n\t\t\t\"from_tag\": \"1022317138\",\n\t            \t\"to_user\": \"lab\",\n\t\t\t\"to_domain\": \"\",\n\t            \t\"to_tag\": \"1d24a28a0bded6c40d31e6db8aab9ac6.5494\",\n\t\t\t\"pid_user\": \"\",\n\t            \t\"contact_user\": \"\",\n\t\t\t\"auth_user\": \"\",\n\t            \t\"callid\": \"426690302\",\n\t            \t\"callid_aleg\": \"\",\n\t            \t\"via_1\": \"SIP\\/2.0\\/UDP 192.168.1.23:5060;received=87.210.62.235;rport=5060;branch=z9hG4bK148935884\",\n\t\t\t\"via_1_branch\": \"z9hG4bK148935884\",\n\t            \t\"cseq\": \"1521 REGISTER\",\n\t            \t\"diversion\": \"\",\n\t            \t\"reason\": \"\",\n\t            \t\"content_type\": \"\",\n\t            \t\"auth\": \"\",\n\t            \t\"user_agent\": \"\",\n\t            \t\"source_ip\": \"188.226.157.55\",\n\t            \t\"source_port\": \"5060\",\n\t            \t\"destination_ip\": \"87.210.62.235\",\n\t            \t\"destination_port\": \"5060\",\n\t            \t\"contact_ip\": \"\",\n\t            \t\"contact_port\": \"0\",\n\t            \t\"originator_ip\": \"\",\n\t            \t\"originator_port\": \"0\",\n\t            \t\"correlation_id\": \"\",\n\t            \t\"proto\": \"1\",\n\t            \t\"family\": \"2\",\n\t\t\t\"rtp_stat\": \"\",\n\t            \t\"type\": \"2\",\n\t\t\t\"node\": \"homer01:2001\",\n\t           \t\"trans\": \"call\",\n\t\t\t\"dbnode\": \"single\"\n\t        }, \n\t\t...\t\t\t\t\n\t    ],\n\t    \"count\": 200\n     }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\tHTTP/1.1 200 OK\n\tSet-Cookie: HOMERSESSID=tcuass65ejl2lifoopuuurpmq7; path=/\n\tContent-Type: application/json; charset=UTF-8\n\n\t{\n\t\t\"sid\":\"tcuass65ejl2lifoopuuurpmq7\"\n\t \t\"auth\":\"false\",\n       \t\"status\":\"wrong-session\"\n\t}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./doc.php",
    "groupTitle": "Search"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p> "
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./html/main.js",
    "group": "_home_shurik_web_homer_api_apidoc_html_main_js",
    "groupTitle": "_home_shurik_web_homer_api_apidoc_html_main_js",
    "name": ""
  }
] });