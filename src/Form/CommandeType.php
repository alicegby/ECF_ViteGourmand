<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\StatutCommande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statutCommande', EntityType::class, [
                'class' => StatutCommande::class,
                'choice_label' => 'libelle',
                'label' => 'Statut de la commande',
                'placeholder' => 'Sélectionnez un statut'
            ])
            ->add('pretMateriel', CheckboxType::class, [
                'label' => 'Matériel loué',
                'required' => false
            ])
            ->add('restitutionMateriel', CheckboxType::class, [
                'label' => 'Matériel restitué',
                'required' => false
            ])
            ->add('pretPersonnel', CheckboxType::class, [
                'label' => 'Personnel loué',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}