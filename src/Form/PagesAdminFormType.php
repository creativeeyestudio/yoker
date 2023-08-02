<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagesAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            // Contenu en FR
            ->add('page_name_fr', TextType::class, [
                'label' => 'Nom de la page',
                'mapped' => false,
            ])
            ->add('page_content_fr', CKEditorType::class, [
                'label' => 'Contenu de la page (FR)',
                'mapped' => false,
                'attr' => [
                    // Ajoutez une classe CSS pour identifier le champ de texte
                    'class' => 'grapesjs-editor',
                ],
            ])
            ->add('page_meta_title_fr', TextType::class, [
                'label' => 'Balise Meta Title (FR)',
                'required' => false,
                'mapped' => false
            ])
            ->add('page_meta_desc_fr', TextareaType::class, [
                'label' => 'Balise Meta Description (FR)',
                'required' => false,
                'mapped' => false
            ])

            // Global
            ->add('page_url', TextType::class, [
                'label' => 'URL de la page',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => "Visibilité de la page",
                'choices' => [
                    "En brouillon" => false,
                    "Publié" => true
                ]
            ])
            ->add('blocked_page', CheckboxType::class, [
                'label' => 'Page fixe',
                'required' => false, 
            ])

            ->add('page_submit', SubmitType::class, [
                'label' => 'Enregistrer'
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
