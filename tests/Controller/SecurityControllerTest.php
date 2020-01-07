<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserTestFixtures;
use App\DataFixtures\UserNotEnabledTestFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Functional tests of the security aspects of the system.
 *
 */
class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;
    
    protected $client;
    /**
     * Anonymous (unauthenticated) users should be always redirected to the login page.
     *
     * @return void
     */
    public function testAnonAccess()
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/dashboard');
        $this->assertRedirect('http://localhost/login');
        $crawler = $this->client->followRedirect();
        $this->assertLoginContent($crawler);
        $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
        $crawler = $this->client->followRedirect();
        $this->assertLoginContent($crawler);
        $this->client->request('GET', '/noexiste');
        $this->assertTrue($this->client->getResponse()->isNotFound());

    }

    /**
     * When the credentials user or pasword fail the security system have to
     * redirect the user to the login page. And of course deny access. 
     *
     *
     * @return void
     */
    public function testFailedLogin()
    {
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/login');
        $this->assertLoginContent($crawler);
        $form = $crawler->selectButton('Entrar')->form();
        //trying with wrong credentials
        $form['_username'] = 'fakeadmin';
        $form['_password'] = 'fakeadmin';
        $crawler = $this->client->submit($form);
        $this->assertRedirect('http://localhost/login');

    }

    /**
     * Tests the basic login and logout mechanics.
     * This also tests the allowed routes of the basic User.
     */
    public function testLoginUser()
    {
        $this->loadFixtures([UserTestFixtures::class]);
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
        $this->checkUserRoutes();
        // now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
    }

    /**
     * Tests allowed routes of the basic ROLE_ADMIN.
     */
    public function testLoginAdmin()
    {
        $this->loadFixtures([UserTestFixtures::class]);
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
        //check that the user with ROLE_USER has no access to certain stuff.
        $this->checkAdminRoutes();
        // now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
    }

    /**
     * Tests allowed routes of the ROLE_EDITOR.
     */
    public function testLoginEditor()
    {
        $this->loadFixtures([UserTestFixtures::class]);
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
        //check that the user with ROLE_USER has no access to certain stuff.
        $this->checkEditorRoutes();
        // now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
    }

    /**
     * Tests acces denied when the user is not neabled.
     *
     */
    public function testNotEnabled()
    {
        $this->loadFixtures([UserNotEnabledTestFixtures::class]);
        $this->client = static::createClient();
        $this->client->setServerParameters([]);

        //now login:
        $crawler = $this->client->request('GET', '/');
        $crawler=$this->client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'userne@test.com',
            '_password'  => 'testnePass',
        ));
        $this->client->submit($form);
        $this->assertRedirect('http://localhost/login');
        $crawler = $this->client->followRedirect();                
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
    
    private function assertLoginContent($crawler)
    {
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(
            1,
            $crawler->filter(
                'p:contains("Autenticação")'
            )->count()
        );
        $this->assertEquals(
            4,
            $crawler->filter(
                'input'
            )->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter(
                'button:contains("Entrar")'
            )->count()
        );
    }

    private function checkUserRoutes()
    {
        //has no access!
        $this->check403('/admin_sonata_user_user/list');
        $this->check403('/admin_sonata_user_user/create');
        //   $this->check403('/admin_sonata_user_user/2/show');
        // $this->check403('/admin_sonata_user_user/2/edit');    
        $this->check403('/admin_sonata_user_user/2/delete');
        $this->check403('/admin_sonata_user_user/export');
        $this->check403('/sonata/user/group/list');
        $this->check403('/sonata/user/group/create');          
        $this->check403('/sonata/user/group/export');
        $this->checkSuccess('/app/liturgy/1/show');
        $this->checkSuccess('/app/liturgy/list');
        $this->check403('/app/liturgy/create');        
        $this->check403('/app/liturgy/1/edit');
        $this->check403('/app/liturgy/1/delete');
        $this->checkSuccess('/liturgy_text/assemble');
    }

    private function checkEditorRoutes()
    {
        //has no access!
        $this->check403('/admin_sonata_user_user/list');
        $this->check403('/admin_sonata_user_user/create');
        //        $this->check403('/admin_sonata_user_user/2/show');
        //        $this->check403('/admin_sonata_user_user/2/edit');    
        $this->check403('/admin_sonata_user_user/2/delete');
        $this->check403('/admin_sonata_user_user/export');
        $this->check403('/sonata/user/group/list');
        $this->check403('/sonata/user/group/create');          
        $this->check403('/sonata/user/group/export');
        $this->checkSuccess('/app/liturgy/1/show');
        $this->checkSuccess('/app/liturgy/list');
        $this->checkSuccess('/app/liturgy/create');        
        $this->checkSuccess('/app/liturgy/1/edit');
        $this->checkSuccess('/app/liturgy/1/delete');
        $this->checkSuccess('/liturgy_text/assemble');
    }


    private function checkAdminRoutes()
    {
        //has no access!
        $this->checkSuccess('/admin_sonata_user_user/list');
        $this->checkSuccess('/admin_sonata_user_user/create');
        $this->checkSuccess('/admin_sonata_user_user/1/show');
        $this->checkSuccess('/admin_sonata_user_user/1/edit');    
        $this->checkSuccess('/admin_sonata_user_user/1/delete');
        /*        $this->checkSuccess('/sonata/user/group/list');
        $this->checkSuccess('/sonata/user/group/create');          
        $this->checkSuccess('/sonata/user/group/export');*/
        $this->checkSuccess('/app/liturgy/1/show');
        $this->checkSuccess('/app/liturgy/list');
        $this->checkSuccess('/app/liturgy/create');        
        $this->checkSuccess('/app/liturgy/1/edit');
        $this->checkSuccess('/app/liturgy/1/delete');
        $this->checkSuccess('/liturgy_text/assemble');
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
