<?php

namespace App\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\EqualType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Sonata Admin for the Report entity.
 *
 *
 */
final class ReportAdmin extends AbstractAdmin
{
    
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild('label.view.report', [
            'uri' => $admin->generateUrl('show', ['id' => $id])
        ]);

        if ($this->isGranted('LIST')) {
            $menu->addChild('label.list.outcomes', [
                'uri' => $this->getChild('app.admin.outcome')->generateUrl('list')]);
        }
    }
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
        $collection->remove('edit');
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
            ->add(
                'origin',
                null,
                ['label' => 'label.origin', 'choice_label' => 'name']
            )
            
            ->end();
    }
    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier(
            'date',
            null,
            ['label' => 'report.label.date']
        
        );
        $listMapper->add(
            'wordSet',
            null,
            ['label' => 'report.label.wordSet',
             'class' => WordSet::class
             ]
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
                'show' => array(),
            )
        ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
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
            ->add(
                'outcomes',
                null,
                [
                    'associated_property'=> 'id'
                ]
            )
            ->end()
            ->with('Stats', ['class' => 'col-md-7'])
            ->add('totalWords', null, ['label' => 'report.label.totalWords'])
            ->add('totalLogEntries', null, ['label' => 'report.label.totalLogEntries'])
            ->add('totalAllowedLogEntries', null, ['label' => 'report.label.totalAllowedLogEntries'])
            ->add('totalDeniedLogEntries', null, ['label' => 'report.label.totalDeniedLogEntries'])
            ->add('totalClassifiedLogEntries', null, ['label' => 'report.label.totalClassifiedLogEntries'])
            ->add('totalAllowedClassifiedLogEntries', null, ['label' => 'report.label.totalAllowedClassifiedLogEntries'])
            ->add('totalDeniedClassifiedLogEntries', null, ['label' => 'report.label.totalDeniedClassifiedLogEntries'])

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
