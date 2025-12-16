<?php

namespace App\Form;

use App\Entity\Materiel;
use App\Entity\CategoryMateriel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class MaterielType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('titreMateriel', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nom du matériel est obligatoire')
                ]
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire')
                ],
                'label' => 'Description'
            ])
            ->add('stock', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le stock est obligatoire')
                ],
                'label' => 'Stock'
            ])
            ->add('prixPiece', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le prix pièce est obligatoire')
                ],
                'label' => 'Prix pièce',
                'currency' =>'EUR'
            ])
            ->add('caution', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le prix de la caution est obligatoire')
                ],
                'label' => 'Caution',
                'currency' =>'EUR'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du matériel',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeType' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg, jpg, png, webp).',
                    ])
                ],
            ])
            ->add('altTexte', TextType::class, [
                'required' => true,
                'label' => 'Texte alternatif de l’image'
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryMateriel::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
            'is_edit' => false
        ]);
    }
}