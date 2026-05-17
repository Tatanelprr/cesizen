<?php

namespace App\Form;

use App\Entity\DiagnosticEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiagnosticEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'Événement'])
            ->add('points', IntegerType::class, ['label' => 'Points'])
            ->add('position', IntegerType::class, ['label' => 'Position', 'required' => false])
            ->add('isActive', CheckboxType::class, ['label' => 'Actif', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DiagnosticEvent::class]);
    }
}
