<?php

namespace App\Controller\API;

use App\Repository\ConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route("/tentative/{value}", methods: ["GET"])]
    public function updateTentatite($value)
    {
        $config=$this->configRepository->findByNom("tentative");
        $config->setValeur($value);
        $this->configRepository->update($config);
        return $this->json("Tentative changer avec succes", 200, [], []);

    }

    #[Route("/delais/{value}", methods: ["GET"])]
    public function updatedelais($value)
    {
        $config=$this->configRepository->findByNom("delais");
        $config->setValeur($value);
        $this->configRepository->update($config);
        return $this->json("Delais changer avec succes", 200, [], []);

    }


}