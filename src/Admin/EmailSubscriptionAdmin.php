<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


final class EmailSubscriptionAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        // All routes are removed
        $collection->clear();
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('isActive', CheckboxType::class,[
            'label' => 'email_subscription.label.is_active',
            'required' => false,
            'help' => 'email_subscription.help.is_active'
        ]);
    }

}
