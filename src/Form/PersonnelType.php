<?php

namespace App\Form;

use App\Entity\Personnel;
use App\Entity\CategoryPersonnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PersonnelType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('titrePersonnel', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le titre du personnel est obligatoire')
                ],
                'label' => 'Titre du personnel'
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
            ->add('prixParHeure', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le prix par heure est obligatoire')
                ],
                'label' => 'Prix par heure',
                'currency' =>'EUR'
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryPersonnel::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
            'is_edit' => false
        ]);
    }
}