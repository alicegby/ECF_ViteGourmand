<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'attr' => ['placeholder' => 'Votre nom'],
                'required' => true,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : ',
                'attr' => ['placeholder' => 'Votre prénom'],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email : ',
                'attr' => ['placeholder' => 'Votre email'],
                'required' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Vous êtes : ',
                'choices' => [
                    'Particulier' => 'particulier',
                    'Société' => 'societe'
                ],
                'expanded' => true,  
                'multiple' => false,
                'required' => true,
            ])
            ->add('theme', ChoiceType::class, [
                'label' => 'Sujet : ',
                'choices' => [
                    'Demande de contact' => 'contact',
                    'Restitution du matériel' => 'restitution',
                    'Question' => 'question',
                    'Autre' => 'autre'
                ],
                'placeholder' => 'Sélectionnez un sujet',
                'required' => true,
            ])
            ->add('objet', TextType::class, [
                'label' => 'Objet : ',
                'attr' => ['placeholder' => 'Titre de votre message'],
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message : ',
                'attr' => ['placeholder' => 'Écrivez votre message ici...'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}