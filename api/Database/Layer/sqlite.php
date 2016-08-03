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


namespace Database\Layer;

defined( '_HOMEREXEC' ) or die( 'Restricted access' );

class sqlite {


        /*generate a query for SQLITE and Search DATA GET */                

        function querySearchData($layerHelper) 
        {
        
		$table = $layerHelper['table']['base']."_".$layerHelper['table']['type']."_".$layerHelper['table']['timestamp'];                        
		if(isset($layerHelper['order']['by'])) {
	                $order = " ORDER BY ".$layerHelper['order']['by']." ".$layerHelper['order']['type']." LIMIT ".$layerHelper['order']['limit'];
		}
		else {
	                $order = " LIMIT ".$layerHelper['order']['limit'];
		}
                $values = implode(",", $layerHelper['values']);
                $callwhere = $layerHelper['where']['param'];                                
                
                $query  = "SELECT ".$values;
                
                if(isset($layerHelper['fields']))        
                {
                        if(isset($layerHelper['fields']['msg']) && $layerHelper['fields']['msg'] == true)
                        {
                                $query .= ",msg";                        
                        }
                                                
                        if(isset($layerHelper['fields']['md5msg']) && $layerHelper['fields']['md5msg'] == true)
                        {
                                $query .= ",MD5(msg) as md5sum";
                        }                                        
                }                
                
                if(isset($layerHelper['fields']['ts']))                        
                {
                        foreach($layerHelper['fields']['ts'] as $k=>$v) {
                                $query .= ", UNIX_TIMESTAMP(".$v['name'].") as ".$v['alias'];
                        }
                }                
                
                $query .= " FROM ".$table." as t ";
                
                if(isset($layerHelper['time']))
                {
                        $time = $layerHelper['time'];                                                          
                        $query .= " WHERE (t.date BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                        if(count($callwhere)) $query .= " AND ( " .implode(" ".$layerHelper['where']['type']." ", $callwhere). ")";
                }
                else if(count($callwhere))
                {
                        $query .= " WHERE ( " .implode(" ".$layerHelper['where']['type']." ", $callwhere). ")";
                }
                
                $query .= $order;
                                
                return $query;
        }                                    
        
        
        function queryInsertIntoData($layerHelper) 
        {
        
		$table = $layerHelper['table']['base']."_".$layerHelper['table']['type']."_".$layerHelper['table']['timestamp'];                        
		if(isset($layerHelper['order']['by'])) {
	                $order = " ORDER BY ".$layerHelper['order']['by']." ".$layerHelper['order']['type']." LIMIT ".$layerHelper['order']['limit'];
		}
		else {
	                $order = " LIMIT ".$layerHelper['order']['limit'];
		}
                $values = implode(",", $layerHelper['values']);
                $callwhere = $layerHelper['where']['param'];                                
                
                $query  = "INSERT INTO ";                        
                
                if(isset($layerHelper['table']['destination']) && isset($layerHelper['table']['destination']['db']))                      
                {
                        $query.=$layerHelper['table']['destination']['db'].".";
                }                
                
                $query .= $table. " SELECT ".$values." FROM ".$table;
                
                if(isset($layerHelper['time']))
                {
                        $time = $layerHelper['time'];                                                          
                        $query .= " WHERE (t.date BETWEEN FROM_UNIXTIME(".$time['from_ts'].") AND FROM_UNIXTIME(".$time['to_ts']."))";
                        if(count($callwhere)) $query .= " AND ( " .implode(" ".$layerHelper['where']['type']." ", $callwhere). ")";
                }
                else if(count($callwhere))
                {
                        $query .= " WHERE ( " .implode(" ".$layerHelper['where']['type']." ", $callwhere). ")";
                }
                
                $query .= $order;
                                
                return $query;
        }                                            
        
	function getExpire($now, $math, $interval, $type)
        {
                return $now." ".$math." INTERVAL ".$interval." ".$type;  
        }
}

?>
