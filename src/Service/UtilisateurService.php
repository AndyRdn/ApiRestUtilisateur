<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurService
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hashPassword(Utilisateur $user) {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getMdpSimple());
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
        if ($oldUser->getMdpSimple() !== $newUser->getMdpSimple()) {
            $updatedFields[] = 'mdp';
        }

        return $updatedFields;
    }
}