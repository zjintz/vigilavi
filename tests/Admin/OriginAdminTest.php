<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserTestFixtures;
use App\DataFixtures\AppExampleFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Functional tests of the security aspects of the system.
 *
 */
class OriginAdminTest extends WebTestCase
{
    use FixturesTrait;
    
    protected $client;
    protected $comalaOriginId;
    protected $macondoOriginId;

    protected function setUp(): void
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->client->setServerParameters([]);
        $this->comalaOriginId = $fixtures->getReference('comala-origin')->getId();
        $this->macondoOriginId = $fixtures->getReference('macondo-origin')->getId();
    }

    /**
     * Tests the OriginAdmin for the basic User.
     */
    public function testOriginAdminUser()
    {
        $this->login('user@test.com', 'testPass');
        $this->checkUserOrigins($this->comalaOriginId);
    }

    /**
     * Tests the OriginAdmin for the Editor role.
     */
    public function testOriginAdminEditor()
    {
        $this->login('editor@test.com', 'testPass');
        $this->checkEditorOrigins($this->macondoOriginId);
    }

    /**
     * Tests the OriginAdmin for the Admin role.
     */
    public function testOriginAdminAdmin()
    {
        $this->login('admin@test.com', 'testPass');
        $this->checkAdminOrigins($this->macondoOriginId);
    }
    
    private function login($username, $password)
    {
        $this->client->request('GET', '/');
        $crawler = $this->client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => $username,
            '_password'  => $password,
        ));
        $this->client->submit($form);
        $this->assertRedirect('http://localhost/');
        $this->client->followRedirect();
    }

    private function checkUserOrigins($originId)
    {
        $this->checkSuccess('/app/origin/list');
        $crawler = $this->client->request('GET', '/app/origin/list');
        $this->assertEquals(
            1,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            2,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->check403('/app/origin/'.$originId.'/edit');
        $this->checkSuccess('/app/origin/'.$originId.'/show');    
    }

    private function checkEditorOrigins($originId)
    {
        $this->checkSuccess('/app/origin/list');
        $crawler = $this->client->request('GET', '/app/origin/list');
        $this->assertEquals(
            1,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            0,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->check403('/app/origin/'.$originId.'/edit');
        $this->checkSuccess('/app/origin/'.$originId.'/show');
    }

    private function checkAdminOrigins($originId)
    {
        $this->checkSuccess('/app/origin/list');
        $crawler = $this->client->request('GET', '/app/origin/list');
        $this->assertEquals(
            0,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->checkSuccess('/app/origin/'.$originId.'/edit');
        $this->checkSuccess('/app/origin/'.$originId.'/show');
    }

    private function assertRedirect($destiny)
    {
        $this->assertTrue(
            $this->client->getResponse()->isRedirect()
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect($destiny)
        );
    }
    
    private function checkSuccess($route)
    {
        $this->client->request('GET', $route);
        $this->assertResponseIsSuccessful($this->client->getResponse());
    }
    private function check403($route)
    {
        $this->client->request('GET', $route);
        $this->assertResponseStatusCodeSame(
            403,
            $this->client->getResponse()->getStatusCode()
        );
    }

}
