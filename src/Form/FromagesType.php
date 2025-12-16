<?php

namespace App\Form;

use App\Entity\Fromages;
use App\Entity\CategoryCheese;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class FromagesType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('titreFromage', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nom du fromage est obligatoire')
                ],
                'label' => 'Nom de la boisson'
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
            ->add('minCommande', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le minimum de commande est obligatoire')
                ],
                'label' => 'Minimum de commande'
            ])
            ->add('prixParFromage', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le prix par fromage est obligatoire')
                ],
                'label' => 'Prix par fromage',
                'currency' =>'EUR'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du fromage',
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
                'class' => CategoryCheese::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Fromages::class,
            'is_edit' => false
        ]);
    }
}