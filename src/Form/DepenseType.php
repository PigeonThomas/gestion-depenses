<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Depense;
use App\Entity\Magasin;
use App\Entity\User;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depense')
            ->add('montant_depense')
            ->add('commentaire_depense')
            ->add('facture_depense')
            ->add('km_vehicule')
            ->add('carbu_prix_litre')
            ->add('repa_description')
            ->add('repa_ref_pieces')
            ->add('repa_photos')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom_categorie',
            ])
            ->add('magasin', EntityType::class, [
                'class' => Magasin::class,
                'choice_label' => 'nom_magasin',
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => 'surnom_vehicule',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom_user',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
