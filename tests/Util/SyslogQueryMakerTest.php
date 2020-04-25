<?php
namespace App\Tests\Util;

use App\Entity\Origin;
use App\Repository\OriginRepository;
use App\Util\SyslogQueryMaker;
use PHPUnit\Framework\TestCase;

/**
 * Test the SyslogQueryMaker class.
 *
 *
 */
class SyslogQueryMakerTest extends TestCase
{

    /**
     * Tests it makes the query to get the remote origins as expected.
     *
     */
    public function testMakeRemoteOriginsQuery()
    {
        $syslogQueryMaker  = new SyslogQueryMaker();
        $query = $syslogQueryMaker->makeRemoteOriginsQuery();
        $this->assertEquals(
            "select * from SophosIp;",
            $query,
            "Din't get the expected query."
        );
    }

    public function testMakeRemoteLogsQueryVoid()
    {
        $syslogQueryMaker  = new SyslogQueryMaker();
        $query = $syslogQueryMaker->makeRemoteLogsQuery("2020-01-01", [], 0, 10);
        $this->assertEquals(
            "select * from SophosEvents WHERE date BETWEEN '2020-01-01 00:00:00' AND '2020-01-01 23:59:59' ORDER BY id ASC LIMIT 0, 10 ;",
            $query,
            "didn't get the expected query."
        );
    }

    public function testMakeRemoteLogsQuerySingle()
    {
        $origin1= new Origin();
        $origin1->setSubnet("198.163.100");
        $syslogQueryMaker  = new SyslogQueryMaker();
        $query = $syslogQueryMaker->makeRemoteLogsQuery("2020-01-01", [$origin1], 10, 100);
        $this->assertEquals(
            "select * from SophosEvents WHERE date BETWEEN '2020-01-01 00:00:00' AND '2020-01-01 23:59:59' AND ( LEFT(src_ip,12) = '198.163.100.' ) ORDER BY id ASC LIMIT 10, 100 ;",
            $query,
            "didn't get the expected query."
        );
    }

}
