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

function __autoload($className) {
    
    $className = str_replace("\\", "/", $className);
    $path = $className . '.php';
    if (file_exists($path)) require $path;
    else throw new Exception("Unable to load $className.");
}


function getVar($name, $default, $request, $type) {        

        $val = isset($request[$name]) ? $request[$name] : $default;                     

        switch(strtolower($type))
        {
                case "int":
                    //$val = intval($val);
                    $val = $val + 0;
                    break;

                case "long":
                    $val = $val + 0;
                    break;                    

                case "float":
                    $val = floatval($val);
                    break;                    

                case "bool":
                    $val = !! $val;
                    break;

                case "string":
                    if(get_magic_quotes_gpc()) $val = stripslashes($val);
                    $val = strval($val);                                     
                    break;
                    
                case "datetime":
                    $val = gmdate("Y-m-d H:i:s", strtotime($val));                    
                    break;                    

                case "date":
                    $val = gmdate("Y-m-d", strtotime($val));                    
                    break;                    

                case "time":
                    $val = gmdate("H:i:s", strtotime($val));                    
                    break;                                        

                case "milliseconds":
                    $val = gmdate("Y-m-d H:i:s", intval($val)/1000);                    
                    break;              
                default:
                    break;
        }
                  
        return $val;
}

function generateWhere ($search, $and_or, $db, $skip_keys = array()) {
           $s = 0;

           $mydb = $db;

           $callwhere = array();
           $b2b = 0;

           // Prepare WHERE PARAMS
           foreach($search as $key=>$value) {
                   if(in_array($key, $skip_keys)) continue;

                   // SKIP EMPTY VALUES
                   if($value == NULL || $value == "" || $value == -1) continue;

                   if($key == "callid" && $b2b ) $callwhere[] =" (";

                   $eqlike = preg_match("/%/", $value) ? " like " : " = ";

                   if(preg_match("/^!/", $value)) {
                       $value =  preg_replace("/^!/", "", $value);
                       $eqlike = "!=";
                   }

                   /* Array search */
                   if(preg_match("/;/", $value)) {
                      $dda = array();
                      foreach(preg_split("/;/", $value) as $k=>$v) $dda[] = "`".$key."`".$eqlike.$mydb->quote($v);
                      $callwhere[] = "( ". ($eqlike == " = ") ? implode(" OR ",$dda) : implode(" AND ",$dda)." )";
                   }
                   else {
                       $mkey = "`".$key."`";                                              
                       //if($s == 1) $callwhere[] = $and_or;                       
                       $callwhere[] = $mkey.$eqlike.$mydb->quote($value);
                   }
                   
                   if($key == "callid" && $b2b) $callwhere[] = " OR callid_aleg ".$eqlike.$mvalue;

                   $s = 1;
           }
 
           return $callwhere;
}

function sendFile($status = 200, $message = "OK",$filename,  $filesize,  $body)
{
                header('HTTP/1.1 ' . $status . ' '. $message );
                header('Content-type: application/octet-stream');
                header('Content-Disposition: filename="'.$filename.'"');
                header('Content-length: '.$filesize);
                header('Cache-Control: no-cache, must-revalidate');
                header('Access-Control-Allow-Origin: http://homer5.org/');
                header('Access-Control-Max-Age: 3600');
                header('Server: Homer 5 REST');                                         
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

                echo $body;

                exit;           
}


?>
