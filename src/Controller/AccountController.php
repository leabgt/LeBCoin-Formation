<?php

namespace App\Controller;

use App\Entity\Address; 
use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\Annonce; 

use App\Form\AddressType;
use App\Form\WalletType; 

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AccountController extends AbstractController
{
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/account', name: 'account')]
    public function index(Security $security, UserRepository $userRepository): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $user = $userRepository->find($this->getUser());
        $addresses = $user->getAddresses(); 
        $wallet = $user->getWallet(); 

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'wallet' => $wallet, 
        ]);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/account/address/new', name: 'account_address_new')]
    public function newAddress(UserRepository $userRepository, Request $request)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $userRepository->find($this->getUser()); 
            $user->addAddress($address);
            
            $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/address_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param UserRepository $userRepository
     * @param Address $address
     * @param Request $request
     * @return Response
    */
    #[Route('/backoffice/address/update/{address}', name: 'account_address_update')]
    public function updateAddress(UserRepository $userRepository, Address $address, Request $request): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->find($this->getUser()); 
            $user->addAddress($address);

            $this->em->persist($address);
            $this->em->flush();
            return $this->redirectToRoute('account');
        }
        return $this->render('account/address_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Address $address
     * @return Response
     */
    #[Route('/account/address/delete/{address}', name: 'account_address_delete', methods: ['DELETE'])]
    public function deleteAddress(UserRepository $userRepository, Address $address, CsrfTokenManagerInterface $csrfTokenManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->get('_token'))) {
            $user = $userRepository->find($this->getUser()); 
            $user->addAddress($address);
            
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute("account");
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/account/wallet/new', name: 'account_wallet_new')]
    public function newWallet(UserRepository $userRepository, Request $request)
    { 
        $form = $this->createForm(WalletType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->find($this->getUser());
            $wallet = $user->getWallet(); 
            
            $originwallet = $wallet->getAmount();

            $amount = $form->get('amount')->getData(); 
            $wallet->setAmount($originwallet += $amount);
            
            $user->setWallet($wallet);
            
            $this->em->persist($wallet);
            $this->em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/wallet_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/account/annonce/new', name: 'account_annonce_new')]
    public function newAnnonce(UserRepository $userRepository, Request $request)
    {
        $annonce = new Annonce();
        $form = $this->createForm(AddressType::class, $annonce);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $userRepository->find($this->getUser()); 
            $user->addAnnonce($annonce);
            
            $this->em->persist($annonce);
            $this->em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/address_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
