<?php

namespace App\DataFixtures;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\EmailSubscription;
use App\Entity\Headquarter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserTestFixtures extends Fixture
    implements FixtureGroupInterface, DependentFixtureInterface 
{

    public function load(ObjectManager $manager)
    {
        $testHQ = new Headquarter();
        $testHQ->setCity("testCity");
        $testHQ->setCountry("testCountry");
        $testHQ->setName("testHQ");
        $testUser = new User();
        $testUser->setUsername('user@test.com');
        $testUser->setPlainPassword('testPass');
        $testUser->setEnabled(true);
        $testUser->setEmail('user@test.com');
        $testUser->setRoles(["ROLE_USER"]);
        $testUser->setHeadquarter($testHQ);
        $comalaOrigin = $this->getReference(
            AppExampleFixtures::COMALA_ORIGIN_REFERENCE
        );
        $macondoOrigin = $this->getReference(
            AppExampleFixtures::MACONDO_ORIGIN_REFERENCE
        );
        $testUser->addOrigin($comalaOrigin);
        $testUser->addOrigin($macondoOrigin);
        $testUser->setEmailSubscription($this->createSubscription($manager));
        $this->setReference('user', $testUser);
        $testAdmin = new User();
        $testAdmin->setUsername('admin@test.com');
        $testAdmin->setPlainPassword('testPass');
        $testAdmin->setEnabled(true);
        $testAdmin->setEmail('admin@test.com');
        $testAdmin->setRoles(["ROLE_ADMIN"]);
        $testAdmin->setHeadquarter($testHQ);
        $testAdmin->addOrigin($macondoOrigin);
        $testAdmin->setEmailSubscription($this->createSubscription($manager));
        $this->setReference('admin', $testAdmin);
        $testEditor = new User();
        $testEditor->setUsername('editor@test.com');
        $testEditor->setPlainPassword('testPass');
        $testEditor->setEnabled(true);
        $testEditor->setEmail('editor@test.com');
        $testEditor->setRoles(["ROLE_EDITOR"]);
        $testEditor->setHeadquarter($testHQ);
        $testEditor->addOrigin($comalaOrigin);
        $testEditor->setEmailSubscription($this->createSubscription($manager));
        $this->setReference('editor', $testEditor);
        $manager->persist($testUser);
        $manager->persist($testAdmin);
        $manager->persist($testEditor);
        $manager->flush();
    }

    protected function createSubscription(ObjectManager $manager)
    {
        $inactiveSubs = new EmailSubscription();
        $inactiveSubs->setIsActive(true);
        $manager->persist($inactiveSubs);
        return $inactiveSubs;
    }

    public function getDependencies()
    {
        return array(
            AppExampleFixtures::class,
        );
    }

    public static function getGroups(): array
    {
        return ['app-example'];
    }
}
