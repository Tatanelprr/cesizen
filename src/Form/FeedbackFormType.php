<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeedbackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de retour',
                'choices' => [
                    'Bug' => 'bug',
                    'Idée' => 'idea',
                    'Autre' => 'other',
                ],
                'placeholder' => 'Choisissez un type…',
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                    new Length(max: 100, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'),
                ],
                'attr' => ['placeholder' => 'Résumez le problème ou l\'idée en quelques mots'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 6, 'placeholder' => 'Décrivez en détail…'],
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire.'),
                    new Length(max: 2000, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email (optionnel)',
                'required' => false,
                'constraints' => [
                    new Email(message: 'L\'adresse email n\'est pas valide.'),
                ],
                'attr' => ['placeholder' => 'Pour que l\'équipe puisse vous répondre'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
