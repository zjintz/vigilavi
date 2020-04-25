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
    protected $queryMaker;
    
    public function __construct(
        string $sophosDNS,
        string $sophosUser,
        string $sophosPass,
        SyslogQueryMaker $queryMaker
    ) {

        $this->sophosDNS = $sophosDNS;
        $this->sophosUser = $sophosUser;
        $this->sophosPass = $sophosPass;
        $this->options =  [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->queryMaker = $queryMaker;
    }
    
    /**
     * Collects the data related to the origins .
     *
     */
    public function getRemoteOrigins()
    {
        return $this->doQuery($this->queryMaker->makeRemoteOriginsQuery());
    }

    public function getRemoteLogs($dateLog, $origins, $start, $end)
    {
        return $this->doQuery(
            $this->queryMaker->makeRemoteLogsQuery($dateLog, $origins, $start, $end)
        );
    }

    protected function doQuery($query)
    {
        echo $query;
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
