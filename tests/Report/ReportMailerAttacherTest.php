<?php
namespace App\Tests\Report;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Origin;
use App\Entity\Report;
use App\Entity\WordSet;
use App\Repository\OriginRepository;
use App\Repository\ReportRepository;
use App\Repository\WordSetRepository;
use App\Report\ReportMailerAttacher;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;


/**
 * Tests of the ReportMailerAttacher class, used by the ReportMailer class.
 *
 *
 */
class ReportMailerAttacherTest extends TestCase
{

    protected function mockEntityManagerVoid()
    {
        $wordsetRepository = $this->createMock(WordSetRepository::class);
        $wordsetRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with(WordSet::class)
                      ->willReturn($wordsetRepository);

        return $entityManager;
    }

    protected function mockEntityManager()
    {
        $wordsetA = new WordSet();
        $wordsetA->setName("wordsetA");
        $wordsetRepository = $this->createMock(WordSetRepository::class);
        $wordsetRepository->expects($this->exactly(1))
            ->method('findAll')
            ->willReturn([$wordsetA]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(1))
                      ->method('getRepository')
                      ->with(WordSet::class)
                      ->willReturn($wordsetRepository);

        return $entityManager;
    }
    
    /**
     * Tests the listAttachments function of the ReportMailerAttacher.
     *
     * The function makes a list of files to attach in the email message to a 
     * given user and a given date. In this testcase causes of void return are tested.
     */
    public function testListAttachmentsVoid()
    {
        //first when there are no origins in the user.
        $templating = $this->createMock(\Twig\Environment::class);;
        $attacher = new ReportMailerAttacher($this->mockEntityManagerVoid(), $templating);
        $testUser = new User();
        $fileList = $attacher->listAttachments($testUser, "2020-01-01");
        $this->assertEquals([], $fileList);
        //now we add an origin to the user, but there are no wordsets.
        $attacher = new ReportMailerAttacher($this->mockEntityManagerVoid(), $templating);
        $origin = new Origin();
        $testUser->addOrigin($origin);
        $fileList = $attacher->listAttachments($testUser, "2020-01-01");
        $this->assertEquals([], $fileList);
        //Wordsets but no origin
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManager(),
            $templating
        );
        $testUser = new User();
        $fileList = $attacher->listAttachments($testUser, "2020-01-01");
        $this->assertEquals([], $fileList);
    }


    /**
     * Tests the listAttachments function of the ReportMailerAttacher.
     *
     * The function makes a list of files to attach in the email message to a 
     * given user and a given date. 
     */
    public function testListAttachments()
    {
                //Wordsets but no origin
        $templating = $this->createMock(\Twig\Environment::class);;
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManager(),
            $templating
        );
        $testUser = new User();
        $origin = new Origin();
        $origin->setName("aqui");
        $testUser->addOrigin($origin);
        $fileList = $attacher->listAttachments($testUser, "2020-01-01");
        $this->assertEquals(["wv_2020-01-01_wordsetA_aqui.html"], $fileList);
        // 2 origins
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManager(),
            $templating
        );
        $testUser = new User();
        $origin = new Origin();
        $origin->setName("aqui");
        $origin2 = new Origin();
        $origin2->setName("alla");
        $testUser->addOrigin($origin);
        $testUser->addOrigin($origin2);
        $fileList = $attacher->listAttachments($testUser, "2020-01-01");
        $this->assertEquals(
            [
                "wv_2020-01-01_wordsetA_aqui.html",
                "wv_2020-01-01_wordsetA_alla.html"
            ],
            $fileList
        );
    }

    public function testListAttachmentsDir()
    {
        $templating = $this->createMock(\Twig\Environment::class);;
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManager(),
            $templating
        );
        $testUser = new User();
        $origin = new Origin();
        $origin->setName("aqui");
        $origin2 = new Origin();
        $origin2->setName("alla");
        $testUser->addOrigin($origin);
        $testUser->addOrigin($origin2);
        //using the dir option
        $fileList = $attacher->listAttachments($testUser, "2020-01-01", "/dir/");
        $this->assertEquals(
            [
                "/dir/wv_2020-01-01_wordsetA_aqui.html",
                "/dir/wv_2020-01-01_wordsetA_alla.html"
            ],
            $fileList
        );

        $attacher = new ReportMailerAttacher(
            $this->mockEntityManager(),
            $templating
        );
        $testUser->addOrigin($origin);
        $testUser->addOrigin($origin2);
        //using the dir option
        $fileList = $attacher->listAttachments($testUser, "2020-01-01", "/dir");
        $this->assertEquals(
            [
                "/dir/wv_2020-01-01_wordsetA_aqui.html",
                "/dir/wv_2020-01-01_wordsetA_alla.html"
            ],
            $fileList
        );
    }

    /**
     * Makes sure that the makeAllView method cretes the dir if is not created.
     *
     *
     */
    public function testMakeAllViewsNoDir()
    {
        $templating = $this->createMock(\Twig\Environment::class);
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManagerVoid(),
            $templating
        );
        $dirPath = '/tmp/vigilavi_test/';
        $this->clearPath($dirPath);
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
        $fileList = $attacher->makeAllViews("2020-01-01", $dirPath);
        $this->assertTrue(file_exists($dirPath) && is_dir( $dirPath));
        $this->assertEquals([], $fileList);
        $realDirList = scandir($dirPath);
        $this->assertEquals([], array_diff($realDirList, array('..', '.')));
    }
    
    public function testMakeAllViewsVoid()
    {
        $templating = $this->createMock(\Twig\Environment::class);
        $attacher = new ReportMailerAttacher(
            $this->mockEntityManagerVoid(),
            $templating
        );
        $dirPath = '/tmp/vigilavi_test/';
        $this->clearPath($dirPath);
        $fileList = $attacher->makeAllViews("2020-01-01", $dirPath);
        $this->assertEquals([], $fileList);
        $realDirList = scandir($dirPath);
        $this->assertEquals([], array_diff($realDirList, array('..', '.')));
    }
    
    protected function clearPath($dirPath)
    {
        $files = glob($dirPath.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    public function testMakeAllViewsNoOrigins()
    {
        $wordsetA = new WordSet();
        $wordsetA->setName("wordsetA");
        $wordsetRepository = $this->createMock(WordSetRepository::class);
        $wordsetRepository->expects($this->exactly(1))
            ->method('findAll')
            ->willReturn([$wordsetA]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        $entityManager->expects($this->exactly(2))
                      ->method('getRepository')
                      ->withConsecutive([WordSet::class],[Origin::class],)
                      ->willReturnOnConsecutiveCalls($wordsetRepository, $originRepository);
        //Since there are no origins no reports should be made.
        $templating = $this->createMock(\Twig\Environment::class);;
        $attacher = new ReportMailerAttacher($entityManager, $templating);
        $dirPath = '/tmp/vigilavi_test/';
        $this->clearPath($dirPath);
        $fileList = $attacher->makeAllViews("2020-01-01", $dirPath);
        $this->assertEquals([], $fileList);
        $realDirList = scandir($dirPath);
        $this->assertEquals([], array_diff($realDirList, array('..', '.')));
    }

    public function testMakeAllViewsNoReports()
    {
        $wordsetA = new WordSet();
        $wordsetA->setName("wordsetA");
        $wordsetRepository = $this->createMock(WordSetRepository::class);
        $wordsetRepository->expects($this->exactly(1))
            ->method('findAll')
            ->willReturn([$wordsetA]);
        $originO = new Origin();
        $originO->setName("OO");
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$originO]);

        $reportRepository = $this->createMock(ReportRepository::class);
        $reportRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(3))
                      ->method('getRepository')
                      ->withConsecutive([WordSet::class],[Origin::class], [Report::class])
                      ->willReturnOnConsecutiveCalls(
                          $wordsetRepository,
                          $originRepository,
                          $reportRepository
                      );
        //Since there are no reports no files should be made.
        $templating = $this->createMock(\Twig\Environment::class);
        $attacher = new ReportMailerAttacher($entityManager, $templating);
        $fileList = $attacher->makeAllViews("2020-01-01", '/tmp/vigilavi_test/');
        $this->assertEquals([], $fileList);
        $realDirList = scandir('/tmp/vigilavi_test/');
        $this->assertEquals([], array_diff($realDirList, array('..', '.')));
    }

    public function testMakeAllViews()
    {
        $wordsetA = new WordSet();
        $wordsetA->setName("wordsetA");
        $wordsetRepository = $this->createMock(WordSetRepository::class);
        $wordsetRepository->expects($this->exactly(1))
            ->method('findAll')
            ->willReturn([$wordsetA]);
        $originO = new Origin();
        $originO->setName("OO");
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$originO]);

        $report = new Report();
        $entryDate = \DateTime::createFromFormat('yy-m-d', "2020-01-01");
        $reportRepository = $this->createMock(ReportRepository::class);
        $reportRepository->expects($this->once())
                         ->method('findBy')
                         ->with(["date"=>$entryDate])
                         ->willReturn([$report]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(3))
                      ->method('getRepository')
                      ->withConsecutive([WordSet::class],[Origin::class], [Report::class])
                      ->willReturnOnConsecutiveCalls(
                          $wordsetRepository,
                          $originRepository,
                          $reportRepository
                      );
        $templating = $this->createMock(\Twig\Environment::class);
        $templating->expects($this->once())
                   ->method('render')
                      ->willReturn(
                          "<html></html>"
                      );
        $attacher = new ReportMailerAttacher($entityManager, $templating);
        $fileList = $attacher->makeAllViews("2020-01-01", '/tmp/vigilavi_test/');
        $this->assertEquals(['/tmp/vigilavi_test/wv_2020-01-01_wordsetA_OO.html'], $fileList);
        $realDirList = scandir('/tmp/vigilavi_test/');
        $this->assertEquals('wv_2020-01-01_wordsetA_OO.html', $realDirList[2]);
    }
}
