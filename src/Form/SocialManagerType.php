<?php

namespace App\Form;

use App\Entity\SocialManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $socialMediaPlatforms = [
            'facebook' => 'Facebook',
            'twitter' => 'X (Anciennement Twitter)',
            'instagram' => 'Instagram',
            'youtube' => 'Youtube',
            'pinterest' => 'Pinterest',
            'slideshare' => 'Slideshare',
            'xing' => 'Xing',
            'angelList' => 'Angel List',
            'glassdoor' => 'Glassdoor',
            'behance' => 'Behance',
            'meetup' => 'Meetup',
            'reddit' => 'Reddit',
            'quora' => 'Quora',
            'whatsapp' => 'WhatsApp Business',
        ];

        foreach ($socialMediaPlatforms as $name => $label) {
            $builder->add($name, UrlType::class, [
                'label' => $label,
                'required' => false,
                'row_attr' => ['class' => 'mb'],
            ]);
        }

        $builder->add('submit', SubmitType::class, ['label' => 'Envoyer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialManager::class,
        ]);
    }
}
