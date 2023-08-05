<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class GlobalSettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo', DropzoneType::class, [
                'label' => "Logo du site",
                'required' => false,
            ])
            ->add('damping', NumberType::class, [
                'label' => 'Niveau de fludité',
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'required' => false
            ])
            ->add('scrollimg', NumberType::class, [
                'label' => 'Vitesse de défilement',
                'html5' => true,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
