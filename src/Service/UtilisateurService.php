<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Annotations\AnnotationReader;

class UtilisateurService
{
    private $hasherFactory;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory->getPasswordHasher("plaintext");
    }

    public function updateUserFields(Utilisateur $user, $data) {
        return [];
    }

    public function hashPassword(Utilisateur $user)
    {
        $hashedPassword = $this->hasherFactory->hash($user->getMdpSimple());
        $user->setMotDePasse($hashedPassword);
    }

    public function getUpdatedFields(Utilisateur $oldUser, Utilisateur $newUser): array
    {
        $updatedFields = [];

        if ($oldUser->getPrenom() !== $newUser->getPrenom()) {
            $updatedFields[] = 'prenom';
        }

        if ($oldUser->getNom() !== $newUser->getNom()) {
            $updatedFields[] = 'nom';
        }

        if ($oldUser->getDateNaissance() !== $newUser->getDateNaissance()) {
            $updatedFields[] = 'dateNaissance';
        }

        if ($oldUser->getGenre() !== $newUser->getGenre()) {
            $updatedFields[] = 'genre';
        }

        $this->hashPassword($newUser);
        if ($oldUser->getMotDePasse() !== $newUser->getMotDePasse()) {
            $updatedFields[] = 'mdp';
        }

        return $updatedFields;
    }
}
