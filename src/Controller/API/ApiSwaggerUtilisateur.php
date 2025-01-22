<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api")]
class ApiSwaggerUtilisateur extends AbstractController
{

    #[Route("/doc", name: "apiDoc", methods: ["GET"])]
    public function index() {
        return $this->render('swagger_ui.html.twig');
    }

}