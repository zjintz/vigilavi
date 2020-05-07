<?php
namespace App\Tests\Controller;

use App\DataFixtures\UserTestFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;


class UserMailerControllerTest extends WebTestCase
{
    use FixturesTrait;
    
    public function testNotifyNewUsersToAdmins()
    {
        $fixtures = $this->loadFixtures(
            [UserTestFixtures::class]
        )->getReferenceRepository();
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->setServerParameters([]);

        $crawler = $client->request('GET', '/');
        $crawler=$client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'admin@test.com',
            '_password'  => 'testPass',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
                // enables the profiler for the next request (it does nothing if the profiler is not available)
        $client->enableProfiler();

        $crawler = $client->request('POST', '/notify/new_user/');
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent to the 2 admins
        $this->assertSame(2, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('Novo Usuario do vigilavi.org', $message->getSubject());
        $this->assertSame('no_reply@vigilavi.org', key($message->getFrom()));
        $this->assertSame('test@mail.com', key($message->getTo()));
        /*        $this->assertSame(
            'You should see me from the profiler!',
            $message->getBody()
            );*/
    }

    public function testNotifyActivation()
    {
        $fixtures = $this->loadFixtures(
            [UserTestFixtures::class]
        )->getReferenceRepository();
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->setServerParameters([]);

        $crawler = $client->request('GET', '/');
        $crawler=$client->followRedirect();
        $form = $crawler->selectButton('Entrar')->form(array(
            '_username'  => 'admin@test.com',
            '_password'  => 'testPass',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
                // enables the profiler for the next request (it does nothing if the profiler is not available)
        $client->enableProfiler();
        $userId = $fixtures->getReference('user')->getId();
        $crawler = $client->request('POST', '/notify/'.$userId.'/activation');
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent to the user
        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('Sua conta foi ativada', $message->getSubject());
        $this->assertSame('no_reply@vigilavi.org', key($message->getFrom()));
        $this->assertSame('user@test.com', key($message->getTo()));
        /*        $this->assertSame(
            'You should see me from the profiler!',
            $message->getBody()
            );*/
    }
}
