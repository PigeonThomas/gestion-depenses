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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depense',DateType::class, [
                'label' => 'Date de la dépense',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
            ])
            ->add('montant_depense', MoneyType::class, [
                'label' => 'Montant de la dépense',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00'],
                'scale' => 2,
            ])
            ->add('commentaire_depense', TextType::class, [
                'label' => 'Commentaire',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('factureFile', FileType::class, [
                'label' => 'Reçu/Facture (PDF, JPG, PNG)',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('km_vehicule', NumberType::class, [
                'label' => 'Kilométrage du véhicule',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('carbu_prix_litre', MoneyType::class, [
                'label' => 'Prix du carburant par litre',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => '0.000'],
                'scale' => 3,
                'required' => false,
            ])
            ->add('repa_description', TextType::class, [
                'label' => 'Description de la réparation',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('repa_ref_pieces', TextType::class, [
                'label' => 'Référence des pièces',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('repaPhotosFile', FileType::class, [
                'label' => 'Photos de la réparation (3 photos maximum)',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie de dépense',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez une catégorie',
                'class' => Categorie::class,
                'choice_label' => 'nom_categorie',
            ])
            ->add('magasin', EntityType::class, [
                'label' => 'Magasin',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un magasin',
                'class' => Magasin::class,
                'choice_label' => 'nom_magasin',
            ])
            ->add('vehicule', EntityType::class, [
                'label' => 'Véhicule',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un véhicule',
                'class' => Vehicule::class,
                'choice_label' => 'surnom_vehicule',
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un utilisateur',
                'class' => User::class,
                'choice_label' => 'nom_user',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer la dépense',
                'attr' => ['class' => 'btn btn-primary mt-3'],
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
