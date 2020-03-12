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

    /**
     * Tests the basic login and logout mechanics.
     * This also tests the allowed routes of the basic User.
     */
    public function testLoginUser()
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        $this->client = static::createClient();
        $this->client->setServerParameters([]);

        //now login:
        $crawler = $this->client->request('GET', '/');
        $crawler=$this->client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'user@test.com',
            '_password'  => 'testPass',
        ));
        $this->client->submit($form);
        $this->assertRedirect('http://localhost/');
        $crawler = $this->client->followRedirect();
        //check that the user with ROLE_USER has no access to certain stuff.
        $wordsetId = $fixtures->getReference('wordset')->getId();
        $this->checkUserWordset($wordsetId);
    }

    /**
     * Tests allowed routes of the ROLE_EDITOR.
     */
    public function testLoginEditor()
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
                        
        $this->client = static::createClient();
        $this->client->setServerParameters([]);
        //now login:
        $crawler = $this->client->request('GET', '/');
        $crawler=$this->client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'editor@test.com',
            '_password'  => 'testPass',
        ));
        $this->client->submit($form);
        $this->assertRedirect('http://localhost/');
        $crawler = $this->client->followRedirect();
        $wordsetId = $fixtures->getReference('wordset')->getId();
        //check that the user with ROLE_EDITOR has no access to certain stuff.
        $this->checkEditorWordset($wordsetId);
    }

    /**
     * Tests allowed routes of the basic ROLE_ADMIN.
     */
    public function testLoginAdmin()
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        $this->client = static::createClient();
        $this->client->setServerParameters([]);

        //now login:
        $crawler = $this->client->request('GET', '/');
        $crawler=$this->client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'admin@test.com',
            '_password'  => 'testPass',
        ));
        $this->client->submit($form);
        $this->assertRedirect('http://localhost/');
        $crawler = $this->client->followRedirect();
        $wordsetId = $fixtures->getReference('wordset')->getId();
        $this->checkAdminWordset($wordsetId);
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

        //testing it can create a WS
        $crawler = $this->client->request('GET','/app/wordset/create');
        $values = $crawler->selectButton('btn_create_and_edit')->form()->getValues();
        foreach ($values as $key=>$value) {
            if ((substr($key, -6) === '[name]')) {
                $values[$key] = "TestName";
            }
            if ((substr($key, -13) === '[description]')) {
                $values[$key] = "TestDesc";
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

        //check it can edit
        $values = $crawler->selectButton('btn_update_and_edit')->form()->getValues();
        foreach ($values as $key=>$value) {
            if ((substr($key, -6) === '[name]')) {
                $values[$key] = "TestName2";
            }
            if ((substr($key, -13) === '[description]')) {
                $values[$key] = "TestDesc2";
            }
            if ((substr($key, -7) === '[words]')) {
                $values[$key] = "w1\nw2";
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
        $this->assertEquals(
            1,
            $crawler->filter(
                'textarea:contains("w1")'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'textarea:contains("w2")'
            )->count()
        );
        //now add some words.
        
    }
    private function checkAdminWordset($wordsetId)
    {
        $this->checkSuccess('/app/wordset/list');
        $this->checkSuccess('/app/wordset/create');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/show');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/edit');
        $this->checkSuccess('/app/wordset/'.$wordsetId.'/delete');

                //testing it can create a WS
        $crawler = $this->client->request('GET','/app/wordset/create');
        $values = $crawler->selectButton('btn_create_and_edit')->form()->getValues();
        foreach ($values as $key=>$value) {
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
        foreach ($values as $key=>$value) {
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
        //now add some words.
    }

    private function checkSuccess($route){
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
