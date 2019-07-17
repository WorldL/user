<?php

namespace App\Repository;

use App\Entity\SmsLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SmsLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsLog[]    findAll()
 * @method SmsLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SmsLog::class);
    }

    public function create($name, $phone, $parameters, $status = 'SENDED')
    {
        $smsLog = new SmsLog();
        $smsLog->setName($name)
               ->setPhone($phone)
               ->setParameters($parameters)
               ->setStatus($status);
        $this->getEntityManager()->persist($smsLog);
        $this->getEntityManager()->flush();

        return $smsLog;
    }

    // /**
    //  * @return SmsLog[] Returns an array of SmsLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SmsLog
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
