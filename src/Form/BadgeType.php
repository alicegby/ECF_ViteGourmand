<?php

namespace App\Form;

use App\Entity\Badge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du badge',
                'constraints' => [
                    new NotBlank(message: 'Le nom du badge est obligatoire')
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire')
                ]
            ])
            ->add('icone', TextType::class, [
                'label' => 'Icône (nom ou chemin)',
                'constraints' => [
                    new NotBlank(message: 'L’icône est obligatoire')
                ]
            ])
            ->add('conditionObtention', TextareaType::class, [
                'label' => 'Condition d’obtention',
                'constraints' => [
                    new NotBlank(message: 'La condition est obligatoire')
                ]
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Badge actif',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Badge::class
        ]);
    }
}