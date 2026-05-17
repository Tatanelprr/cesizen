<?php

namespace App\Form;

use App\Entity\DiagnosticThreshold;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiagnosticThresholdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scoreMin', IntegerType::class, ['label' => 'Score minimum'])
            ->add('scoreMax', IntegerType::class, ['label' => 'Score maximum'])
            ->add('niveau', TextType::class, ['label' => 'Niveau (ex: Stress élevé)'])
            ->add('codeColor', ColorType::class, ['label' => 'Couleur'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('conseil', TextareaType::class, ['label' => 'Conseils']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DiagnosticThreshold::class]);
    }
}
