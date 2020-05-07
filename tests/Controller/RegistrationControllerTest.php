<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Application\Sonata\UserBundle\Entity\User;

/**
 * Functional tests of the user registration.
 *
 */
class RegistrationControllerTest extends WebTestCase
{

    use FixturesTrait;
    
    protected $client;

    /**
     * Anonymous (unauthenticated) users can get to the registration page of course.
     *
     * @return void
     */
    public function testRegister()
    {
        $this->client = static::createClient();
        $this->loadFixtures();
        $crawler =$this->client->request('GET', '/register/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Registar')->form();
        // set some values
        $form['fos_user_registration_form[firstName]'] = 'name';
        $form['fos_user_registration_form[lastName]'] = 'lm';
        $form['fos_user_registration_form[plainPassword][first]'] = '1111111';
        $form['fos_user_registration_form[plainPassword][second]'] = '1111111';
        $form['fos_user_registration_form[email]'] = 'test@no.com';
        $form['fos_user_registration_form[headquarter][name]'] = 'hqname';
        $form['fos_user_registration_form[headquarter][city]'] = 'hqcity';
        $form['fos_user_registration_form[headquarter][country]'] = 'CO';
        $crawler = $this->client->submit($form);
        $this->assertRedirect('/login');
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        //check that it can't go to the dashboard.
        // submit the form
        $this->client->request('GET', '/dashboard');
        $this->assertRedirect('http://localhost/login');
        $crawler = $this->client->followRedirect();
        //now test that it can't login
        $form = $crawler->selectButton('Entrar')->form();
        $form['_username'] = 'test@no.com';
        $form['_password'] = '1111111';
        $crawler = $this->client->submit($form);
        $this->assertRedirect('http://localhost/login');
        $this->assertNewUser();
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

    private function assertNewUser()
    {
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail('test@no.com');
        $newSubs = $user->getEmailSubscription();
        $this->assertEquals(false, $user->isEnabled());
        $this->assertEquals(true, $newSubs->getIsActive());
        $this->assertEquals(["ROLE_USER"], $user->getRoles());
    }
}
