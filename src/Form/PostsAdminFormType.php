<?php

namespace App\Form;

use App\Entity\PostsList;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class PostsAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // FR
            ->add('post_name_fr', TextType::class, [
                'label' => "Nom de l'article (FR)",
                'mapped' => false
            ])
            ->add('post_content_fr', CKEditorType::class, [
                'label' => "Contenu de l'article (FR)",
                'mapped' => false
            ])
            ->add('post_meta_title_fr', TextType::class, [
                'label' => "Meta Title de l'article (FR)",
                'required' => false,
                'mapped' => false
            ])
            ->add('post_meta_desc_fr', TextareaType::class, [
                'label' => "Meta Description de l'article (FR)",
                'required' => false,
                'mapped' => false
            ])

            // GLOBAL
            ->add('post_thumb', DropzoneType::class, [
                'label' => "Image de l'article",
                'required' => false,
                'mapped' => false
            ])
            ->add('post_url', HiddenType::class, [
                'label' => "URL de l'article (Optionnel)",
                'required' => false
            ])
            ->add('online', ChoiceType::class, [
                'label' => "Visibilité de l'article",
                'choices' => [
                    "En brouillon" => false,
                    "Publié" => true
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Enregistrer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostsList::class,
        ]);
    }
}
