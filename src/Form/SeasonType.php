<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate', Type\DateType::class, [
                'widget' => 'single_text'])
            ->add('overview')
            ->add('poster')
            ->add('tmdb_id')
            ->add('serie', EntityType::class, [
                'class' => Serie::class,
                'choice_label' => function (Serie $serie) {
                return $serie->getSeasons()-> count() . ' saisons - ' . $serie->getName();
                    },
                'query_builder' => function (SerieRepository $er) {
                return $er->createQueryBuilder('s')
                       ->orderBy('s.name', 'ASC');
                }
            ])
            ->add('submit',Type\SubmitType::class, ['label' => 'Ajouter'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
