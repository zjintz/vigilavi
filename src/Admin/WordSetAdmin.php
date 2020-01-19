<?php

namespace App\Admin;

use App\Entity\Word;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\EqualType;

/**
 * Sonata Admin for the WordSet.
 *
 *
 */
final class WordSetAdmin extends AbstractAdmin
{
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-7', 'label' => 'wordSet.title.general']
            )
            ->end();

        $formMapper
            ->with('General')
            ->add('name', TextType::class, ['label' => 'wordSet.label.name',])
            ->add('description', TextType::class, ['label' => 'wordSet.label.description',])
            ->add(
                'words',
                CollectionType::class,
                ['label' => 'wordSet.label.words',
                 'allow_add'=>true,
                 'allow_delete'=> true
                 
                ]
            )
            ->end();
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
}
