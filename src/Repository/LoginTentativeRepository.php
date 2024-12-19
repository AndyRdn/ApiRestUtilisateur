<?php

namespace App\Repository;

use App\Entity\LoginTentative;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginTentative>
 */
class LoginTentativeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginTentative::class);
    }

//    /**
//     * @return LoginTentative[] Returns an array of LoginTentative objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LoginTentative
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function update(LoginTentative $loginTentative, bool $flush = true): void
    {
        $this->getEntityManager()->persist($loginTentative);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findById(int $id): ?LoginTentative
    {
        return $this->find($id);
    }
    public function getLastByIdUtilisateur(int $idUtilisateur): ?LoginTentative
    {

        return $this->createQueryBuilder('lt')
            ->join('lt.utilisateur', 'u')
            ->andWhere('u.id = :idUtilisateur')
            ->setParameter('idUtilisateur', $idUtilisateur)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
