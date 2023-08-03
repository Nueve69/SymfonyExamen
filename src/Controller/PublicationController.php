<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Comment;
use App\Form\CommentType;

class PublicationController extends AbstractController
{
    public function __construct(
        private PublicationRepository $publicationRepository
    ) {
    }
    /**
     * @Route("/", name="home")
     */
    #[Route('/publication', name: 'app_publication')]    
    public function index()
    {
        $publications = $this->publicationRepository->findAll();

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    // public function show(Publication $publication)
    // {
    //     return $this->render('publication/show.html.twig', [
    //         'publication' => $publication,
    //     ]);
    // }
    #[Route('publication/show/{id}', name: 'app_publication_show')]
    public function detail($id, Request $request)
    {

        $publicationEntity = $this->publicationRepository->find($id);

        if ($publicationEntity === null) {
            return $this->redirectToRoute('app_publication');
        }

        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime());
        $comment->setPublication($publicationEntity);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/show.html.twig', [
            'publication' => $publicationEntity,
            'form' => $form->createView()
        ]);
    }
    
    

    
}
