<?php

namespace App\Controller\API;

use App\Entity\HistoriqueUtilisateur;
use App\Entity\Utilisateur;
use App\Enum\EmailSubject;
use App\Service\EmailService;
use App\Service\JwtTokenManager;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/utilisateur")]
class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private JwtTokenManager $tokenManager;
    private EmailService $email;
    private UtilisateurService $userService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, UtilisateurService $userService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
        $this->userService = $userService;
    }

    #[Route("/signup", name: "signin", methods: ["GET"])]
    public function signup(MailerInterface $mailer)
    {

        //        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        //        $user->setMotDePasse($hashedPassword);

        $mailer->send($this->email->createMail("irina.elina.r@gmail.com", EmailSubject::AUTHENTIFICATION->value));
        return $this->json("Email sent", 200, [], []);
    }

    #[Route("/signin", methods: ["POST"])]
    public function login(): JsonResponse
    {

        //        if ($passwordHasher->isPasswordValid($user, $plainPassword)) {
        //
        //        }
        return $this->json("haha", 200, [], []);
    }

    #[Route("/update", methods: ["POST"])]
    public function updateUser(
        Request $request,
        SerializerInterface $serializer,
    ): JsonResponse {

        $user = new Utilisateur();
        $user->setId(1);
        $user->setPrenom("Irina");
        $user->setNom("Ranaivozanany");
        $user->setDateNaissance(new \DateTimeImmutable("2005-03-07 18:05:30"));
        $user->setGenre(0);
        $user->setMail("irina.elina.r@gmail.com");
        $user->setMdpSimple("ieol7829");
        $this->userService->hashPassword($user); // setting the motDePasse field to the hashed version of mdpSimple

        $updatedUser = $user->copy();
        try {
            $updatedUser = $serializer->deserialize(
                $request->getContent(),
                Utilisateur::class,
                'json',
                ['object_to_populate' => $updatedUser, 'groups' => ['update']]
            );
        } catch (\Exception $e) {
            return $this->json(['error' => 'Input invalide'], 400);
        }

        $updatedFields = $this->userService->getUpdatedFields($user, $updatedUser);

        $histoUser = new HistoriqueUtilisateur();
        $histoUser->makeFromUser($updatedUser, new \DateTimeImmutable());
        if (!empty($updatedFields)) {
            // inserting a new user row for the update (at today's dateTime) in table "historique_utilisateur"
            // $this->entityManager->persist($histoUser);

            // updating the user row in table "utilisateur"
            // $this->entityManager->persist($updatedUser);
            // $this->entityManager->flush();
        }

        return $this->json(["updatedField" => $updatedFields, "newUser" => $updatedUser, "histoUser" => $histoUser], 200, [], []);
    }
}
