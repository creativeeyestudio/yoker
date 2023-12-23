<?php

namespace App\Form;

use App\Entity\CodeWeave;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodeWeaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom du fichier",
                'row_attr' => [
                    'class' => 'mb'
                ]
            ])

            ->add('code', TextareaType::class, [
                'label' => "Code",
                'row_attr' => [
                    'class' => 'mb',
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => "Envoyer",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CodeWeave::class,
        ]);
    }
}
