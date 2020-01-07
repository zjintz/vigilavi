<?php

namespace App\Form;

use App\Entity\Headquarter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A Form of for the Headquarters to be embeded in the registration form.
 *
 *
 */
class HeadquarterType extends AbstractType
{
    private $translator;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['label' => $this->translator->trans('hq.label.name')]
            )
            ->add(
                'city',
                TextType::class,
                ['label' => $this->translator->trans('hq.label.city')]
            )
            ->add(
                'country',
                TextType::class,
                ['label' => $this->translator->trans('hq.label.country')]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Headquarter::class,
        ]);
    }
}
