<?php

namespace App\Form;

use App\Entity\Serie;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('backdrop_file', Type\FileType::class, ['required' => false,'mapped' => false, 'label' => 'Image de fond',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'votre fichier est trop lourd (>1024ko)',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Les formats acceptés sont png jpeg et jpg'
                    ])
                ]])
            ->add('poster_file', Type\FileType::class, ['required' => false, 'mapped' => false, 'label' => 'Affiche'])
            ->add('submit',Type\SubmitType::class, ['label' => 'Ajouter'])
//            ->add('delete',Type\SubmitType::class, ['label' => 'Supprimer', 'attr' => ['class' => 'btn btn-danger']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
