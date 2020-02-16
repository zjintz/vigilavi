<?php
namespace App\Tests\Util;

use App\Entity\Origin;
use App\Util\OriginSynchronizer;
use PHPUnit\Framework\TestCase;

class OriginSynchronizerTest extends TestCase
{

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when 
     * is nothing in 
     * the vigiliavi DB, and nothing in the syslog DB.
     *
     */
    public function testSyncOriginsEmptyEmpty()
    {
        $oSynchronizer = new OriginSynchronizer();
        $response = $oSynchronizer->syncOrigins([], []);
        $this->assertEquals(
            [
                'entities' => [],
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' =>0,
                    'active_origins'=>0,
                    'inactive_origins' =>0,
                    'total_origins' =>0]
            ],
            $response,
            "Erroneus output from the syncOrigins function.");
    }

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when there 
     * are entities in the vigilavi DB, and nothing in the syslog DB.
     *
     * In this case it should set the active field to false of every local entity.
     */
    public function testSyncOriginsFullEmpty()
    {
        $oSynchronizer = new OriginSynchronizer();
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'origin-A', true);
        $origins[]= $this->makeOrigin("192.168.2", 'origin-B', true);
        $origins[]= $this->makeOrigin("192.168.3", 'origin-C', true);
        $response = $oSynchronizer->syncOrigins($origins, []);
        $outputOrigins = [];
        $outputOrigins[]= $this->makeOrigin("192.168.1", 'origin-A', false);
        $outputOrigins[]= $this->makeOrigin("192.168.2", 'origin-B', false);
        $outputOrigins[]= $this->makeOrigin("192.168.3", 'origin-C', false);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' =>3,
                    'active_origins'=>0,
                    'inactive_origins' =>3,
                    'total_origins' =>3
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
        //Another assert adding more active  origins.
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'origin-A', true);
        $origins[]= $this->makeOrigin("192.168.2", 'origin-B', true);
        $origins[]= $this->makeOrigin("192.168.3", 'origin-C', true);
        $origins[]= $this->makeOrigin("192.168.4", 'origin-D', true);
        $origins[]= $this->makeOrigin("192.168.5", 'origin-E', true);
        $response = $oSynchronizer->syncOrigins($origins, []);
        $outputOrigins[]= $this->makeOrigin("192.168.4", 'origin-D', false);
        $outputOrigins[]= $this->makeOrigin("192.168.5", 'origin-E', false);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' =>5,
                    'active_origins'=>0,
                    'inactive_origins' =>5,
                    'total_origins' =>5
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );

        //Another assert mixing local inactive  origins.
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'origin-A', true);
        $origins[]= $this->makeOrigin("192.168.2", 'origin-B', true);
        $origins[]= $this->makeOrigin("192.168.3", 'origin-C', true);
        $origins[]= $this->makeOrigin("192.168.4", 'origin-D', true);
        $origins[]= $this->makeOrigin("192.168.5", 'origin-E', true);
        $origins[]= $this->makeOrigin("192.168.6", 'origin-F', false);
        $origins[]= $this->makeOrigin("192.168.7", 'origin-G', false);
        $response = $oSynchronizer->syncOrigins($origins, []);
        $outputOrigins[]= $this->makeOrigin("192.168.6", 'origin-F', false);
        $outputOrigins[]= $this->makeOrigin("192.168.7", 'origin-G', false);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' =>5,
                    'active_origins'=>0,
                    'inactive_origins' =>7,
                    'total_origins' =>7
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
    }

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * the vigilavi DB is empty, but origin data  in the syslog DB.
     *
     * In this case it should create Origin entities, for each active entry.
     */
    public function testSyncOriginsEmptyFull()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.3', 'nomeSede'=> 'sedeC', 'red'=>1];
        $response = $oSynchronizer->syncOrigins([],$remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $outputOrigins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
        $outputOrigins[]= $this->makeOrigin("192.168.3", 'sedeC', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 3,
                    'modified_origins' => 0,
                    'active_origins'=> 3,
                    'inactive_origins' =>0,
                    'total_origins' =>3
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
        //Another assert adding more remote  origins.
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.3', 'nomeSede'=> 'sedeC', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.4', 'nomeSede'=> 'sedeD', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.5', 'nomeSede'=> 'sedeE', 'red'=>1];
        $response = $oSynchronizer->syncOrigins([],$remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $outputOrigins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
        $outputOrigins[]= $this->makeOrigin("192.168.3", 'sedeC', true);
        $outputOrigins[]= $this->makeOrigin("192.168.4", 'sedeD', true);
        $outputOrigins[]= $this->makeOrigin("192.168.5", 'sedeE', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 5,
                    'modified_origins' =>0,
                    'active_origins'=>5,
                    'inactive_origins' =>0,
                    'total_origins' =>5
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
        
        //Another assert mixing remote inactive  origins.
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.3', 'nomeSede'=> 'sedeC', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.4', 'nomeSede'=> 'sedeD', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.5', 'nomeSede'=> 'sedeE', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.6', 'nomeSede'=> 'sedeF', 'red'=>0];
        $remoteOrigins[]= ['subnet'=>'192.168.7', 'nomeSede'=> 'sedeG', 'red'=>0];
        $response = $oSynchronizer->syncOrigins([],$remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $outputOrigins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
        $outputOrigins[]= $this->makeOrigin("192.168.3", 'sedeC', true);
        $outputOrigins[]= $this->makeOrigin("192.168.4", 'sedeD', true);
        $outputOrigins[]= $this->makeOrigin("192.168.5", 'sedeE', true);
                
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 5,
                    'modified_origins' =>0,
                    'active_origins'=>5,
                    'inactive_origins' =>0,
                    'total_origins' =>5
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
            );
    }


    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * the vigilavi DB and the syslog DB have the same origins and are all active.
     *
     * In this case: The synchronizer will return no entities since it has nothing
     * to create or modify.  
     */
    public function testSyncOriginsFullFullActive()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 0,
                    'active_origins'=> 1,
                    'inactive_origins' =>0,
                    'total_origins' =>1
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );

        //Another asertion adding more entities.
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $origins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 0,
                    'active_origins'=> 2,
                    'inactive_origins' =>0,
                    'total_origins' =>2
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );

    }


    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * there is a new name for an Origin in the remote BD.
     *
     * In this case: It should return the entities with the new names.
     */
    public function testSyncOriginsNewName()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'Nueva Sede', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[] = $this->makeOrigin("192.168.1", 'Nueva Sede', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 1,
                    'active_origins'=> 1,
                    'inactive_origins' =>0,
                    'total_origins' =>1
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );

        //Another asertion adding more entities.
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $origins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 0,
                    'active_origins'=> 2,
                    'inactive_origins' =>0,
                    'total_origins' =>2
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );

    }

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * there are  Origins in the remote BD that switched the active status.
     *
     * In this case: It should return the modified entities.
     */
    public function testSyncOriginsInactive()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'Nueva Sede', 'red'=>0];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'Nueva Sede', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[] = $this->makeOrigin("192.168.1", 'Nueva Sede', false);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 1,
                    'active_origins'=> 0,
                    'inactive_origins' =>1,
                    'total_origins' =>1
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
    }

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * there are  Origins in the remote BD that are not in the local DB
     *
     * In this case: It should return the new entities that have to be created.
     */
    public function testSyncOriginsNewEntity()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'old-O', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'new-O', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'old-O', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[] = $this->makeOrigin("192.168.2", 'new-O', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 1,
                    'modified_origins' => 0,
                    'active_origins'=> 2,
                    'inactive_origins' =>0,
                    'total_origins' =>2
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
        //ANOTHER ASSERT: in this case an inactive remote origin.
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'old-O', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'new-O', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.3', 'nomeSede'=> 'new-inactive', 'red'=>0];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'old-O', true);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[] = $this->makeOrigin("192.168.2", 'new-O', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 1,
                    'modified_origins' => 0,
                    'active_origins'=> 2,
                    'inactive_origins' =>0,
                    'total_origins' =>2
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
    }

    /**
     * Tests the syncOrigins function from the OriginSynchronizer when  
     * there are  Origins in the remote BD that are active, but the local orgin
     * is not active.
     *
     * In this case: It should return the modified entities.
     */
    public function testSyncOriginsActivateOrigin()
    {
        $oSynchronizer = new OriginSynchronizer();
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'old-O', 'red'=>1];
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'old-O', false);
    
        $response = $oSynchronizer->syncOrigins($origins, $remoteOrigins);
        $outputOrigins = [];
        $outputOrigins[] = $this->makeOrigin("192.168.1", 'old-O', true);
        $this->assertEquals(
            [
                'entities' => $outputOrigins,
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' => 1,
                    'active_origins'=> 1,
                    'inactive_origins' =>0,
                    'total_origins' =>1
                ]
            ],
            $response,
            "Erroneus output from the syncOrigins function."
        );
        //ANOTHER ASSERT: in this case an inactive remote origin.

    }

    protected function makeOrigin($subnet, $name, $active)
    {
        $origin = new Origin();
        $origin->setSubnet($subnet);
        $origin->setName($name);
        $origin->setActive($active);
        return $origin;
    }
}
