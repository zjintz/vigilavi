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
 * Sonata Admin for the Report entity.
 *
 *
 */
final class ReportAdmin extends AbstractAdmin
{
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-7', 'label' => 'report.title.general']
            )
            ->end();

        $formMapper
            ->with('General')
            ->add('wordSet', null, ['label' => 'report.label.wordSet',])
            ->add('date', null, ['label' => 'report.label.date',])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('wordSet', null, [
            'operator_type' => EqualType::class,
            'advanced_filter' => false
        ]);
        $datagridMapper->add('date', null, [
            'operator_type' => EqualType::class,
            'advanced_filter' => false
        ]);

    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier(
            'wordSet',
            null,
            ['label' => 'report.label.wordSet',
             'class' => WordSet::class
             ]
        );
        $listMapper->addIdentifier(
            'date',
            null,
            ['label' => 'report.label.date'
             ]
        );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->with('General', ['class' => 'col-md-7'])->end();

        $showMapper
            ->with('General', ['label' => 'report.title.general'])
            ->add('wordSet', null, ['label' => 'report.label.wordSet'])
            ->add('date', null, ['label' => 'report.label.date'])
            ->add(
                'outcomes',
                null,
                ['label' => 'report.label.outcomes',
                 'class' => Outcome::class
                ]
            )
            ->end()
            ;
    }
}
