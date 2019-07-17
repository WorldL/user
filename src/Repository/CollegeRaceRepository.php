<?php

namespace App\Repository;

use App\Entity\CollegeRace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeRace|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeRace|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeRace[]    findAll()
 * @method CollegeRace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeRaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeRace::class);
    }

    // /**
    //  * @return CollegeRace[] Returns an array of CollegeRace objects
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
    public function findOneBySomeField($value): ?CollegeRace
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
