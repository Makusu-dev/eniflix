<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie')]
final class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
//    le ParameterbagInterface va aller chercher dans le fichier config/services.yaml où l'on a ajouter le
//      le nb d'entrées par page
    public function list(SerieRepository $serieRepository, int $page, ParameterBagInterface $parameterBag): Response
    {
//        on peut faire un findAll mais ici on veut n'afficher que les séries en cours (voir ci-dessous)
//        $series = $serieRepository->findAll();

//        là on va chercher les infos entrées dans le services.yaml
        $nbPerPage = $parameterBag->get('serie')['nb_max'];

        $offset = ($page - 1) * $nbPerPage;

        $criterias = [
//            'status' => 'Returning',
//            'genres' => 'Comedy'
        ];

        $series = $serieRepository->findBy(
            $criterias,
            [
                'popularity' => 'DESC',
            ],
            $nbPerPage,
            $offset,
        );

        $total = $serieRepository->count($criterias);
        $totalPages = $total / $nbPerPage;

        return $this->render('serie/list.html.twig',
            [
                'series' => $series,
                'totalPages' => $totalPages,
                'page' => $page,

            ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);
        return $this->render('serie/detail.html.twig', [
                'serie' => $serie,
                'id' => $serie->getId(),]
        );
    }

    #[Route('custom', name: '_custom', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function listCustom(SerieRepository $serieRepository): Response
    {
        $serieCustom = $serieRepository->findSerieCustom(400, 5);
        return $this->render('serie/custom.html.twig', [

            'serieCustom' => $serieCustom,
        ]);
    }
    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            dd($serie);
            $serie->setDateCreated(new \DateTime());;
            $em->persist($serie);
            $em->flush();
            $this->render('serie_detail', ['id'=>$serie->getId()]);


        }

        return $this->render('serie/edit.html.twig',['serie_form' => $form]);
    }


}
