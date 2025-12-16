<?php

namespace App\Form;

use App\Entity\Reduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'Type de réduction',
                'constraints' => [new NotBlank(message: 'Le type est obligatoire')]
            ])
            ->add('conditionQuantite', IntegerType::class, [
                'label' => 'Condition quantité',
                'required' => false,
            ])
            ->add('reduction', NumberType::class, [
                'label' => 'Réduction (%)',
                'scale' => 2,
                'constraints' => [new NotBlank(message: 'Le montant de réduction est obligatoire')]
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reduction::class,
        ]);
    }
}