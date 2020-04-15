<?php

namespace App\Admin;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Origin;
use App\Entity\WordSet;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\EqualType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\CollectionType;

/**
 * Sonata Admin for the Origin.
 *
 *
 */
final class OriginAdmin extends AbstractAdmin
{
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'edit', 'show'));
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

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General', ['class' => 'col-md-5'])
            ->add(
                'name',
                null,
                ['label' => 'origin.label.name']
            )
            ->add(
                'subnet',
                null,
                ['label' => 'origin.label.subnet']
            )
            ->end()
            ;

        $showMapper
            ->with('Word Sets', ['class' => 'col-md-5'])
             ->add(
                 'wordsets',
                 null,
                 array(
                    'template' => 'origin_admin/wordsets_field.html.twig'
                )
             )
            ->end()
            ;
    }
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'WordSets',
                ['class' => 'col-md-6', 'label' => 'origin.title.wordSets']
            )
            ->end();

        $formMapper
            ->with('WordSets')
            ->add(
                'wordsets',
                ModelType::class,
                [
                    'label' => 'origin.label.wordSets',
                    'class' => WordSet::class,
                    'expanded' => true,
                    'by_reference' => false,
                    'multiple' => true
                ]
            )
            ->end();
    }
}
