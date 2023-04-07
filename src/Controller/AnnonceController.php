<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Commentary;
use App\Entity\Acquisition; 

use App\Repository\UserRepository;

use App\Form\CommentaryType; 
use App\Form\AcquisitionType; 

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\SecurityBundle\Security;

class AnnonceController extends AbstractController
{
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/annonce', name: 'app_annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }

    /**
     * @param Annonce $annonce
     * @return Response
     */
    #[Route('/annonce/find/{annonce}', name: 'app_annonce_id')]
    public function getId(Security $security, Annonce $annonce, UserRepository $userRepository, Request $request): Response
    { 
        $user = $security->getUser();
        
        // * ADD COMMENT FORM //
        $commentary = new Commentary;  
        $form = $this->createForm(CommentaryType::class, $commentary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $annonce->addCommentary($commentary);

            $user = $userRepository->find($this->getUser());
            $user->addCommentary($commentary); 
            
            $this->em->persist($commentary);
            $this->em->flush();

            return $this->redirectToRoute('app_annonce_id', ['annonce' => $annonce->getId()]);
        }

        // * BUY FORM //
        // $acquisition = new Acquisition; 
        // $formAcquisition = $this->createForm(AcquisitionType::class, $acquisition);
        
        // $formAcquisition->handleRequest($request);
        
        // if ($formAcquisition->isSubmitted()) {
            
        //     $annonce->setAcquisition($acquisition); 

        //     $user = $userRepository->find($this->getUser());
        //     $user->addAcquisition($acquisition); 
            
        //     $this->em->persist($acquisition);
        //     $this->em->flush();

        //     return $this->redirectToRoute('app_acquisition_id', ['acquisition' => $acquisition->getId()]);
        // }
        
        // * GET COMMENT //
        $commentaries = $annonce->getCommentaries();

        // * RENDER //
        return $this->render('annonce/getId.html.twig', [
            'annonce' => $annonce,
            'commentaries' => $commentaries,
            'form' => $form,
            // 'formAcquisition'=> $formAcquisition,  
            'user' => $user, 
        ]);
    }
}
