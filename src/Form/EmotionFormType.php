<?php

namespace App\Form;

use App\Entity\Emotion;
use App\Entity\EmotionCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmotionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'Nom'])
            ->add('codeColor', ColorType::class, ['label' => 'Couleur'])
            ->add('icon', TextType::class, ['label' => 'Icône (emoji)', 'required' => false])
            ->add('category', EntityType::class, [
                'class'        => EmotionCategory::class,
                'choice_label' => 'libelle',
                'label'        => 'Catégorie (émotion de base)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Emotion::class]);
    }
}
