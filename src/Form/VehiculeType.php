<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('immat_vehicule')
            ->add('surnom_vehicule')
            ->add('type_vehicule')
            ->add('marque_vehicule')
            ->add('modele_vehicule')
            ->add('annee_circulation_vehicule')
            ->add('energie_vehicule')
            ->add('kmAchat_vehicule')
            ->add('image_vehicule')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
