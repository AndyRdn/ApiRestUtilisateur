<?php

namespace App\Controller\API;

use App\Entity\InscriptionPending;
use App\Entity\Utilisateur;
use App\Entity\HistoriqueUtilisateur;
use App\Enum\EmailSubject;
use App\Service\EmailService;
use App\Service\JwtTokenManager;
use App\Service\ResponseService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\InscriptionPendingRepository;

#[Route("/utilisateur")]
class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private JwtTokenManager $tokenManager;
    private EmailService $email;
    private UtilisateurService $userService;
    private SerializerInterface $serializer;
    private $hasherFactory;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, EntityManagerInterface $em, UtilisateurService $userService, SerializerInterface $serializer, PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
        $this->userService = $userService;
        $this->serializer = $serializer;
        $this->hasherFactory = $hasherFactory->getPasswordHasher("plaintext");
    }

    #[Route("/signup", name: "signin", methods: ["POST"])]
    public function signup(MailerInterface $mailer, Request $request, InscriptionPendingRepository $repository): JsonResponse
    {
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
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $insertedUser = $repository->findOneBy(["mail" => $user->getMail()]);

        $resp = ResponseService::getJSONTemplate("success", ["message" => "Veuillez confirmer votre inscription dans votre boîte mail"]);

        $mailer->send($this->email->createMail("miarantsoasuper3000@gmail.com", EmailSubject::INSCRIPTION->value, $insertedUser->getId()));

        return $this->json($resp);
    }

    #[Route("/{id}/update", methods: ["POST"])]
    public function updateUser(
        Request $request,
        int $id
    ): JsonResponse {

        $user = $this->entityManager->getRepository(Utilisateur::class)->find($id);
        $oldUser = $user->copy();
        try {
            $user = $this->serializer->deserialize(
                $request->getContent(),
                Utilisateur::class,
                'json',
                ['object_to_populate' => $user, 'groups' => ['update']]
            );
        } catch (\Exception $e) {
            return $this->json(['error' => 'Input invalide'], 400);
        }

        $updatedFields = $this->userService->getUpdatedFields($oldUser, $user);

        $histoUser = new HistoriqueUtilisateur();
        if (!empty($updatedFields)) {
            // updating the user row in table "utilisateur"
            $this->entityManager->persist($user);

            // inserting a new user row for the update (at today's dateTime) in table "historique_utilisateur"
            $histoUser->makeFromUser($user, new \DateTimeImmutable());
            $this->entityManager->persist($histoUser);

            $this->entityManager->flush();
        }

        return $this->json(["updatedFields" => $updatedFields, "user" => $user], 200, [], []);
    }

}