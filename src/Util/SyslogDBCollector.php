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
    public function __construct(
        string $sophosDNS,
        string $sophosUser,
        string $sophosPass) {

        $this->sophosDNS = $sophosDNS;
        $this->sophosUser = $sophosUser;
        $this->sophosPass = $sophosPass;
    }
    
    /**
     * Collects the data related to the origins .
     *
     */
    public function getRemoteOrigins()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        

        try {
            $connection = new \PDO("mysql:host=200.160.126.57;dbname=Syslog", "root", "Ar4u705scj", $options);
            $query = "select * from SophosIp";
            $result = $connection->query($query);
            while ($row = $result->fetch())
            {
                var_dump($row);
            }
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
       
    }

    public function getRemoteLogs()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $connection = new \PDO($this->sophosDNS, $this->sophosUser, $this->sophosPass, $options);
            $query = "select * from SophosEvents LIMIT 5";
            $result = $connection->query($query);
            while ($row = $result->fetch())
            {
                var_dump($row);
            }
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
       
    }

    

}
