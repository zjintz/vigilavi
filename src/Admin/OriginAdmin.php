<?php

namespace App\Admin;

use App\Entity\Origin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\Form\Type\EqualType;

/**
 * Sonata Admin for the Origin.
 *
 *
 */
final class OriginAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->add(
            'name',
            null,
            ['label' => 'origin.label.name']
        );
        $listMapper->add(
            'type',
            null,
            ['label' => 'origin.label.type']
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
            ->with('General', ['label' => 'origin.title.general'])
            ->add(
                'name',
                null,
                ['label' => 'origin.label.name']
            )
            ->add(
                'type',
                null,
                ['label' => 'origin.label.type']
            )
            ->end();
    }
}
