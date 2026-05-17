<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('content', TextareaType::class, ['label' => 'Contenu', 'attr' => ['rows' => 12]])
            ->add('category', EntityType::class, [
                'class'        => ArticleCategory::class,
                'choice_label' => 'name',
                'label'        => 'Catégorie',
                'required'     => false,
                'placeholder'  => '-- Sans catégorie --',
            ])
            ->add('isPublished', CheckboxType::class, [
                'label'    => 'Publier immédiatement',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Article::class]);
    }
}
