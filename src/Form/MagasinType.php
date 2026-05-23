<?php

namespace App\Form;

use App\Entity\Magasin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MagasinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_magasin', TextType::class, [
                'label' => 'Nom du magasin',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Kokopelli, Carrefour, etc.'],
            ])
            ->add('online_magasin', CheckboxType::class, [
                'label' => 'En ligne',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-check-input'],
                'choice' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
            ->add('adresse_magasin', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 123 Rue Exemple, Ville, Pays'],
                'required' => false,
            ])
            ->add('lien_magasin', TextType::class, [
                'label' => 'Lien',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: https://www.exemple.com'],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer le magasin',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Magasin::class,
        ]);
    }
}
