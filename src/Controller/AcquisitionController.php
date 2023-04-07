<?php

namespace App\Controller;

use App\Entity\Acquisition;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\SecurityBundle\Security;

class AcquisitionController extends AbstractController
{
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/acquisition', name: 'app_acquisition')]
    public function index(): Response
    {
        return $this->render('acquisition/index.html.twig', [
            'controller_name' => 'AcquisitionController',
        ]);
    }

    //     /**
    //  * @param Acquisition $acquisition
    //  * @return Response
    //  */
   
    // #[Route('/acquisition/find/{acquisition}', name: 'app_acquisition_id')]
    // public function getId(Acquisition $acquisition, UserRepository $userRepository): Response
    // { 
    //     $user = $userRepository->find($this->getUser());
    //     $addresses = $user->getAddresses(); 

    //     return $this->render('acquisition/getId.html.twig', [
    //         'acquisition' => $acquisition,
    //         'addresses' => $addresses, 
    //     ]);
    // }
}
