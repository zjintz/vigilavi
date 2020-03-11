<?php

namespace App\Admin;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Origin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Route\RouteCollection;
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
        $collection->clearExcept(array('list'));

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    public function createQuery($context = 'list')
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')
                     ->getToken()->getUser();
        $query = parent::createQuery();
        $query->innerJoin('o.users', 'uo');
        $query->andWhere('uo.id = '.$user->getId());
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
            'subnet',
            null,
            ['label' => 'origin.label.subnet']
        );

    }
}
