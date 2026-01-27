<?php

namespace App\Form;

use App\Entity\Plats;
use App\Entity\Fromages;
use App\Entity\Boissons;
use App\Entity\Materiel;
use App\Entity\Personnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $commande = $options['data'];
        $minPersonne = $commande->getNbPersonne(); // minimum à partir de la commande

        $builder
            // Nombre de personnes avec min
            ->add('nbPersonne', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'attr' => ['min' => $minPersonne],
            ])

            // Plats du menu (modifiable)
            ->add('menuPlats', EntityType::class, [
                'class' => Plats::class,
                'choice_label' => 'titrePlat',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false, // on gère manuellement
                'required' => true,
                'data' => $commande->getCommandePlats()->map(fn($cp) => $cp->getPlat())->toArray(),
            ])

            // Options : fromages, boissons, matériel, personnel
            ->add('commandeFromages', EntityType::class, [
                'class' => Fromages::class,
                'choice_label' => 'titreFromage',
                'multiple' => true,
                'expanded' => false, // juste quantité
                'mapped' => false,
                'required' => false,
            ])
            ->add('commandeBoissons', EntityType::class, [
                'class' => Boissons::class,
                'choice_label' => 'titreBoisson',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('commandeMateriels', EntityType::class, [
                'class' => Materiel::class,
                'choice_label' => 'titreMateriel',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('commandePersonnels', EntityType::class, [
                'class' => Personnel::class,
                'choice_label' => 'titrePersonnel',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Commande::class,
            'is_edit' => false,
        ]);
    }
}