<?php

namespace App\Form;

use App\Entity\Plats;
use App\Entity\CategoryFood;
use App\Entity\Allergenes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class PlatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titrePlat', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nom du plat est obligatoire')
                ],
                'label' => 'Nom du plat'
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire')
                ],
                'label' => 'Description'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du plat',
                'mapped' => false,
                'required' => !$options['is_edit'],  // image obligatoire seulement en création
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg','image/jpg','image/png','image/webp'],
                        mimeTypesMessage: 'Veuillez télécharger une image valide (jpeg, jpg, png, webp).'
                    )
                ],
            ])
            ->add('altTexte', TextType::class, [
                'required' => true,
                'label' => 'Texte alternatif de l’image'
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryFood::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie'
            ])
            ->add('allergenes', EntityType::class, [
                'class' => Allergenes::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Allergènes'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plats::class,
            'is_edit' => false  
        ]);
    }
}