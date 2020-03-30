<?php

namespace App\Admin;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\LogEntry;
use App\Entity\Origin;
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
        $collection->remove('batch');
    }

    public function createQuery($context = 'list')
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')
                     ->getToken()->getUser();
        $query = parent::createQuery();
        $query->innerJoin('o.origin', 'oo');
        $query->innerJoin('oo.users', 'uo');
        $query->andWhere('uo.id = '.$user->getId());
        return $query;
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
        $listMapper->add(
            'origin.name',
            null,
            ['label' => 'logEntry.label.origin']
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
                'log_type',
                null,
                ['label' => 'logEntry.label.log_type']
            )
            ->add(
                'log_subtype',
                null,
                ['label' => 'logEntry.label.log_subtype']
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
            ->add(
                'src_ip',
                null,
                ['label' => 'logEntry.label.src_ip']
            )
            ->add(
                'dst_ip',
                null,
                ['label' => 'logEntry.label.dst_ip']
            )
            ->add(
                'origin.name',
                null,
                ['label' => 'logEntry.label.origin']
            )
            ->add(
                'outcomes',
                null,
                [
                    'label' => 'logEntry.label.outcomes',
                    'associated_property' => 'id'
                ]
            )
            ->end();
    }
}
