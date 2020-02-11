<?php

namespace App\Admin;

use App\Entity\LogEntry;
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
 * Sonata Admin for the LogEntry.
 *
 *
 */
final class LogEntryAdmin extends AbstractAdmin
{
    public function configureRoutes(RouteCollection $collection) {
        $collection->remove('export');
        $collection->remove('delete');
        $collection->remove('create');
        $collection->remove('edit');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->add(
            'date',
            null,
            ['label' => 'label.date']
        );
        $listMapper->add(
            'url',
            null,
            ['label' => 'logEntry.label.url']
        );
        $listMapper->add(
            'domain',
            null,
            ['label' => 'logEntry.label.domain']
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
                ->with('General', ['class' => 'col-md-7'])->end();

        $showMapper
            ->with('General', ['label' => 'logEntry.title.general'])
            ->add(
                'date',
                null,
                ['label' => 'logEntry.label.date']
            )
            ->add(
                'url',
                null,
                ['label' => 'logEntry.label.url']
            )
            ->add(
                'domain',
                null,
                ['label' => 'logEntry.label.domain']
            )
            ->end();
    }
}
