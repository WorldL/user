<?php

namespace App\Repository;

use App\Entity\CollegeScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeScore[]    findAll()
 * @method CollegeScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeScoreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeScore::class);
    }

    // /**
    //  * @return CollegeScore[] Returns an array of CollegeScore objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CollegeScore
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
