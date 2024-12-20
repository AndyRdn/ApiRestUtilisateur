<?php

namespace App\Service;

use App\Repository\ConfigRepository;

class ConfigService
{
    private $configRepository;

    /**
     * @param $configRepository
     */
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function getTentativeRef()
    {
        return $this->configRepository->findByNom("tentative")->getValeur();
    }

    public function getDelaisRef()
    {
        return $this->configRepository->findByNom("delais")->getValeur();
    }

    public function getTokenRef()
    {
        return $this->configRepository->findByNom("token")->getValeur();
    }

}