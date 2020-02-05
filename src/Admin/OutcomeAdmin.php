<?php

namespace App\Admin;

use App\Entity\Outcome;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\Form\Type\EqualType;

/**
 * Sonata Admin for the Outcome.
 *
 *
 */
final class OutcomeAdmin extends AbstractAdmin
{

    public function configureRoutes(RouteCollection $collection)
    {
        
        $collection->remove('export');
        $collection->remove('edit');
        $collection->remove('delete');
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('classification', null, [
            'operator_type' => 'sonata_type_equal',
            'advanced_filter' => false
        ]);
    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier(
            'classification',
            null,
            ['label' => 'outcome.label.classification']
        );
        $listMapper->add(
            'wordsFound',
            null,
            ['label' => 'outcome.label.wordsFound']
        );

        $listMapper->add(
            'logEntry.url',
            null,
            [
                'label' => '.label.url',

            ]
        );

        $listMapper->add(
            'logEntry.log_subtype',
            null,
            [
                'label' => '.label.log_subtype',

            ]
        );
        
        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'show' => array(),
            )
        ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->with('General', ['class' => 'col-md-7'])->end();

        $showMapper
            ->with('General', ['label' => 'outcome.title.general'])
            ->add('classification', null, ['label' => 'outcome.label.classification'])
            ->add(
                'wordsFound',
                null,
                ['label' => 'outcome.label.wordsFound']
            )
            ->add('report',
                  null,
                  [
                      'label' => 'outcome.label.report',
                    'associated_property'=> 'id',
                      'route' => [
                        'name' => 'show'
                      ]
                      
                  ]
            )
            ->add('report.date',
                  null,
                  [
                      'label' => 'label.date'                      
                  ]
            )
            
            ->add(
                'logEntry',
                null,
                [
                    'label' => 'outcome.label.log_entry',
                    'associated_property'=> 'url',
                    'route' => [
                        'name' => 'show'
                    ]
                ]
            )
            ->add(
                'logEntry.log_subtype',
                null,
                [
                    'label' => 'label.log.subtype'
                ]
            )
            ->end()
            ;
    }
}
