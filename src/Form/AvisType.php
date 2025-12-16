<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\StatutAvis;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', EntityType::class, [
                'class' => StatutAvis::class,
                'choice_label' => 'libelle',
                'label' => 'Statut de l’avis',
                'placeholder' => 'Sélectionner un statut',
            ])
            ->add('validePar', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function(Employe $employe) {
                    return $employe->getNom() . ' ' . $employe->getPrenom();
                },
                'label' => 'Validé par',
                'placeholder' => 'Non assigné',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}