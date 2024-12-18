<?php

namespace App\Enum;

enum EmailSubject: string
{
    case INSCRIPTION = "Validation de votre profil";
    case AUTHENTIFICATION = "Authentification de votre profil";
    case RESET = "Reinitialisation du nombre d'essaie de connexion";
}
