<?php

namespace App\Form;

use App\Entity\Horaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HoraireType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder 
            ->add('jour', ChoiceType::class, [
                'choices' => [
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ],
                'label' => 'Jour',
                'constraints' => [new NotBlank()]
            ])
            ->add('heureOuverture', TimeType::class, [
                'label' => ' Heure d\'ouverture',
                'widget' => 'choice',
                'constraints' => [new NotBlank()]
            ])
            ->add('heureFermeture', TimeType::class, [
                'label' => ' Heure de fermeture',
                'widget' => 'choice',
                'constraints' => [new NotBlank()]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Horaire::class,
        ]);
    }
}