<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\Form\Type\EqualType;

/**
 * Sonata Admin for the Report entity.
 *
 *
 */
final class ReportAdmin extends AbstractAdmin
{
    

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-7', 'label' => 'word.title.general']
            )
            ->end();

        $formMapper
            ->with('General', ['label' => 'report.title.general'])
            ->add('wordSet', null, ['label' => 'report.label.wordSet'])
            ->add('date', null, ['label' => 'report.label.date'])
            ->add('origin',
                  null,
                  ['label' => 'label.origin', 'choice_label' => 'name']
            )
            
            ->end();
    }
    
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->add(
            'wordSet',
            null,
            ['label' => 'report.label.wordSet',
             'class' => WordSet::class
             ]
        );
        $listMapper->add(
            'date',
            null,
            ['label' => 'report.label.date']
        
        );
        $listMapper->add(
            'origin',
            null,
            ['label' => 'label.origin',
             'associated_property' => 'name'
            ]
        
        );
        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'show' => array()
            )
        ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->tab('General')
                ->with('General', ['class' => 'col-md-5'])
            ->add('wordSet', null, ['label' => 'report.label.wordSet'])
            ->add('date', null, ['label' => 'report.label.date'])
            ->add(
                'origin',
                null,
                [
                    'label' => 'label.origin',
                    'associated_property' => 'name'
                ]
            )
            ->end()
            ->with('Stats', ['class' => 'col-md-7'])
            ->end()
            ->end()
            ;

        $showMapper
            ->tab('Outcomes')
            ->with('Outcomes', ['label' => 'report.title.general'])
            
            ->add(
                'outcomes',
                AdminType::class,
                [
                    'label' => 'report.label.outcomes',
                    'route' => ['name' => 'show']
                ]
            )
            ->end()
            ->end()
            ;
    }

    public function toString($object)
    {
        $title = "";
        if(!is_null($object->getDate()))
        {
            $title = $object->getDate()->format("Y-m-d");
            $title = 'do '.$title;
        }
        return $object instanceof Liturgy
            ? $object->getTitle()
            : 'Informe '.$title; // shown in the breadcrumb on the create view
    }
}
