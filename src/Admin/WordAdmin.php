<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\Form\Type\EqualType;

/**
 * Sonata Admin for the Word entity.
 *
 *
 */
final class WordAdmin extends AbstractAdmin
{
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'General',
                ['class' => 'col-md-7', 'label' => 'word.title.general']
            )
            ->end();

        $formMapper
            ->with('General')
            ->add('text', TextType::class, ['label' => 'word.label.text',])
            ->add(
                'language',
                ChoiceType::class,
                ['choices'  => [
                          'English' => 'English',
                          'Español' => 'Español',
                          'Português' => 'Português',
                      ],
                 'expanded' => false,
                 'multiple' => false,
                 'required' => true,
                 'label' => 'word.label.language',
                ]
            )
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('text', null, [
            'operator_type' => 'sonata_type_equal',
            'advanced_filter' => false
        ]);
        $datagridMapper->add('language', null, [
            'operator_type' => 'sonata_type_equal',
            'advanced_filter' => false
        ]);
    }


    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper->addIdentifier(
            'text',
            null,
            ['label' => 'word.label.text']
        );
        $listMapper->add(
            'language',
            null,
            ['label' => 'word.label.language']
        );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->with('General', ['class' => 'col-md-7'])->end();

        $showMapper
            ->with('General', ['label' => 'word.title.general'])
            ->add('text', null, ['label' => 'word.label.text'])
            ->add('language', null, ['label' => 'word.label.language'])
            ->end()
            ;
    }
}
