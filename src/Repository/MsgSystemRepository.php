<?php

namespace App\Repository;

use App\Entity\MsgSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method MsgSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MsgSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MsgSystem[]    findAll()
 * @method MsgSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MsgSystemRepository extends ServiceEntityRepository
{

    const NO_STATUS = 'NO';
    const YES_STATUS = 'YES';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MsgSystem::class);
    }

    public function unreadCount($userId)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ms.id)')
            ->from('App:MsgSystem', 'ms')
            ->where('ms.user_id = :user_id')
            ->andWhere('ms.read_status = :no_status')
            ->andWhere('ms.deletedAt is null')
            ->setParameters(['user_id' => $userId, 'no_status' => self::NO_STATUS]);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function addMsg($userId, $content)
    {
        $msg = (new MsgSystem())
            ->setUserId($userId)
            ->setContent($content)
            ->setReadStatus(self::NO_STATUS);

        $this->_em->persist($msg);
        $this->_em->flush();

        return $msg;
    }

    public function markAsRead($userId)
    {
        $this->_em->createQueryBuilder()
            ->update('App:MsgSystem', 'ms')
            ->set('ms.read_status', ':yes_status')
            ->set('ms.updatedAt', ':date')
            ->where('ms.user_id = :user_id AND ms.read_status = :no_status AND ms.deletedAt is null')
            ->setParameters([
                'user_id' => $userId,
                'no_status' => self::NO_STATUS,
                'yes_status' => self::YES_STATUS,
                'date' => new \DateTime()
            ])
            ->getQuery()
            ->execute();
        $this->_em->flush();

        return;
    }

    public function list($userId, $page = 1, $pagesize = 20)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('ms.id, ms.content, ms.read_status, ms.createdAt as created_at')
            ->from('App:MsgSystem', 'ms')
            ->where('ms.user_id = :user_id')
            ->andWhere('ms.deletedAt is null')
            ->orderBy('ms.id', 'DESC')
            ->setParameters(['user_id' => $userId])
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize);

        $p = new Paginator($query);
        $p->setUseOutputWalkers(false);

        $list = [];
        if (0 === count($p)) {
            return $list;
        }

        foreach ($p as &$v) {
            $v['read_status'] = strtolower($v['read_status']);
            $v['created_at'] = $v['created_at']->format(\DateTime::W3C);
            $list[] = $v;
        }

        return $list;
    }

    // /**
    //  * @return MsgSystem[] Returns an array of MsgSystem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MsgSystem
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
