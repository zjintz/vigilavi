<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class HeadquarterAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        // All routes are removed
        $collection->clear();
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        $formMapper->add('city', TextType::class);
        $formMapper->add('country', TextType::class);
    }
}
