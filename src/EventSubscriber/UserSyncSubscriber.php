<?php

namespace App\EventSubscriber;

use App\Entity\Utilisateur;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Kreait\Firebase\Database;

class UserSyncSubscriber implements EventSubscriber
{
    private $database;
    private $isSyncing = false;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
            'postRemove',
        ];
    }

    public function postPersist(PostPersistEventArgs $args)
    {
        $this->handleChange($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $this->handleChange($args->getObject());
    }

    public function postRemove(PostRemoveEventArgs $args)
    {
        $this->handleDelete($args->getObject());
    }

    private function handleChange($entity)
    {
        if ($entity instanceof Utilisateur && !$this->isSyncing) {
            $this->isSyncing = true;

            $data = $entity->jsonSerialize();
            $path = 'utilisateurs/'.$entity->getFirebaseId();

            try {
                // Never sync passwords
                unset($data['mdpSimple']);
                unset($data['motDePasse']);

                $this->database->getReference($path)->set($data);
            } finally {
                $this->isSyncing = false;
            }
        }
    }

    private function handleDelete($entity)
    {
        if ($entity instanceof Utilisateur && !$this->isSyncing) {
            $this->isSyncing = true;
            $path = 'utilisateurs/'.$entity->getFirebaseId();
            $this->database->getReference($path)->remove();
            $this->isSyncing = false;
        }
    }
}