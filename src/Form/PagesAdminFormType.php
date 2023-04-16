<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
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
            ->add('page_name', TextType::class, [
                'label' => 'Nom de la page'
            ])
            ->add('page_url', TextType::class, [
                'label' => 'URL de la page',
                'required' => false,
            ])
            ->add('page_content', CKEditorType::class, [
                'label' => 'Contenu de la page (FR)',
                'mapped' => false
            ])
            ->add('page_meta_title', TextType::class, [
                'label' => 'Balise Meta Title (FR)',
                'required' => false,
            ])
            ->add('page_meta_desc', TextareaType::class, [
                'label' => 'Balise Meta Description (FR)',
                'required' => false,
            ])
            ->add('page_content_en', CKEditorType::class, [
                'label' => 'Contenu de la page (EN)',
                'mapped' => false
            ])
            ->add('page_meta_title_en', TextType::class, [
                'label' => 'Balise Meta Title (EN)',
                'required' => false,
            ])
            ->add('page_meta_desc_en', TextareaType::class, [
                'label' => 'Balise Meta Description (EN)',
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
