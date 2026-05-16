<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_categorie', TextType::class, [
                'label' => 'Nom de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('couleur_categorie', ChoiceType::class, [
                'label' => 'Couleur de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisissez une couleur',
                'required' => false,
                'choices' => [
                    'Rouge' => 'rgb(255, 99, 132)',
                    'Vert' => 'rgb(75, 192, 192)',
                    'Bleu' => 'rgb(54, 162, 235)',
                    'Jaune' => 'rgb(255, 206, 86)',
                    'Orange' => 'rgb(255, 159, 64)',
                    'Violet' => 'rgb(153, 102, 255)',
                    'Noir' => 'rgb(0, 0, 0)',
                    'Blanc' => 'rgb(255, 255, 255)',
                ],
            ])
            ->add('icone_categorie', ChoiceType::class, [
                'label' => 'Icône de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'placeholder' => 'Choisissez une icône',
                'choices' => [
                    '<i class="fas fa-car"></i>' => '<i class="fas fa-car"></i>',
                    '<i class="fas fa-bicycle"></i>' => '<i class="fas fa-bicycle"></i>',
                    '<i class="fas fa-bus"></i>' => '<i class="fas fa-bus"></i>',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
