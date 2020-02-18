<?php

namespace App\Util;

use \PDO;

/**
 * \brief     Collects data from the syslog database.
 *
 *
 */
class SyslogDBCollector
{

    protected $sophosDNS;
    protected $sophosUser;
    protected $sophosPass;
    protected $options;
    
    public function __construct(
        string $sophosDNS,
        string $sophosUser,
        string $sophosPass
    ) {

        $this->sophosDNS = $sophosDNS;
        $this->sophosUser = $sophosUser;
        $this->sophosPass = $sophosPass;
        $this->options =  [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    }
    
    /**
     * Collects the data related to the origins .
     *
     */
    public function getRemoteOrigins()
    {
        $query = "select * from SophosIp";
        return $this->doQuery($query);
    }

    public function getRemoteLogs($dateLog, $subnet)
    {
        $query = "select * from SophosEvents LIMIT 5";
        return $this->doQuery($query);
    }
    
    protected function doQuery($query)
    {
        try {
            $connection = new \PDO(
                $this->sophosDNS, $this->sophosUser, $this->sophosPass,
                $this->options
            );
            $result = $connection->query($query);
            $data = [];
            while ($row = $result->fetch()) {
                $data[] = $row;
            }
            return $data;
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    

}
