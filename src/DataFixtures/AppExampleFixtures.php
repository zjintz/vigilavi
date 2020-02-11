<?php

namespace App\DataFixtures;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\LogEntry;
use App\Entity\Word;
use App\Entity\WordSet;
use App\Entity\Origin;
use App\Entity\Headquarter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppExampleFixtures extends Fixture implements FixtureGroupInterface
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag= $parameterBag;
    }
    
    public function load(ObjectManager $manager)
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $csv = fopen($rootDir.'/data/example-1000.csv', 'r');
        $format = 'Y-m-d';
        $format24H = "H:i:s";
        
        $num = 0;
        $line = fgetcsv($csv);
        $origin = new Origin();
        $origin->setSubnet("193.77.1");
        $origin->setName("Comala");
        $origin->setType("Sede");
        $manager->persist($origin);
        while (!feof($csv)) {
            $timeLog = \DateTime::createFromFormat($format24H,$line[2]);
            $logEntry[$num] = new LogEntry();
            $logEntry[$num]->setDevice($line[0]);
            $logEntry[$num]->setDate($timeLog);
            $logEntry[$num]->setLogType($line[7]);
            $logEntry[$num]->setLogSubtype($line[9]);
            $logEntry[$num]->setUserName($line[13]);
            $logEntry[$num]->setUrl($line[18]);
            $logEntry[$num]->setSrcIp($line[22]);
            $logEntry[$num]->setDstIp($line[23]);
            $logEntry[$num]->setDomain($line[29]);
            $logEntry[$num]->setOrigin($origin);
            $manager->persist($logEntry[$num]);
            $num += 1;
            $line = fgetcsv($csv);
        }
        fclose($csv);
        $this->addTestUser($manager, $origin);
        $this->addWords($manager);

        $manager->flush();
    }

    private function addTestUser($manager, $origin)
    {
        $testHQ = new Headquarter();
        $testHQ->setCity("testCity");
        $testHQ->setCountry("testCountry");
        $testHQ->setName("testHQ");
        $testAdmin = new User();
        $testAdmin->setUsername('test@mail.com');
        $testAdmin->setPlainPassword('test');
        $testAdmin->setEnabled(true);
        $testAdmin->setEmail('test@mail.com');
        $testAdmin->setRoles(["ROLE_ADMIN"]);
        $testAdmin->addOrigin($origin);
        $testAdmin->setHeadquarter($testHQ);
        $manager->persist($testAdmin);
        $manager->flush();
    }
    private function addWords(ObjectManager $manager)
    {
        $words = ["bed", "shirt", "sheet", "skype"];
        $palabras = ["cama", "camisa", "lata", "machete"];
        $palavras = ["cigarro", "pai", "carro", "cima"];
        $englishWordSet = new WordSet();
        $englishWordSet->setName("English words");
        $englishWordSet->setDescription("just english.");
        $latWordSet = new WordSet();
        $latWordSet->setName("Portunol");
        $latWordSet->setDescription("Solo esp y pr.");
        $manager->persist($englishWordSet);
        $manager->persist($latWordSet);
        foreach($words as $lastWord)
        {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($englishWordSet);
            $manager->persist($newWord);            
        }

        foreach($palabras as $lastWord)
        {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($latWordSet);
            $manager->persist($newWord);
        }

        foreach($palavras as $lastWord)
        {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($latWordSet);
            $manager->persist($newWord);
        }
        $manager->flush();
        
    }

    private function makeWord(string $text)
    {
        $newWord = new Word();
        $newWord->setText($text);
        return $newWord;
    }

    public static function getGroups(): array
    {
        return ['app-example'];
    }
}
