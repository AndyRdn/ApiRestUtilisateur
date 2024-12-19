<?php

namespace App\Controller\API;

use App\Entity\DoubleAuthentification;
use App\Entity\InscriptionPending;
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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

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

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JwtTokenManager $tokenManager, EmailService $email, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository, PasswordHasherFactoryInterface $hasherFactory, LoginTentativeRepository $tentativeRepository, ConfigService $configService, DoubleAuthentificationRepository $doubleAuthRepository)
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
    public function login(MailerInterface $mailer, Request $request): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);
        $email=$jsonData['email'];
        $mdp=$jsonData['mdp'];
//        dd($email);

        $utilisateur=$this->utilisateurRepository->findByLogin($email);
//        $hashMdp=$this->hasherFactory->hash('sha256', $mdp);

        $tentative=$this->tentativeRepository->getLastByIdUtilisateur($utilisateur->getId());

        if (strcasecmp($utilisateur->getMotDePasse(),$mdp)===0 && $tentative->getTentative()>0) {
            $mailer->send($this->email->createMail("andyrdn4@gmail.com", EmailSubject::AUTHENTIFICATION->value, $utilisateur->getId()));
            return $this->json("Succes", 200, [], []);
        }else{
            if ($tentative->getTentative()==0){
                //cree une email Pour le reset de la tentative
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
    public function checkPin(Request $request, int $id): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);
        $code=$jsonData['code'];
        $refDelais=$this->configService->getDelaisRef();
        $doubleAuth=$this->doubleAuthRepository->findValidCodeByUtilisateur($id,$refDelais);
        $refTentative=$this->configService->getTentativeRef();
        $tentative=$this->tentativeRepository->getLastByIdUtilisateur($id);
//        dd($doubleAuth->getCode());
        if ($doubleAuth != null && $doubleAuth->getCode()==$code && $tentative->getTentative()>0){
            $tentative->setTentative($refTentative);
            $this->tentativeRepository->update($tentative);
            return $this->json("Token Behhh", 200, [], []);
        }else{
            if ($tentative->getTentative()==0){
                //cree une email Pour le reset de la tentative
                return $this->json("Une email de reinitialisation a ete envoyer", 200, [], []);
            }else{
                $tentative->setTentative($tentative->getTentative()-1);
                $this->tentativeRepository->update($tentative);
                return $this->json("Faild", 200, [], []);
            }
        }
    }

}