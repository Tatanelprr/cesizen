<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('type', ChoiceType::class, [
                'label'   => 'Type',
                'choices' => [
                    'Méditation' => 'meditation',
                    'Sport'      => 'sport',
                    'Lecture'    => 'lecture',
                    'Musique'    => 'musique',
                    'Nature'     => 'nature',
                    'Autre'      => 'autre',
                ],
            ])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => ['rows' => 6]])
            ->add('urlMedia', UrlType::class, ['label' => 'Lien média', 'required' => false])
            ->add('isActive', CheckboxType::class, ['label' => 'Active', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Activity::class]);
    }
}
