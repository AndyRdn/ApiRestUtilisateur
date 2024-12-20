<?php

namespace App\Controller\API;

use App\Entity\DoubleAuthentification;
use App\Entity\InscriptionPending;
use App\Entity\HistoriqueUtilisateur;
use App\Entity\LoginTentative;
use App\Entity\Utilisateur;
use App\Enum\EmailSubject;

use App\Repository\ConfigRepository;
use App\Repository\DoubleAuthentificationRepository;
use App\Repository\InscriptionPendingRepository;
use App\Repository\LoginTentativeRepository;
use App\Repository\UtilisateurRepository;
use App\Service\ConfigService;


use App\Service\EmailService;
use App\Service\JwtTokenManager;
use App\Service\ResponseService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;



#[Route("/utilisateur")]
class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private JwtTokenManager $tokenManager;
    private EmailService $email;
    private EntityManager $em;
    private $hasherFactory ;
    private  $utilisateurRepository;
    private $tentativeRepository;
    private $configService;
    private $doubleAuthRepository;
    private UtilisateurService $userService;
    private SerializerInterface $serializer;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository, PasswordHasherFactoryInterface $hasherFactory, LoginTentativeRepository $tentativeRepository, ConfigService $configService, DoubleAuthentificationRepository $doubleAuthRepository, UtilisateurService $userService, SerializerInterface $serializer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->email = $email;
        $this->em = $em;
        $this->utilisateurRepository=$utilisateurRepository;
        $this->hasherFactory=$hasherFactory;
        $this->tentativeRepository=$tentativeRepository;
        $this->configService=$configService;
        $this->doubleAuthRepository=$doubleAuthRepository;
        $this->userService = $userService;
        $this->serializer = $serializer;
        $this->hasherFactory = $hasherFactory->getPasswordHasher("plaintext");

    }

    #[Route("/signup", name: "signin", methods: ["POST"])]
    public function signup(MailerInterface $mailer, Request $request, InscriptionPendingRepository $repository): JsonResponse
    {
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
        $this->entityManager->persist($user);
        $this->entityManager->flush();

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
    public function login(MailerInterface $mailer, Request $request): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);
        $email=$jsonData['email'];
        $mdp=$jsonData['mdp'];
//        dd($email);

        $utilisateur=$this->utilisateurRepository->findByLogin($email);
//        $hashMdp=$this->hasherFactory->hash('sha256', $mdp);

        $tentative=$this->tentativeRepository->getLastByIdUtilisateur($utilisateur->getId());
//      check Email sy MDP sy Tentative
        if (strcasecmp($utilisateur->getMotDePasse(),$mdp)===0 && $tentative->getTentative()>0) {
            $mailer->send($this->email->createMail("andyrdn4@gmail.com", EmailSubject::AUTHENTIFICATION->value, $utilisateur->getId()));
            return $this->json("Succes", 200, [], []);
        }else{
//          check si
            if ($tentative->getTentative()==0){
                //cree une email Pour le reset de la tentative
                $mailer->send($this->email->createMail("andyrdn4@gmail.com", EmailSubject::RESET->value, $utilisateur->getId()));
                return $this->json("Une email de reinitialisation a ete envoyer", 200, [], []);
            }else{
                $tentative->setTentative($tentative->getTentative()-1);
                $this->tentativeRepository->update($tentative);
                return $this->json("Faild", 200, [], []);
            }

        }
//            return $this->json("Faild", 200, [], []);

    }

    #[Route("/confirmation/{id}", methods: ["POST"])]
    public function checkPin(Request $request, int $id, MailerInterface $mailer): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $code = $jsonData['code'];
        $refDelais = $this->configService->getDelaisRef();
        $doubleAuth = $this->doubleAuthRepository->findValidCodeByUtilisateur($id, $refDelais);
        $refTentative = $this->configService->getTentativeRef();
        $tentative = $this->tentativeRepository->getLastByIdUtilisateur($id);
//        dd($doubleAuth->getCode());
        if ($doubleAuth != null && $doubleAuth->getCode() == $code && $tentative->getTentative() > 0) {
            $tentative->setTentative($refTentative);
            $this->tentativeRepository->update($tentative);
            return $this->json("Token Behhh", 200, [], []);
        } else {
            if ($tentative->getTentative() == 0) {
                //cree une email Pour le reset de la tentative
                $mailer->send($this->email->createMail("andyrdn4@gmail.com", EmailSubject::RESET->value, $id));
                return $this->json("Une email de reinitialisation a ete envoyer", 200, [], []);
            } else {
                $tentative->setTentative($tentative->getTentative() - 1);
                $this->tentativeRepository->update($tentative);
                return $this->json("Faild", 200, [], []);
            }
        }
    }

    #[Route("/update/{id}", methods: ["POST"])]
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
        $message = "Aucun changement effectué";
        if (!empty($updatedFields)) {
            // updating the user row in table "utilisateur"
            $this->entityManager->persist($user);
            
            // inserting a new user row for the update (at today's dateTime) in table "historique_utilisateur"
            $histoUser->makeFromUser($user, new \DateTimeImmutable());
            $this->entityManager->persist($histoUser);
            
            $this->entityManager->flush();

            $message = "Informations de l'utilisateur modifiées avec succès.";
        }
        
        $resp = ResponseService::getJSONTemplate("success", ["message" => $message]);

        return $this->json($resp);

    }

    #[Route("/resetTentative/{id}", methods: ["GET"])]
    public function resetTentative(int $id)
    {
        $logTentative=$this->tentativeRepository->getLastByIdUtilisateur($id);
        $logTentative->setTentative($this->configService->getTentativeRef());
        $this->tentativeRepository->update($logTentative);
        return $this->json("Tentative Reinitialiser avec succes", 200, [], []);

    }

}