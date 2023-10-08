<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PagesAdminFormType extends AbstractType
{
    private $tokenStorage;
    private $token;
    private $user;
    private $blkAcc;

    public function __construct(TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
        $this->token = $this->tokenStorage->getToken();
        if ($this->token && $this->token->getuser()) {
            $this->user = $this->token->getUser();
        }
    }

    public function blockedPageAccess(){
        if ($this->user->getRoles()[0] === 'ROLE_ADMIN' || $this->user->getRoles()[0] === 'ROLE_EDITOR') {
           $this->blkAcc = CheckboxType::class;
        } else {
            $this->blkAcc = HiddenType::class;
        }
        return $this->blkAcc;
    }

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
            ->add('main_page', CheckboxType::class, [
                'label' => "Page principale",
                'required' => false
            ])
            ->add('page_url', TextType::class, [
                'label' => 'URL de la page',
                'required' => false,
            ])
            ->add('status', $this->blockedPageAccess(), [
                'label' => "Page visible",
                'required' => false, 
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
