<?php

namespace App\Repository;

use App\Entity\CollegeCrime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeCrime|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeCrime|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeCrime[]    findAll()
 * @method CollegeCrime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeCrimeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeCrime::class);
    }

    // /**
    //  * @return CollegeCrime[] Returns an array of CollegeCrime objects
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
    public function findOneBySomeField($value): ?CollegeCrime
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
