<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categorie = $builder->getData();
        $defaultColor = $options['default_color'];
        $currentColor = $categorie instanceof Categorie && $categorie->getCouleurCategorie()
            ? $categorie->getCouleurCategorie()
            : $defaultColor;
        $currentIcon = $categorie instanceof Categorie ? $categorie->getIconeCategorieClass() : null;
        $iconChoices = [
            'Pain' => 'fa-solid fa-bread-slice',
            'Boissons' => 'fa-solid fa-champagne-glasses',
            'Courses' => 'fas fa-shopping-cart',
            'Maison' => 'fas fa-home',
            'Sante' => 'fas fa-heart',
            'Loisirs' => 'fas fa-film',
            'Restaurant' => 'fas fa-utensils',
            'Voiture' => 'fas fa-car',
            'Velo' => 'fas fa-bicycle',
            'Bus' => 'fas fa-bus',
            'Panier' => 'fa-solid fa-basket-shopping',
            'Reparation' => 'fa-solid fa-wrench',
            'Repas' => 'fa-solid fa-plate-utensils',
            'Carburant' => 'fa-solid fa-gas-pump',
            'Achats' => 'fa-solid fa-bag-shopping',
        ];

        $builder
            ->add('nom_categorie', TextType::class, [
                'label' => 'Nom de la catégorie*',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('couleur_categorie', ColorType::class, [
                'label' => 'Couleur de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control form-control-color',
                    'title' => 'Choisissez une couleur',
                    'data-categorie-color-input' => 'true',
                ],
                'data' => $currentColor,
                'empty_data' => $defaultColor,
                'required' => false,
            ])
            ->add('icone_categorie', ChoiceType::class, [
                'label' => 'Icône de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => static fn () => ['class' => 'btn-check'],
                'data' => $currentIcon,
                'choices' => $iconChoices,
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
            'default_color' => '#3b82f6',
        ]);

        $resolver->setAllowedTypes('default_color', 'string');
    }
}
