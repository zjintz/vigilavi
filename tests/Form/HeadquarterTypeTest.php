<?php
// tests/Form/Type/TestedTypeTest.php
namespace App\Tests\Form;

use App\Form\HeadquarterType;
use App\Entity\Headquarter;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class HeadquarterTypeTest extends TypeTestCase
{
    private $translator;

    protected function setUp(): void
    {
        // mock any dependencies
        $this->translator = $this->createMock(TranslatorInterface::class);

        parent::setUp();
    }


    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new HeadquarterType($this->translator);

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        ];
    }


    
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'testname',
            'city' => 'testcity',
            'country' => 'CO',
        ];

        $hqCompare = new Headquarter();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(HeadquarterType::class, $hqCompare);

        $headquarter = new Headquarter();
        $headquarter->setName("testname");
        $headquarter->setCity("testcity");
        $headquarter->setCountry('CO');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($headquarter, $hqCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
