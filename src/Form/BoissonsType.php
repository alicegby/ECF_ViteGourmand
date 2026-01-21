<?php

namespace App\Form;

use App\Entity\Boissons;
use App\Entity\CategoryDrink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class BoissonsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('titreBoisson', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nom de la boisson est obligatoire')
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
            ->add('qteParPers', IntegerType::class, [
                'required' => true,
                'label' => 'Quantité par personne'
            ])
            ->add('minCommande', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le minimum de commande est obligatoire')
                ],
                'label' => 'Minimum de commande'
            ])
            ->add('prixParBouteille', MoneyType::class, [
                'constraints' => [new NotBlank(message: 'Le prix par bouteille est obligatoire')],
                'label' => 'Prix par bouteille',
                'currency' => 'EUR',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de la boisson',
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
            ->add('alt', TextType::class, [
                'required' => true,
                'label' => 'Texte alternatif de l’image'
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryDrink::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boissons::class,
            'is_edit' => false
        ]);
    }
}