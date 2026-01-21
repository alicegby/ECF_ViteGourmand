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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('icone', FileType::class, [
                'label' => 'Icône du badge',
                'mapped' => false,  
                'required' => !$options['is_edit'],
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg','image/jpg','image/png','image/webp'],
                        mimeTypesMessage: 'Veuillez télécharger une image valide (jpeg, jpg, png, webp).'
                    )
                ],
            ])
            ->add('conditionObtention', TextType::class, [
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
            'data_class' => Badge::class,
            'is_edit' => false
        ]);
    }
}