<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserTestFixtures;
use App\DataFixtures\AppExampleFixtures;
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
        $userId = $fixtures->getReference('user')->getId();
        $editorId = $fixtures->getReference('editor')->getId();
        $adminId = $fixtures->getReference('admin')->getId();
        $reportId = $fixtures->getReference('report-23-0')->getId();
        $comalaOriginId = $fixtures->getReference('comala-origin')->getId();
        $this->checkUserRoutes($userId, $editorId, $adminId);
        $logEntryId = $fixtures->getReference('comala_log_entry')->getId();
        $this->checkUserLogEntries($logEntryId);
        $this->checkUserReports($reportId);
        // now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
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

        $userId = $fixtures->getReference('user')->getId();
        $editorId = $fixtures->getReference('editor')->getId();
        $adminId = $fixtures->getReference('admin')->getId();
        $reportId = $fixtures->getReference('report-23-0')->getId();
        //check that the user with ROLE_EDITOR has no access to certain stuff.
        $this->checkEditorRoutes($userId, $editorId, $adminId);
        $logEntryId = $fixtures->getReference('comala_log_entry')->getId();
        $this->checkEditorLogEntries($logEntryId);
        $this->checkEditorReports($reportId);
        //now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
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

        $userId = $fixtures->getReference('user')->getId();
        $editorId = $fixtures->getReference('editor')->getId();
        $adminId = $fixtures->getReference('admin')->getId();
        $reportId = $fixtures->getReference('report-23-0')->getId();
                
        $this->checkAdminRoutes($userId, $editorId, $adminId);
        $logEntryId = $fixtures->getReference('macondo_log_entry')->getId();
        $this->checkAdminLogEntries($logEntryId);
        $this->checkAdminReports($reportId);
        // now logout
        $crawler = $this->client->request('GET', '/logout');
        $this->assertRedirect('http://localhost/login');
    }

    /**
     * Tests acces denied when the user is not enabled.
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

    private function checkUserRoutes($userId, $editorId, $adminId)
    {
        //********** USER RELATED**********
        //has no access!
        $this->check403('/admin_sonata_user_user/list');
        $this->check403('/admin_sonata_user_user/create');
        $this->check403('/admin_sonata_user_user/'.$editorId.'/show');
        $this->check403('/admin_sonata_user_user/'.$editorId.'/edit');    
        $this->check403('/admin_sonata_user_user/'.$editorId.'/delete');
        $this->check403('/admin_sonata_user_user/'.$adminId.'/show');
        $this->check403('/admin_sonata_user_user/'.$adminId.'/edit');    
        $this->check403('/admin_sonata_user_user/'.$adminId.'/delete');
        $this->check403('/admin_sonata_user_user/'.$userId.'/delete');    
        $this->check403('/sonata/user/group/list');
        $this->check403('/sonata/user/group/create');          
        $this->check403('/sonata/user/group/export');
        //has access
        $this->checkSuccess('/admin_sonata_user_user/'.$userId.'/edit');
    }
    
    private function checkUserLogEntries($logEntryId)
    {
        $this->checkSuccess('/app/logentry/list');
        $crawler = $this->client->request('GET', '/app/logentry/list');
        $this->assertEquals(
            5,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            3,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            8,
            $crawler->filter(
                'tbody tr'
            )->count()
            );
        $this->checkSuccess('/app/logentry/'.$logEntryId.'/show');
    }

    private function checkEditorLogEntries($logEntryId)
    {
        $this->checkSuccess('/app/logentry/list');
        $crawler = $this->client->request('GET', '/app/logentry/list');
        $this->assertEquals(
            5,
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
            5,
            $crawler->filter(
                'tbody tr'
            )->count()
            );
        $this->checkSuccess('/app/logentry/'.$logEntryId.'/show');
    }

    private function checkAdminLogEntries($logEntryId)
    {
        $this->checkSuccess('/app/logentry/list');
        $crawler = $this->client->request('GET', '/app/logentry/list');
        $this->assertEquals(
            0,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            3,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            3,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->checkSuccess('/app/logentry/'.$logEntryId.'/show');
    }

    private function checkUserReports($reportId)
    {
        $this->checkSuccess('/app/report/list');
        $crawler = $this->client->request('GET', '/app/report/list');
        $this->assertEquals(
            2,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            2,
            $crawler->filter(
                'td:contains("Macondo")'
            )->count()
        );
        $this->assertEquals(
            4,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->checkSuccess('/app/report/'.$reportId.'/show');
        $this->checkSuccess('/app/report/'.$reportId.'/summary');
        $this->check403('/app/report/'.$reportId.'/delete');
    }

    private function checkEditorReports($reportId)
    {
        $this->checkSuccess('/app/report/list');
        $crawler = $this->client->request('GET', '/app/report/list');
        $this->assertEquals(
            2,
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
            2,
            $crawler->filter(
                'tbody tr'
            )->count()
        );
        $this->checkSuccess('/app/report/'.$reportId.'/show');
        $this->check403('/app/report/'.$reportId.'/delete');
    }

    private function checkAdminReports($reportId)
    {
        $this->checkSuccess('/app/report/list');
        $crawler = $this->client->request('GET', '/app/report/list');
        $this->assertEquals(
            0,
            $crawler->filter(
                'td:contains("Comala")'
            )->count()
        );
        $this->assertEquals(
            2,
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
        $this->checkSuccess('/app/report/'.$reportId.'/show');
        $this->checkSuccess('/app/report/'.$reportId.'/delete');
    }
    private function checkEditorRoutes($userId, $editorId, $adminId)
    {
        //has no access!
        $this->check403('/admin_sonata_user_user/list');
        $this->check403('/admin_sonata_user_user/create');
        $this->check403('/admin_sonata_user_user/'.$userId.'/show');
        $this->check403('/admin_sonata_user_user/'.$userId.'/edit');    
        $this->check403('/admin_sonata_user_user/'.$userId.'/delete');
        $this->check403('/admin_sonata_user_user/'.$adminId.'/show');
        $this->check403('/admin_sonata_user_user/'.$adminId.'/edit');    
        $this->check403('/admin_sonata_user_user/'.$adminId.'/delete');
        $this->check403('/admin_sonata_user_user/'.$editorId.'/delete');    
        $this->check403('/sonata/user/group/list');
        $this->check403('/sonata/user/group/create');          
        $this->check403('/sonata/user/group/export');
        //has access
        $this->checkSuccess('/admin_sonata_user_user/'.$editorId.'/edit');

    }


    private function checkAdminRoutes($userId, $editorId, $adminId)
    {
        $this->checkSuccess('/admin_sonata_user_user/list');
        $this->checkSuccess('/admin_sonata_user_user/create');
        $this->checkSuccess('/admin_sonata_user_user/'.$userId.'/show');
        $this->checkSuccess('/admin_sonata_user_user/'.$userId.'/edit');    
        $this->checkSuccess('/admin_sonata_user_user/'.$userId.'/delete');
        $this->checkSuccess('/admin_sonata_user_user/'.$editorId.'/show');
        $this->checkSuccess('/admin_sonata_user_user/'.$editorId.'/edit');
        $this->checkSuccess('/admin_sonata_user_user/'.$editorId.'/delete');
        $this->checkSuccess('/admin_sonata_user_user/'.$adminId.'/show');
        $this->checkSuccess('/admin_sonata_user_user/'.$adminId.'/edit');
        $this->checkSuccess('/admin_sonata_user_user/'.$adminId.'/delete');
        $this->check403('/sonata/user/group/list');
        $this->check403('/sonata/user/group/create');          
        $this->check403('/sonata/user/group/export');
        //has access

        
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
