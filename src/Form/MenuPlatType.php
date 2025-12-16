<?php

namespace App\Form;

use App\Entity\MenuPlat;
use App\Entity\Menu;
use App\Entity\Plats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuPlatType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('menu', EntityType::class, [
            'class' => Menu::class,
            'choice_label' => 'nom',
            'label' => 'Menu',
            'placeholder' => 'Sélectionnez un menu'
        ])
        ->add('plat', EntityType::class, [
            'class' => Plats::class,
            'choice_label' => 'titrePlat',
            'label' => 'Plat',
            'placeholder' => 'Sélectionnez un plat'
        ])
        ->add('ordre', IntegerType::class, [
            'label' => 'Ordre dans le menu',
            'required' =>true,
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuPlat::class,
        ]);
    }
}