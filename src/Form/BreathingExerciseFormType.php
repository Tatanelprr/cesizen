<?php

namespace App\Form;

use App\Entity\BreathingExercise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class BreathingExerciseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'exercice'])
            ->add('inhale', IntegerType::class, [
                'label'       => 'Inspiration (secondes)',
                'constraints' => [new Range(min: 1, max: 30)],
                'attr'        => ['min' => 1, 'max' => 30],
            ])
            ->add('hold', IntegerType::class, [
                'label'       => 'Apnée (secondes, 0 si aucune)',
                'constraints' => [new Range(min: 0, max: 30)],
                'attr'        => ['min' => 0, 'max' => 30],
            ])
            ->add('exhale', IntegerType::class, [
                'label'       => 'Expiration (secondes)',
                'constraints' => [new Range(min: 1, max: 30)],
                'attr'        => ['min' => 1, 'max' => 30],
            ])
            ->add('description', TextType::class, [
                'label'    => 'Description (optionnelle)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => BreathingExercise::class]);
    }
}
