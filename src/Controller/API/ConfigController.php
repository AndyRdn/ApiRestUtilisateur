<?php

namespace App\Controller\API;

use App\Repository\ConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/config")]
class ConfigController extends AbstractController
{
    private $configRepository;

    /**
     * @param $configRepository
     */
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    #[Route("/tentative", methods: ["POST"])]
    public function updateTentatite(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $value = $data["value"];

        $config=$this->configRepository->findByNom("tentative");
        $config->setValeur($value);
        $this->configRepository->update($config);
        return $this->json("Tentative changée avec succes", 200, [], []);

    }

    #[Route("/delais", methods: ["POST"])]
    public function updatedelais(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $value = $data["value"];

        $config=$this->configRepository->findByNom("delais");
        $config->setValeur($value);
        $this->configRepository->update($config);
        return $this->json("Delais changée avec succes", 200, [], []);

    }


}