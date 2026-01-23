<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire'),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'L’email est obligatoire'),
                ],
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => !$isEdit, // obligatoire si création
                'mapped' => false,       // on hash dans le controller
                'attr' => [
                    'placeholder' => $isEdit ? 'Laisser vide pour conserver le mot de passe' : 'Mot de passe',
                    'minlength' => 12,
                ],
                'constraints' => $isEdit ? [] : [
                    new NotBlank(message: 'Le mot de passe est obligatoire'),
                    new Length(
                        min: 12,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères'
                    ),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'libelle',
                'label' => 'Rôle',
                'required' => true,
                'placeholder' => 'Sélectionnez un rôle',
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
            'is_edit' => false, // option pour savoir si on est sur un edit
        ]);
    }
}