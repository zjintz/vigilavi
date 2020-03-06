<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Headquarter;

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
        $this->setReference('user', $testUser);
        $testAdmin = new User();
        $testAdmin->setUsername('admin@test.com');
        $testAdmin->setPlainPassword('testPass');
        $testAdmin->setEnabled(true);
        $testAdmin->setEmail('admin@test.com');
        $testAdmin->setRoles(["ROLE_ADMIN"]);
        $testAdmin->setHeadquarter($testHQ);
        $testAdmin->addOrigin($macondoOrigin);
        $this->setReference('admin', $testAdmin);
        $testEditor = new User();
        $testEditor->setUsername('editor@test.com');
        $testEditor->setPlainPassword('testPass');
        $testEditor->setEnabled(true);
        $testEditor->setEmail('editor@test.com');
        $testEditor->setRoles(["ROLE_EDITOR"]);
        $testEditor->setHeadquarter($testHQ);
        $testEditor->addOrigin($comalaOrigin);
        $this->setReference('editor', $testEditor);
        $manager->persist($testUser);
        $manager->persist($testAdmin);
        $manager->persist($testEditor);
        $manager->flush();
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
