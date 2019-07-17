<?php

namespace App\Repository;

use App\Entity\MsgNotify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Service\InfoClient;
use Doctrine\DBAL\Types\Type;

/**
 * @method MsgNotify|null find($id, $lockMode = null, $lockVersion = null)
 * @method MsgNotify|null findOneBy(array $criteria, array $orderBy = null)
 * @method MsgNotify[]    findAll()
 * @method MsgNotify[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MsgNotifyRepository extends ServiceEntityRepository
{
    const REVIEW_DETAIL_URL = '/review/detail';
    const INFO_DETAIL_URL = '/info/detail';

    const NO_STATUS = 'NO';
    const YES_STATUS = 'YES';

    private $infoClient;

    public function __construct(RegistryInterface $registry, InfoClient $infoClient)
    {
        $this->infoClient = $infoClient;
        parent::__construct($registry, MsgNotify::class);
    }

    public function unreadCount($userId)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(mn.id)')
            ->from('App:MsgNotify', 'mn')
            ->where('mn.user_id = :user_id')
            ->andWhere('mn.read_status = :no_status')
            ->andWhere('mn.deletedAt is null')
            ->setParameters(['user_id' => $userId, 'no_status' => self::NO_STATUS]);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function addMsg($userId, $notifierId, $infoId, $reviewId)
    {
        $msg = (new MsgNotify())
            ->setUserId($userId)
            ->setNotifierId($notifierId)
            ->setInfoId($infoId)
            ->setReviewId($reviewId)
            ->setReadStatus(self::NO_STATUS);

        $this->_em->persist($msg);
        $this->_em->flush();

        return $msg;
    }

    public function markAsRead($userId)
    {
        $this->_em->createQueryBuilder()
            ->update('App:MsgNotify', 'mn')
            ->set('mn.read_status', ':yes_status')
            ->set('mn.updatedAt', ':date')
            ->where('mn.user_id = :user_id AND mn.read_status = :no_status AND mn.deletedAt is null')
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
        $userRepo = $this->_em->getRepository(User::class);
        $query = $this->_em->createQueryBuilder();
        $query->select(
            'mn.id, mn.notifier_id, mn.info_id, mn.review_id',
            'mn.read_status, mn.createdAt as created_at'
        )
            ->from('App:MsgNotify', 'mn')
            ->where('mn.user_id = :user_id')
            ->andWhere('mn.deletedAt is null')
            ->orderBy('mn.id', 'DESC')
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
            $v['notifier'] = $userRepo->getInfo($userRepo->find($v['notifier_id']));
            unset($v['notifier']['sign']);
            $v['created_at'] = $v['created_at']->format(\DateTime::W3C);

            try {
                $info = $this->infoClient->call(self::INFO_DETAIL_URL, ['info_id' => $v['info_id']]);
                $review = $this->infoClient->call(self::REVIEW_DETAIL_URL, ['review_id' => $v['review_id']]);
                $v['info_cut'] = mb_substr($info['content'], 0, 50);
                $v['review_cut'] = mb_substr($review['content'], 0, 50);
                $v['reply_type'] = 0 < $review['pid'] ? 'reply_review' : 'reply_info';
                $list[] = $v;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $list;
    }

    public function deleteByInfo($infoId)
    {
        $query = $this->_em->createQueryBuilder();
        $query->update('App:MsgNotify', 'mn')
            ->set('mn.deletedAt', ':datetime')
            ->where('mn.info_id = :info_id AND mn.deletedAt is null')
            ->setParameter('info_id', $infoId)
            ->setParameter('datetime', new \Datetime(), Type::DATETIME);
        $query->getQuery()->execute();
    }

    public function deleteByReview($reviewId)
    {
        $query = $this->_em->createQueryBuilder();
        $query->update('App:MsgNotify', 'mn')
            ->set('mn.deletedAt', ':datetime')
            ->where('mn.review_id = :review_id AND mn.deletedAt is null')
            ->setParameter('review_id', $reviewId)
            ->setParameter('datetime', new \Datetime(), Type::DATETIME);
        $query->getQuery()->execute();
    }

    // /**
    //  * @return MsgNotify[] Returns an array of MsgNotify objects
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
    public function findOneBySomeField($value): ?MsgNotify
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
