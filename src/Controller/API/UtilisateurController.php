<?php

namespace App\Controller\API;

use App\Entity\InscriptionPending;
use App\Enum\EmailSubject;
use App\Repository\InscriptionPendingRepository;
use App\Repository\UtilisateurRepository;
use App\Service\EmailService;
use App\Service\JwtTokenManager;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    private EntityManager $em;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, EntityManagerInterface $em)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
        $this->em = $em;
    }

    #[Route("/signup", name: "signin", methods: ["POST"])]
    public function signup(MailerInterface $mailer, Request $request, InscriptionPendingRepository $repository): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);

        //setplain password to inscription pending and getmdp sy ny forongony
        $user = new InscriptionPending();
        $user->setPrenom($jsonData['prenom']);
        $user->setNom($jsonData['nom']);
        $user->setMail($jsonData['mail']);
        $user->setGenre($jsonData['genre']);
        $user->setDateNaissance(new \DateTimeImmutable($jsonData['dateNaissance']));
        $user->setMdpSimple($jsonData['motDePasse']);

        $verif = $jsonData['verification'];
        if ($verif !== $user->getMdpSimple()) {
            $resp = ResponseService::getJSONTemplate("error", [
                "message" => "Veuillez bien verifier votre mot de passe",
            ]);
            return $this->json($resp, 500);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getMdpSimple());
        $user->setMotDePasse($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();

        $insertedUser = $repository->findOneBy(["mail" => $user->getMail()]);

        $resp = ResponseService::getJSONTemplate("success", ["message" => "Veuillez confirmer votre inscription dans votre boîte mail"]);

        $mailer->send($this->email->createMail("miarantsoasuper3000@gmail.com", EmailSubject::INSCRIPTION->value, $insertedUser->getId()));

        return $this->json($resp);
    }

    #[Route("/signin", methods: ["POST"])]
    public function login(): JsonResponse {

//        if ($passwordHasher->isPasswordValid($user, $plainPassword)) {
//
//        }
        return $this->json("haha", 200, [], []);
    }

}