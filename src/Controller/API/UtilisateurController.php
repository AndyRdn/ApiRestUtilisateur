<?php

namespace App\Controller\API;

use App\Enum\EmailSubject;
use App\Service\EmailService;
use App\Service\JwtTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/utilisateur")]
class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private JwtTokenManager $tokenManager;
    private EmailService $email;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
    }

    #[Route("/signup", name: "signin", methods: ["GET"])]
    public function signup(MailerInterface $mailer) {

//        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
//        $user->setMotDePasse($hashedPassword);

        $mailer->send($this->email->createMail("miarantsoasuper3000@gmail.com", EmailSubject::AUTHENTIFICATION->value));
        return $this->json("Email sent", 200, [], []);
    }

    #[Route("/signin", methods: ["POST"])]
    public function login(): JsonResponse {

//        if ($passwordHasher->isPasswordValid($user, $plainPassword)) {
//
//        }
        return $this->json("haha", 200, [], []);
    }

}