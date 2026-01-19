<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\ImageMenu;

class ImageMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image'
            ])
            ->add('altText', TextType::class, [
                'required' => false,
                'label' => 'Texte alternatif'
            ])
            ->add('estPrincipale', CheckboxType::class, [
                'required' => false,
                'label' => 'Image principale'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageMenu::class,
        ]);
    }
}