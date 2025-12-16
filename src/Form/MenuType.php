<?php

namespace App\Form;

use App\Entity\Menu;
use App\Form\ImageMenuType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreMenu', TextType::class, [
                'constraints' => [new NotBlank(message: 'Le nom du menu est obligatoire')],
                'label' => 'Nom du menu',
            ])
            ->add('stock', IntegerType::class, [
                'constraints' => [new NotBlank(message: 'Le stock est obligatoire')],
                'label' => 'Stock',
            ])
            ->add('nbPersMin', IntegerType::class, [
                'constraints' => [new NotBlank(message: 'Le nombre de personnes minimum est obligatoire')],
                'label' => 'Nombre de personne minimum',
            ])
            ->add('prixParPersonne', MoneyType::class, [
                'constraints' => [new NotBlank(message: 'Le prix par personne est obligatoire')],
                'label' => 'Prix par personne',
                'currency' => 'EUR',
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [new NotBlank(message: 'La description est obligatoire')],
                'label' => 'Description',
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageMenuType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // important pour que Doctrine gère correctement les ajouts/suppressions
                'required' => false,     // images facultatives
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}