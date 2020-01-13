<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            'text',
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
            ->end()
            ;
    }
}
