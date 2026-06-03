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

        $builder
            ->add('nom_categorie', TextType::class, [
                'label' => 'Nom de la catégorie*',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('couleur_categorie', ColorType::class, [
                'label' => 'Couleur de la catégorie*',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control form-control-color',
                    'title' => 'Choisissez une couleur',
                    'data-categorie-color-input' => 'true',
                ],
                'data' => $currentColor,
                'empty_data' => $defaultColor,
                'required' => true,
            ])
            ->add('icone_categorie', ChoiceType::class, [
                'label' => 'Icône de la catégorie',
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => static fn () => ['class' => 'btn-check'],
                'data' => $currentIcon,
                'choices' => Categorie::ICON_CHOICES,
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
