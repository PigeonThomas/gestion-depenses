<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('immat_vehicule', TextType::class, [
                'label' => 'Immatriculation',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control','placeholder' => 'Ex: AB-123-CD'],
                'required' => false,
            ])
            ->add('surnom_vehicule', TextType::class, [
                'label' => 'Surnom',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type_vehicule', ChoiceType::class, [
                'label' => 'Type',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'Voiture' => 'voiture',
                    'Moto' => 'moto',
                    'Camion' => 'camion',
                    'Van' => 'van',
                    'Camping-car' => 'camping-car',
                    'Vélo' => 'velo',
                    'Trottinette' => 'trottinette',
                    'Bus' => 'bus',
                    'Hélicoptère' => 'helicoptere', 
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Choisissez un type',
                'required' => false,
            ])
            ->add('marque_vehicule', TextType::class, [
                'label' => 'Marque',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Toyota'],
                'required' => false,
            ])
            ->add('modele_vehicule', TextType::class,[
                'label' => 'Modèle',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Corolla'],
                'required' => false,
            ]) 
            ->add('annee_circulation_vehicule', DateType::class, [
                'label' => 'Année de mise en circulation',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('energie_vehicule', TextType::class, [
                'label' => 'Énergie',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Essence, Diesel, Électrique'],
                'required' => false,
            ])
            ->add('kmAchat_vehicule', IntegerType::class, [
                'label' => 'Kilométrage à l\'achat',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('image_vehicule', FileType::class, [
                'label' => 'Image du véhicule (JPG, PNG)',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer le nouveau véhicule',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
