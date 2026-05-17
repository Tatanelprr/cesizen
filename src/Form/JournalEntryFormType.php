<?php

namespace App\Form;

use App\Entity\Emotion;
use App\Entity\JournalEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emotion', EntityType::class, [
                'class'        => Emotion::class,
                'choice_label' => fn(Emotion $e) => $e->getCategory()->getLibelle() . ' › ' . $e->getLibelle(),
                'label'        => 'Émotion ressentie',
                'group_by'     => fn(Emotion $e) => $e->getCategory()->getLibelle(),
                'query_builder' => fn($repo) => $repo->createQueryBuilder('e')
                    ->join('e.category', 'c')
                    ->where('e.isActive = true')
                    ->orderBy('c.libelle')
                    ->addOrderBy('e.libelle'),
            ])
            ->add('intensite', IntegerType::class, [
                'label' => 'Intensité (1 à 10)',
                'attr'  => ['min' => 1, 'max' => 10, 'type' => 'range'],
            ])
            ->add('notePerso', TextareaType::class, [
                'label'    => 'Note personnelle (optionnelle)',
                'required' => false,
                'attr'     => ['rows' => 4, 'placeholder' => 'Comment vous sentez-vous ?'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => JournalEntry::class]);
    }
}
