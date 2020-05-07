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
class WordSetAdminTest extends WebTestCase
{
    use FixturesTrait;
    
    protected $client;
    protected $wordsetId;

    protected function setUp(): void
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->client->setServerParameters([]);
        $this->wordsetId = $fixtures->getReference('wordset')->getId();
    }

    /**
     * Tests the WordSetAdmin for the basic User.
     */
    public function testWordSetAdminUser()
    {
        $this->login('user@test.com', 'testPass');
        $this->checkUserWordset($this->wordsetId);
    }

    /**
     * Tests WordSetAdmin of the ROLE_EDITOR.
     */
    public function testWordSetAdminEditor()
    {
        $this->login('editor@test.com', 'testPass');
        $this->checkEditorWordset($this->wordsetId);
    }

    /**
     * Tests WordSetAdmin of the  ROLE_ADMIN.
     */
    public function testWordSetAdminAdmin()
    {
        $this->login('admin@test.com', 'testPass');
        $this->checkAdminWordset($this->wordsetId);
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

    private function assertRedirect($destiny)
    {
        $this->assertTrue(
            $this->client->getResponse()->isRedirect()
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect($destiny)
        );
    }
    
    private function checkUserWordset($wordsetId)
    {
        $this->checkSuccess('/app/wordset/list');
        $this->check403('/app/wordset/create');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/show');
        $this->check403('/app/wordset/'.$wordsetId.'/edit');
        $this->check403('/app/wordset/'.$wordsetId.'/delete');
    }

    private function checkEditorWordset($wordsetId)
    {
        //smoke asserts!
        $this->checkSuccess('/app/wordset/list');
        $this->checkSuccess('/app/wordset/create');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/show');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/edit');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/delete');
        $this->checkCreateAndEdit();
        //now delete a wordset.
        $this->checkDelete($wordsetId);
    }

    
    private function checkAdminWordset($wordsetId)
    {
        $this->checkSuccess('/app/wordset/list');
        $this->checkSuccess('/app/wordset/create');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/show');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/edit');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/delete');
        //testing it can create a WS
        $this->checkCreateAndEdit();
        //now delete a wordset.
        $this->checkDelete($wordsetId);
    }

    private function checkCreateAndEdit()
    {
        $crawler = $this->client->request('GET', '/app/wordset/create');
        $values = $crawler->selectButton('btn_create_and_edit')->form()->getValues();
        foreach ($values as $key => $value) {
            if ((substr($key, -6) === '[name]')) {
                $values[$key] = "TestName";
            }
            if ((substr($key, -13) === '[description]')) {
                $values[$key] = "TestDesc";
            }
            if ((substr($key, -7) === '[words]')) {
                $values[$key] = "wordt";
            }
        }
        $form = $crawler->selectButton('btn_create_and_edit')->form($values);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        //assert that the new form has the fields we edited
        $this->assertEquals(
            1,
            $crawler->filter(
                'input[value=TestName]'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'textarea:contains("TestDesc")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'textarea:contains("wordt")'
            )->count()
        );

        //check it can edit
        $values = $crawler->selectButton('btn_update_and_edit')->form()->getValues();
        foreach ($values as $key => $value) {
            if ((substr($key, -6) === '[name]')) {
                $values[$key] = "TestName2";
            }
            if ((substr($key, -13) === '[description]')) {
                $values[$key] = "TestDesc2";
            }
            if ((substr($key, -7) === '[words]')) {
                $values[$key] = "";
            }
        }
        $form = $crawler->selectButton('btn_update_and_edit')->form($values);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertEquals(
            1,
            $crawler->filter(
                'input[value=TestName2]'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'textarea:contains("TestDesc2")'
            )->count()
        );
    }
    private function checkDelete($wordsetId)
    {
        $crawler = $this->client->request('GET', '/app/wordset/'.$wordsetId.'/delete');
        $this->assertResponseIsSuccessful($this->client->getResponse());
        $this->client->submitForm('Sim, eliminar');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful($this->client->getResponse());
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
