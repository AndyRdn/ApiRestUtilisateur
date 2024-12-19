<?php
namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api")]
class ApiSwaggerController extends AbstractController
{
    #[Route("/doc", name: "apiDoc", methods: ["GET"])]
    public function index()
    {
        return $this->render('swagger_ui.html.twig');
    }
}

// Genere api-doc.yaml : php bin/console nelmio:apidoc:dump --format=yaml > public/api-doc.yaml