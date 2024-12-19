<?php

namespace App\Repository;

use App\Entity\DoubleAuthentification;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DoubleAuthentification>
 */
class DoubleAuthentificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoubleAuthentification::class);
    }

    public function save(DoubleAuthentification $doubleAuthentification, bool $flush = true): void
    {
        // Préparer l'entité pour la sauvegarde
        $this->getEntityManager()->persist($doubleAuthentification);

        // Si demandé, appliquer immédiatement les changements
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DoubleAuthentification[] Returns an array of DoubleAuthentification objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DoubleAuthentification
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findValidCodeByUtilisateur(int $idUtilisateur, int $validDuration): ?DoubleAuthentification
    {
        $now = new \DateTimeImmutable();
        $validSince = $now->modify("-{$validDuration} seconds");
//        dd($validSince);
        return $this->createQueryBuilder('da')
            ->innerJoin('da.utilisateur', 'u') // Jointure sur l'entité Utilisateur
            ->andWhere('u.id = :idUtilisateur')
            ->andWhere('da.daty >= :validSince')
            ->setParameter('idUtilisateur', $idUtilisateur)
            ->setParameter('validSince', $validSince)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
