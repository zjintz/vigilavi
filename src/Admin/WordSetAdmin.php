<?php

namespace App\Admin;

use App\Entity\Word;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Sonata Admin for the WordSet.
 *
 *
 */
final class WordSetAdmin extends AbstractAdmin
{


    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
        $collection->remove('batch');
    }
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-6', 'label' => 'wordSet.title.general']
            )
            ->end()
            ->with(
                'Words',
                [
                    'class' => 'col-md-6', 'label' => 'wordSet.title.words'
                ]
            )
            ->end();

        $wordsStats = $this->getWordsStats();
        $formMapper
            ->with('General')
            ->add('name', TextType::class, ['label' => 'wordSet.label.name',])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'wordSet.label.description',
                    'required' => false,
                    'attr' => [ 'rows' => 5]
                ]
            )
            ->end()
            ->with(
                'Words',
                [
                    'description' => $wordsStats
                ]
            )
            ->add(
                'words',
                TextareaType::class,
                [
                    'label' => 'wordSet.label.words',
                    'required' => false,
                    'attr' => [ 'rows' => 8],
                    'help' => 'help.words',
                ]
            )
            ->end();
        
        $formMapper
            ->get('words')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($wordsAsArray) {
                    // transform the array to a string
                        $wordsText = '';
                        if (is_null($wordsAsArray)) {
                            return "";
                        }
                        foreach( $wordsAsArray as $word) {
                            $wordsText = $wordsText.$word->getText()."\n";
                        }
                        return $wordsText;
                    },
                    function ($wordsAsString) {
                        if ($wordsAsString === "") {
                            return [];
                        }
                        $wordsTexts = explode("\n", $wordsAsString);
                        $wordsAsArray=[];
                        foreach ($wordsTexts as $text) {
                            $word = new Word();
                            $word->setText(trim($text));
                            $wordsAsArray[]= $word;
                        } 
                        return $wordsAsArray;
                    }
                )
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name', null, [
            'operator_type' => 'sonata_type_equal',
            'advanced_filter' => false
        ]);
        $datagridMapper->add('description', null, [
            'operator_type' => 'sonata_type_equal',
            'advanced_filter' => false
        ]);
    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier(
            'name',
            null,
            ['label' => 'wordSet.label.name']
        );
        $listMapper->add(
            'description',
            null,
            ['label' => 'wordSet.label.description']
        );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->with('General', ['class' => 'col-md-7'])->end();

        $showMapper
            ->with('General', ['label' => 'wordSet.title.general'])
            ->add('name', null, ['label' => 'wordSet.label.name'])
            ->add('description', null, ['label' => 'wordSet.label.description'])
            ->add('words', null, ['label' => 'wordSet.label.words', 'expanded' => true, 'by_reference' => false, 'multiple' => true, 'associated_property' => 'text'])
            ->end()
            ;
    }


    /**
     * Checks if a string is empty or has only white spaces.
     *
     */
    private function isStringVoid($text)
    {
        if (ctype_space($text) || ($text === '')) {
            return true;
        }
        return false;
    }
    
    private function getWordsStats()
    {
        $words = $this->getSubject()->getWords();
        $totalWords = count($words);

        if ($totalWords == 0){
            return '';
        }
        $nullWords = 0;
        $addedWords = 0;
        $commentedWords = 0;
        foreach ($words as $word) {
            $text = $word->getText();
            if ($this->isStringVoid($text)) {
                $nullWords+=1;
                continue;
            }
            if (!(preg_match('/\s/', $text))) {
                if ($text[0] !== "#") {
                    $addedWords +=1;
                    continue;
                }
                $commentedWords +=1;
                continue;
            }
            $nullWords+=1;
        }

        $total = $this->translator->trans("words.stats.total");
        $valid = $this->translator->trans("words.stats.valid");
        $commented = $this->translator->trans("words.stats.commented");
        $nulls = $this->translator->trans("words.stats.nulls");
        $stats = $total."<span class='badge'>".$totalWords."</span>";
        $stats=$stats." <br>---- ".$valid.$addedWords." , ".$commented.$commentedWords." , "."<span class='alert-warning'>".$nulls."</span>".$nullWords;
        return $stats;
        
    }
}
