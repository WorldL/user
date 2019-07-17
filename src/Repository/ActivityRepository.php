<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\ActivityViewLog;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    /**
     * @var \App\Repository\ActivityViewLogRepository $activityViewLogRepo;
     */
    private $activityViewLogRepo;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Activity::class);
        $this->activityViewLogRepo = $this->_em->getRepository(ActivityViewLog::class);
    }


    public function list($page = 1, $pagesize = 10)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('a.id, a.title, a.intro, a.cover, a.url, a.begin_time, a.end_time')
            ->from('App:Activity', 'a')
            ->where('a.deletedAt is null')
            ->orderBy('a.id', 'desc')
            ->setFirstResult(($page -1) * $pagesize)
            ->setMaxResults($pagesize);
        
        $p = new Paginator($query);
        $p->setUseOutputWalkers(false);

        $list = [];
        foreach ($p as $activity) {
            $activity['begin_time'] = $activity['begin_time']->format(\Datetime::W3C);
            $activity['end_time'] = $activity['end_time']->format(\Datetime::W3C);
            $list[] = $activity;
        }

        return $list;
    }

    public function readActivity($userId)
    {
        $viewLog = $this->activityViewLogRepo->findOneBy(['user_id' => $userId]);
        if (empty($viewLog)) {
            $viewLog = (new ActivityViewLog())
                ->setUserId($userId)
                ->setVisitTime(new \DateTime());
            $this->_em->persist($viewLog);
        } else {
            $viewLog->setVisitTime(new \DateTime());
            $this->_em->merge($viewLog);
        }
        $this->_em->flush();

        return;
    }

    public function countUnreadByUser($userId)
    {
        $viewLog = $this->activityViewLogRepo->findOneBy(['user_id' => $userId]);
        $time = empty($viewLog) ? new \DateTime('2019-01-01') : $viewLog->getVisitTime();
        $count = $this->countUnreadByTime($time);

        return $count;
    }

    public function countUnreadByTime(\DateTime $time)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(a.id)')
            ->from('App:Activity', 'a')
            ->where('a.begin_time > :time')
            ->andWhere('a.begin_time < :now_time')
            ->andWhere('a.end_time > :now_time')
            ->andWhere('a.deletedAt is null')
            ->setParameters(['time' => $time, 'now_time' => new \Datetime()]);
        
        return $query->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return Activity[] Returns an array of Activity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
