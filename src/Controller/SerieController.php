<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use helper\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/serie', name: 'serie')]
#[ISGranted('ROLE_USER')]
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

        $series = $serieRepository->getSeriesWithSeasons($nbPerPage, $offset);

//        $series = $serieRepository->findBy(
//            $criterias,
//            [
//                'popularity' => 'DESC',
//            ],
//            $nbPerPage,
//            $offset,
//        );

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
    public function detail(Serie $serie): Response
    {
        return $this->render('serie/detail.html.twig', [
                'serie' => $serie
        ]);
    }


    #[Route('/create', name: '_create')]
    #[ISGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em, FileUploader $fu,ParameterBagInterface $Parameters): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            dd($serie);
            $posterFile=$form->get('poster_file')->getData();
            $backdropFile=$form->get('backdrop_file')->getData();



            if($posterFile instanceof UploadedFile){
                $dir =$Parameters->get('serie')['poster_dir'];
                $fu->upload(
                    $posterFile,
                    $serie->getName(),
                    'uploads/posters/series');
//                $name=$slugger->slug($serie->getName()).'-'.uniqid().'.'.$posterFile->guessExtension();
//                $posterFile->move('uploads/posters/series', $name);
//                $serie->setPoster($name);
            }

            if($backdropFile instanceof UploadedFile){
                $dir =$Parameters->get('serie')['poster_dir'];
                $fu->upload(
                    $backdropFile,
                    $serie->getName(),
                    'uploads/posters/series');
            }


            $em->persist($serie);
            $em->flush();
            $this->addFlash('success', 'Une série a été enregistrée');
            return $this->redirectToRoute('serie_detail', ['id'=>$serie->getId()]);
        }


        return $this->render('serie/edit.html.twig',['serie_form' => $form]);
    }


    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    #[ISGranted('ROLE_ADMIN')]
    public function update(Serie $serie, Request $request, EntityManagerInterface $em,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $posterFile=$form->get('poster_file')->getData();
            $backdropFile=$form->get('backdrop_file')->getData();

            if($posterFile instanceof UploadedFile){
                $name=$slugger->slug($serie->getName()).'-'.uniqid().'.'.$posterFile->guessExtension();
                $posterFile->move('uploads/posters/series', $name);
                if($serie->getPoster() && file_exists('uploads/posters/series/'.$serie->getPoster())){
                    unlink('uploads/posters/series/'.$serie->getPoster());
                }
                $serie->setPoster($name);
            }

            if($backdropFile instanceof UploadedFile){
                $name=$slugger->slug($serie->getName()).'-'.uniqid().'.'.$backdropFile->guessExtension();
                $backdropFile->move('uploads/backdrops', $name);
                if($serie->getBackdrop() && file_exists('uploads/backdrops/'.$serie->getBackdrop())){
                    unlink('uploads/backdrops/'.$serie->getBackdrop());
                }
                $serie->setBackdrop($name);
            }


            $em->flush();
            $this->addFlash('success', 'Une série a été mise à jour');
            return $this->redirectToRoute('serie_detail', ['id'=>$serie->getId()]);
        }

        return $this->render('serie/edit.html.twig',['serie_form' => $form]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Serie $serie, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->get('token'))) {
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été supprimée');
        } else {
            $this->addFlash('danger', 'Suppression impossible');
        }

        return $this->redirectToRoute('serie_list');
    }


}
