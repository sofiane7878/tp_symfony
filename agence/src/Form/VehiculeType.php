<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Pour une liste déroulante
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Pour un champ texte simple
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'required' => true,
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'required' => true,
            ])
            ->add('immatricule', TextType::class, [
                'label' => 'Immatricule',
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [ // Exemple avec une liste déroulante
                'label' => 'Type de véhicule',
                'choices' => [
                    'SUV' => 'SUV',
                    'Citadine' => 'Citadine',
                    '4x4' => '4x4',
                ],
                'required' => true,
                'placeholder' => 'Sélectionnez un type',
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'required' => true,
                'attr' => [
                    'min' => 25,
                    'max' => 60,
                    'step' => 0.01,
                ],
            ])
            ->add('statut', ChoiceType::class, [ // Exemple pour le statut (disponible ou non)
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => true,
                    'Non disponible' => false,
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
