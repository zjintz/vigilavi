<?php

namespace App\Admin;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Origin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\EqualType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


/**
 * Sonata Admin for the Origin.
 *
 *
 */
final class OriginAdmin extends AbstractAdmin
{

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'view'));

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    public function createQuery($context = 'list')
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')
                     ->getToken()->getUser();
        $query = parent::createQuery($context);
        $query->from(User::class, 'u');
        $query->innerJoin('o.users', 'uo');
        $query->andWhere('u.id = '.$user->getId());
        return $query;
    }
    
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier(
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
            'subnet',
            null,
            ['label' => 'origin.label.subnet']
        );

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
                'subnet',
                null,
                ['label' => 'origin.label.subnet']
            )
            ->end();
    }
}
