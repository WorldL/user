<?php

namespace App\Repository;

use App\Entity\FamousAlumni;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FamousAlumni|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamousAlumni|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamousAlumni[]    findAll()
 * @method FamousAlumni[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamousAlumniRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FamousAlumni::class);
    }

    // /**
    //  * @return FamousAlumni[] Returns an array of FamousAlumni objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FamousAlumni
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
