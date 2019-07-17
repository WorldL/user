<?php

namespace App\Repository;

use App\Entity\CollegeArt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

/**
 * @method CollegeArt|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeArt|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeArt[]    findAll()
 * @method CollegeArt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeArtRepository extends ServiceEntityRepository
{
    private $em;
    public function __construct(
        RegistryInterface $registry,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, CollegeArt::class);
        $this->em = $entityManager;
    }

    // /**
    //  * @return CollegeArt[] Returns an array of CollegeArt objects
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
    public function findOneBySomeField($value): ?CollegeArt
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function list()
    {
        $all =  $this->em->createQueryBuilder()
            ->select('c')
            ->from("App:CollegeArt","c")
            ->getQuery();
        return $all->getResult(Query::HYDRATE_ARRAY);
    }
}
