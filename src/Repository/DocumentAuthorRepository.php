<?php

namespace App\Repository;

use App\Entity\DocumentAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DocumentAuthor|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentAuthor|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentAuthor[]    findAll()
 * @method DocumentAuthor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentAuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DocumentAuthor::class);
    }

    // /**
    //  * @return DocumentAuthor[] Returns an array of DocumentAuthor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentAuthor
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
