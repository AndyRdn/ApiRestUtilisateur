<?php

namespace App\Controller\API;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/login")]
class LoginController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UtilisateurRepository $utilisateurRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->utilisateurRepository = $utilisateurRepository;
    }


    #[Route("/login", methods: ["POST"])]
    public function login(string $email, string $mdp)
    {
        $utilisateur=$this->utilisateurRepository->findByLogin($email);
//        $hashMdp=$this->passwordHasher->hashPassword($utilisateur, $utilisateur->getPlainPassword());

//        if ()) {
//            // Connexion réussie
//        }
    }

}