<?php

namespace App\Service;

use App\Entity\DoubleAuthentification;
use App\Repository\DoubleAuthentificationRepository;
use App\Repository\UtilisateurRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use function Symfony\Component\Clock\now;

class EmailService
{
    private DoubleAuthentificationRepository $authentificationRepository;

    private $utilisateurRepository;
    private $twig;
    private $subject = ["Validation de votre profil", "Authentification de votre profil", "Reinitialisation du nombre d'essaie de connexion"];
    private $path = ["validation.html.twig", "confirmation.html.twig", "reset.html.twig"];

    /**
     * @param $twig
     */
    public function __construct(Environment $twig, DoubleAuthentificationRepository $authentificationRepository, UtilisateurRepository $utilisateurRepository)
    {
        $this->twig = $twig;
        $this->authentificationRepository=$authentificationRepository;
        $this->utilisateurRepository=$utilisateurRepository;
    }


    public function createMail(string $recept, string $subject, int $uid) {
        $pathToTemplate = "";
        $misy = -1;
        for($i = 0; $i < count($this->subject); $i++) {
            if ($this->subject[$i]==$subject) {
                $pathToTemplate .= $this->path[$i];
                $misy = $i;
            }
        }
        $html = $this->twig->render($pathToTemplate, [

            "codePin" => $this->generatePIN($uid),
            "uid" => $uid
        ]);

        $email = (new Email())
            ->from('andyrdn04@gmail.com')
            ->to($recept)
            ->subject($subject)
            ->html($html);

        return $email;
    }

    public function generatePIN($id): int {
        $pin= random_int(100000, 999999);

        $authe= new DoubleAuthentification();
        $authe->setUtilisateur($this->utilisateurRepository->findById($id));
        $authe->setDaty(new \DateTimeImmutable());
        $authe->setCode($pin);
        $this->authentificationRepository->save($authe);

        return $pin;
    }
}