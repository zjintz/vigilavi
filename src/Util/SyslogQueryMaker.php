<?php

namespace App\Util;

/**
 * \brief     Makes Sql queries. 
 *
 *
 */
class SyslogQueryMaker
{

    /**
     * Query to fetch all the origins in the Syslog database.
     *
     *
     */
    public function makeRemoteOriginsQuery()
    {
        $query = "select * from SophosIp;";
        return $query;
    }

    public function makeRemoteLogsQuery($dateLog, $origins, $start, $offset)
    {
        $querySuffix = " ORDER BY id ASC LIMIT $start, $offset ;";
        $query = "select * from SophosEvents WHERE date BETWEEN '".$dateLog." 00:00:00' AND '".$dateLog." 23:59:59'";
        $firstOrigin = true;
        if (empty($origins)) {
            return $query.$querySuffix;
        }
        $query = $query." AND (";
        
        foreach ($origins as $origin) {
            $subnet = $origin->getSubnet().".";
            $len = strlen($subnet);
            if ($firstOrigin) {
                $query = $query." LEFT(src_ip,".$len.") = '".$subnet."' ";
                $firstOrigin = false;
                continue;
            }
            $query = $query."OR LEFT(src_ip,".$len.") = '".$subnet."' ";
        }
        $query = $query . ")";
        $query = $query.$querySuffix;
        return $query;
    }
}
