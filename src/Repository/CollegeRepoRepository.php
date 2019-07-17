<?php

namespace App\Repository;

use App\Entity\CollegeRepo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeRepo[]    findAll()
 * @method CollegeRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeRepoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeRepo::class);
    }

    // /**
    //  * @return CollegeRepo[] Returns an array of CollegeRepo objects
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
    public function findOneBySomeField($value): ?CollegeRepo
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
