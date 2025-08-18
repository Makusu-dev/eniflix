<?php

namespace App\Form;

use App\Entity\Serie;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'nom de la série',
                'required' => true,
            ])
            ->add('overview', Type\TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('status', Type\ChoiceType::class, [
                'label' => 'Statut',
                'choices'=> [
                'En cours' => 'returning',
                'Terminée' => 'ended',
                'Annulée' => 'Canceled'
            ],
                'placeholder' => '-- Choisissez un statut --',
                ])
            ->add('genres')
            ->add('firstAirDate', Type\DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('lastAirDate',Type\DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('backdrop', Type\FileType::class, ['required' => false, 'label' => 'Image de fond'])
            ->add('poster', Type\FileType::class, ['required' => false, 'label' => 'Poster'])
            ->add('submit',Type\SubmitType::class, ['label' => 'Ajouter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
