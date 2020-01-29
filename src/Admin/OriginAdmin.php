<?php

namespace App\Admin;

use App\Entity\Origin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
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

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-7', 'label' => 'title.general']
            )
            ->end();

        $formMapper
            ->with('General')
            ->add('name', TextType::class, ['label' => 'origin.label.name',])
            ->add('type', TextType::class, ['label' => 'origin.label.type',])
            ->add('deviceId', TextType::class, ['label' => 'origin.label.deviceId',])

            ->end();
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
        $listMapper->add(
            'deviceId',
            null,
            ['label' => 'origin.label.deviceId']
        );

        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'show' => array(),
                'edit' => array()
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
            ->add(
                'deviceId',
                null,
                ['label' => 'origin.label.deviceId']
            )
            ->add(
                'reports',
                AdminType::class,
                ['label' => 'origin.label.reports']
            )
            
            ->end();
    }
}
