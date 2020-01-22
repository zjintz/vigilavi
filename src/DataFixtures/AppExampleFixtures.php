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
        $csv = fopen($rootDir.'/data/small-example.csv', 'r');
        $format = 'Y-m-d';
        $format24H = "H:i:s";
        
        $num = 0;
        $line = fgetcsv($csv);
        $origin = new Origin();
        $origin->setDeviceId("C31007H8JH8PME4");
        $origin->setName("Comala");
        $origin->setType("Sede");
        $manager->persist($origin);
        while (!feof($csv)) {
            $timeLog = \DateTime::createFromFormat($format24H,$line[2]);
            $logEntry[$num] = new LogEntry();
            $logEntry[$num]->setDevice($line[0]);
            $logEntry[$num]->setDate(\DateTime::createFromFormat($format,$line[1]));
            $logEntry[$num]->setTime($timeLog);
            $logEntry[$num]->setTimezone($line[3]);
            $logEntry[$num]->setDeviceName($line[4]);
            $logEntry[$num]->setDeviceId($line[5]);
            $logEntry[$num]->setLogId($line[6]);
            $logEntry[$num]->setLogType($line[7]);
            $logEntry[$num]->setLogComponent($line[8]);
            $logEntry[$num]->setLogSubtype($line[9]);
            $logEntry[$num]->setStatus($line[10]);
            $logEntry[$num]->setPriority($line[11]);
            $logEntry[$num]->setFwRuleId($line[12]);
            $logEntry[$num]->setUserName($line[13]);
            $logEntry[$num]->setUserGp($line[14]);
            $logEntry[$num]->setIap($line[15]);
            $logEntry[$num]->setCategory($line[16]);
            $logEntry[$num]->setCategoryType($line[17]);
            $logEntry[$num]->setUrl($line[18]);
            $logEntry[$num]->setContenttype($line[19]);
            $logEntry[$num]->setOverrideToken($line[20]);
            $logEntry[$num]->setHttpresponsecode($line[21]);
            $logEntry[$num]->setSrcIp($line[22]);
            $logEntry[$num]->setDstIp($line[23]);
            $logEntry[$num]->setProtocol($line[24]);
            $logEntry[$num]->setSrcPort($line[25]);//------
            $logEntry[$num]->setDstPort($line[26]);
            $logEntry[$num]->setSentBytes($line[27]);
            $logEntry[$num]->setRecvBytes($line[28]);
            $logEntry[$num]->setDomain($line[29]);
            $logEntry[$num]->setExceptions($line[30]);
            $logEntry[$num]->setActivityname($line[31]);
            $logEntry[$num]->setReason($line[32]);
            $logEntry[$num]->setUserAgent($line[33]);
            $logEntry[$num]->setStatusCode($line[34]);
            $logEntry[$num]->setTransactionid($line[35]);
            $logEntry[$num]->setReferer($line[36]);
            $logEntry[$num]->setDownloadFileName($line[37]);
            $logEntry[$num]->setDownloadFileType($line[38]);
            $logEntry[$num]->setUploadFileName($line[39]);
            $logEntry[$num]->setUploadFileType($line[40]);
            $logEntry[$num]->setConId($line[41]);
            $logEntry[$num]->setApplication($line[42]);
            $logEntry[$num]->setAppIsCloud($line[43]);
            $logEntry[$num]->setOverrideName($line[44]);
            $logEntry[$num]->setOverrideAuthorizer($line[45]);
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
