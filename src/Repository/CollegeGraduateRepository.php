<?php

namespace App\Repository;

use App\Entity\CollegeGraduate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeGraduate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeGraduate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeGraduate[]    findAll()
 * @method CollegeGraduate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeGraduateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeGraduate::class);
    }

    // /**
    //  * @return CollegeUndergraduate[] Returns an array of CollegeUndergraduate objects
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
    public function findOneBySomeField($value): ?CollegeUndergraduate
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
