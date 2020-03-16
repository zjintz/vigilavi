<?php

namespace App\Admin;

use App\Entity\Word;
use App\Util\WordCounter;
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
    private $wordCounter;
    
    public function setWordCounter(WordCounter $wordCounter)
    {
        $this->wordCounter = $wordCounter;
    }
    
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('batch');
        $collection->remove('export');
    }

    /**
     * {@inheritdoc}
     */
    public function configureBatchActions($actions)
    {
        if (isset($actions['delete'])) {
            unset($actions['delete']);
        }
        return $actions;
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

        $words = $this->getSubject()->getWords();
        $wordsStats = $this->formatWordStats($this->wordCounter->getWordsStats($words));
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

    protected function formatWordStats(array $stats)
    {
        if (!$stats) {
            return '';
        }
        $total = $this->translator->trans("words.stats.total");
        $valid = $this->translator->trans("words.stats.valid");
        $commented = $this->translator->trans("words.stats.commented");
        $nulls = $this->translator->trans("words.stats.nulls");
        $line = $total."<span class='badge'>".$stats['total_words']."</span>";
        $line = $line." <br>---- ".$valid.$stats['added_words']." , ";
        $line = $line.$commented.$stats['commented_words']." , ";
               
        $line=$line."<span class='alert-warning'>".$nulls."</span>".$stats["null_words"];
        return $line;
    }
    
}
