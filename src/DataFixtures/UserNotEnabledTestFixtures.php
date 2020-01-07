<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Headquarter;

class UserNotEnabledTestFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $testHQ = new Headquarter();
        $testHQ->setCity("testCity");
        $testHQ->setCountry("testCountry");
        $testHQ->setName("testHQ");
        $testUser = new User();
        $testUser->setUsername('userne@test.com');
        $testUser->setPlainPassword('testnePass');
        $testUser->setEnabled(false);
        $testUser->setEmail('userne@test.com');
        $testUser->setHeadquarter($testHQ);
        $manager->persist($testHQ);
        $manager->persist($testUser);
        $manager->flush();
    }
}
