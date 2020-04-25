<?php

namespace App\DataFixtures;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\LogEntry;
use App\Entity\Word;
use App\Entity\WordSet;
use App\Entity\Origin;
use App\Entity\Headquarter;
use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppExampleFixtures extends Fixture implements FixtureGroupInterface
{
    public const COMALA_ORIGIN_REFERENCE = 'comala-origin';
    public const MACONDO_ORIGIN_REFERENCE = 'macondo-origin';
    public const AREA51_ORIGIN_REFERENCE = 'area51-origin';

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag= $parameterBag;
    }
    
    public function load(ObjectManager $manager)
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $csv = fopen($rootDir.'/data/example-1000.csv', 'r');
        $format = 'Y-m-d H:i:s';
        $num = 0;
        $line = fgetcsv($csv);
        $comalaOrigin = $this->makeOrigin("Comala", "193.77.1");
        $macondoOrigin = $this->makeOrigin("Macondo", "193.77.2");
        $area51Origin = $this->makeOrigin("Area51", "193.77.3");
        $this->addReference(self::COMALA_ORIGIN_REFERENCE, $comalaOrigin);
        $this->addReference(self::MACONDO_ORIGIN_REFERENCE, $macondoOrigin);
        $this->addReference(self::AREA51_ORIGIN_REFERENCE, $area51Origin);
        $manager->persist($comalaOrigin);
        $manager->persist($macondoOrigin);
        $manager->persist($area51Origin);
        while (!feof($csv)) {
            $dateTimeLog = \DateTime::createFromFormat($format,($line[1]." ".$line[2]));
            $logEntry[$num] = new LogEntry();
            $logEntry[$num]->setDate($dateTimeLog);
            $logEntry[$num]->setLogType($line[7]);
            $logEntry[$num]->setLogSubtype($line[9]);
            $logEntry[$num]->setUserName($line[13]);
            $logEntry[$num]->setUrl($line[18]);
            $logEntry[$num]->setSrcIp($line[22]);
            $logEntry[$num]->setDstIp($line[23]);
            $logEntry[$num]->setDomain($line[29]);
            if ($num < 5) {
                $logEntry[$num]->setOrigin($comalaOrigin);
            }
            if ($num >= 5 && $num <= 7) {
                $logEntry[$num]->setOrigin($macondoOrigin);
            }
            if ($num > 7) {
                $logEntry[$num]->setOrigin($area51Origin);
            }
            
            $manager->persist($logEntry[$num]);
            $num += 1;
            $line = fgetcsv($csv);
        }
        $this->addReference("comala_log_entry", $logEntry[0]);
        $this->addReference("macondo_log_entry", $logEntry[5]);
        fclose($csv);
        $this->addTestUser($manager, $comalaOrigin);
        $this->addWords($manager, [$comalaOrigin, $macondoOrigin, $area51Origin]);
        $manager->flush();
        $this->addReports($manager);
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
    private function addWords(ObjectManager $manager, $origins)
    {
        $words = ["bed", "shirt", "sheet", "skype"];
        $palabras = ["cama", "camisa", "lata", "machete"];
        $palavras = ["cigarro", "pai", "carro", "cima"];
        $englishWordSet = new WordSet();
        $englishWordSet->setName("English words");
        $englishWordSet->setDescription("just english.");
        $origins[2]->addWordSet($englishWordSet);
        $latWordSet = new WordSet();
        $latWordSet->setName("Portunol");
        $latWordSet->setDescription("Solo esp y pr.");
        $origins[0]->addWordSet($latWordSet);
        $origins[1]->addWordSet($latWordSet);
        $origins[2]->addWordSet($latWordSet);
        $manager->persist($englishWordSet);
        $manager->persist($latWordSet);
        foreach ($words as $lastWord) {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($englishWordSet);
            $manager->persist($newWord);          
        }

        foreach ($palabras as $lastWord) {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($latWordSet);
            $manager->persist($newWord);
        }

        foreach ($palavras as $lastWord) {
            $newWord = $this->makeWord($lastWord);
            $newWord->setWordSet($latWordSet);
            $manager->persist($newWord);
        }
        $this->setReference('wordset', $englishWordSet);
        $manager->flush();
    }

    private function addReports(ObjectManager $manager)
    {
        $origins = $manager->getRepository(Origin::class)->findAll();
        $wordsets = $manager->getRepository(WordSet::class)->findAll();
        $format = "Y-m-d";
        $count = 0;
        foreach ($origins as $origin) {
            $newReport = new Report();
            $newReport->setOrigin($origin);
            $newDate = \DateTime::createFromFormat($format, "2019-08-23");
            $newReport->setDate($newDate);
            $this->addReference("report-23-".$count, $newReport);
            $manager->persist($newReport);
            $count +=1;
        }
        $count = 0;
        foreach ($origins as $origin) {
            $newReport = new Report();
            $newReport->setOrigin($origin);
            foreach ($wordsets as $wordset) {
                $origin->addWordSet($wordset);
            }
            $newDate = \DateTime::createFromFormat($format, "2019-09-30");
            $newReport->setDate($newDate);
            $this->addReference("report-30-".$count, $newReport);
            $manager->persist($newReport);
            $count +=1;
        }
        $manager->flush();
    }

    private function makeWord(string $text)
    {
        $newWord = new Word();
        $newWord->setText($text);
        return $newWord;
    }

    private function makeOrigin(string $name, string $subnet)
    {
        $origin = new Origin();
        $origin->setSubnet($subnet);
        $origin->setName($name);
        $origin->setActive(true);
        return $origin;
    }

    public static function getGroups(): array
    {
        return ['app-example'];
    }
}
