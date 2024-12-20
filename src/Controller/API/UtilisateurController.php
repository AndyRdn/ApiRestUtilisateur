<?php

namespace App\Controller\API;

use App\Entity\InscriptionPending;
use App\Entity\LoginTentative;
use App\Entity\Utilisateur;
use App\Enum\EmailSubject;
use App\Repository\InscriptionPendingRepository;
use App\Repository\LoginTentativeRepository;
use App\Repository\UtilisateurRepository;
use App\Service\EmailService;
use App\Service\JwtTokenManager;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/utilisateur")]
class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private JwtTokenManager $tokenManager;
    private EmailService $email;
    private EntityManager $em;
    private $mdpHash ;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, EntityManagerInterface $em, PasswordHasherFactoryInterface $mdpHash)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
        $this->em = $em;
        $this->mdpHash = $mdpHash;
    }

    #[Route("/signup", name: "signin", methods: ["POST"])]
    public function signup(MailerInterface $mailer, Request $request, InscriptionPendingRepository $repository): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);

        $user = new InscriptionPending();
        $user->setPrenom($jsonData['prenom']);
        $user->setNom($jsonData['nom']);
        $user->setMail($jsonData['mail']);
        $user->setGenre($jsonData['genre']);
        $user->setDateNaissance(new \DateTimeImmutable($jsonData['dateNaissance']));
        $user->setMdpSimple($jsonData['motDePasse']);
        $verifUser = $repository->findOneBy(["mail" => $user->getMail()]);
        if ($verifUser !== null) {
            $resp = ResponseService::getJSONTemplate("error", [
                "message" => "Email déjà inscrit",
            ]);
            return $this->json($resp, 500);
        }

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

    #[Route("/signup/verification/{id}", methods: ["GET"])]
    public function signupValidation(int $id, InscriptionPendingRepository $repository, UtilisateurRepository $utilisateurRepository, LoginTentativeRepository $tentativeRepository): JsonResponse {
        $user = $repository->find($id);
        $verif = $utilisateurRepository->findOneBy(["mail" => $user->getMail()]);
        if ($verif !== null) {
            $resp = ResponseService::getJSONTemplate("error", ["message" => "Utilisateur déjà inscrit, votre lien n'est plus valide"]);
            return $this->json($resp, 500);
        }

        if ($user === null) {
            $resp = ResponseService::getJSONTemplate("error", ["message" => "Inscription inexistant, veuillez bien verifier votre lien"]);
            return $this->json($resp, 500);
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setNom($user->getNom());
        $utilisateur->setPrenom($user->getPrenom());
        $utilisateur->setDateNaissance($user->getDateNaissance());
        $utilisateur->setGenre($user->getGenre());
        $utilisateur->setMail($user->getMail());
        $utilisateur->setMotDePasse($user->getMotDePasse());
        $this->em->persist($utilisateur);
        $this->em->flush();

        $lastUser = $utilisateurRepository->findOneBy(["mail" => $user->getMail()]);
        $tentative = new LoginTentative();
        $tentative->setUtilisateur($lastUser);
        $tentative->setTentative(3);
        $this->em->persist($tentative);
        $this->em->flush();

        $resp = ResponseService::getJSONTemplate("success", [
            "message" => "Inscription validé, bienvenue parmis nous",
            "data" => $lastUser,
        ]);

//        dd($resp);
        return $this->json($resp, 200, [], [
            'groups' => ['utilisateur.info']
        ]);
    }

    #[Route("/signin", methods: ["POST"])]
    public function login(InscriptionPendingRepository $repository) {

//        if ($passwordHasher->isPasswordValid($user, $plainPassword)) {
//
//        }
        $insertedUser = $repository->findOneBy(["mail" => "miarantsoasuper3000@gmail.com"]);
        $verif = $this->mdpHash->getPasswordHasher('plaintext')->hash("haha");

//        dd($this->mdpHash->getPasswordHasher('plaintext')->verify($verif, $insertedUser->getMdpSimple()));
        return $this->render("validation.html.twig", ["uid" => "f"]);
//        return $this->json($this->mdpHash->getPasswordHasher('plaintext')->hash("haha"), 200, [], []);
    }

}