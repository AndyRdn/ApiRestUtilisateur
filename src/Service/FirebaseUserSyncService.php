<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\FirestoreException;

class FirebaseUserSyncService
{
    private Firestore $firestore;
    private EntityManagerInterface $em;
    private bool $isSyncing = false;

    public function __construct(Firestore $firestore, EntityManagerInterface $em)
    {
        $this->firestore = $firestore;
        $this->em = $em;
    }

    public function syncUsers(): void
    {
        $database = $this->firestore->database();
        $collection = $database->collection('utilisateurs');

        // Listen for changes
        $collection->onSnapshot(function ($snapshot) {
            $this->handleFirestoreChanges($snapshot);
        });
    }

    private function handleFirestoreChanges($snapshot): void
    {
        if ($this->isSyncing) {
            return;
        }

        $this->isSyncing = true;

        try {
            foreach ($snapshot->documents() as $document) {
                $this->handleDocument($document);
            }
        } finally {
            $this->isSyncing = false;
        }
    }

    private function handleDocument($document): void
    {
        $data = $document->data();
        $firebaseId = $document->id();

        $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['firebaseId' => $firebaseId]);

        if (!$user) {
            $user = new Utilisateur();
            $user->setFirebaseId($firebaseId);
        }

        // Map Firestore fields to entity
        $user->setPrenom($data['prenom']);
        $user->setNom($data['nom']);
        $user->setDateNaissance(new \DateTimeImmutable($data['dateNaissance']));
        $user->setGenre($data['genre']);
        $user->setMail($data['mail']);

        $this->em->persist($user);
        $this->em->flush();
    }
}