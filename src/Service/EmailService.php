<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{

    private $twig;
    private $subject = ["Validation de votre profil", "Authentification de votre profil", "Reinitialisation du nombre d'essaie de connexion"];
    private $path = ["validation.html.twig", "confirmation.html.twig", "reset.html.twig"];

    /**
     * @param $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }


    public function createMail(string $recept, string $subject) {
        $pathToTemplate = "";
        $misy = -1;
        for($i = 0; $i < count($this->subject); $i++) {
            if ($this->subject[$i]==$subject) {
                $pathToTemplate .= $this->path[$i];
                $misy = $i;
            }
        }
        $html = $this->twig->render($pathToTemplate, [
            "codePin" => $this->generatePIN()
        ]);

        $email = (new Email())
            ->from('andyrdn04@gmail.com')
            ->to($recept)
            ->subject($subject)
            ->html($html);

        return $email;
    }

    public function generatePIN(): int {
        return random_int(100000, 999999);
    }
}