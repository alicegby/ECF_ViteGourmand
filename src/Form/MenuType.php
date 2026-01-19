<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Theme;
use App\Entity\Regime;
use App\Entity\Condition;
use App\Form\ImageMenuType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [new NotBlank(message: 'Le nom du menu est obligatoire')],
                'label' => 'Nom du menu',
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'libelle', 
                'required' => false,
            ])

            ->add('regime', EntityType::class, [
                'class' => Regime::class,
                'choice_label' => 'libelle',
                'required' => false,
            ])

            ->add('conditions', EntityType::class, [
                'class' => Condition::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
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
                'by_reference' => false, 
                'required' => false,    
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}