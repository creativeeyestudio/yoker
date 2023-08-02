<?php

namespace App\Form;

use App\Entity\PagesList;
use App\Entity\PostsList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavLinksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pages', EntityType::class, [
                'class' => PagesList::class,
                'choice_label' => 'page_name[0]',
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('posts', EntityType::class, [
                'class' => PostsList::class,
                'choice_label' => 'post_name[0]',
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('cus_name', TextType::class, [
                'label' => "Nom du lien",
                'required' => false
            ])
            ->add('cus_link', TextType::class, [
                'label' => "URL du lien",
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer"
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
