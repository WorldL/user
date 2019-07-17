<?php

namespace App\Repository;

use App\Entity\UserFollower;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method UserFollower|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFollower|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFollower[]    findAll()
 * @method UserFollower[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFollowerRepository extends ServiceEntityRepository
{
    /**
     * @var UserRepository $userRepo
     */
    private $userRepo;
    public function __construct(RegistryInterface $registry, UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        parent::__construct($registry, UserFollower::class);
    }

    public function relativeFollowStatus($userId, $relativedId)
    {
        $forMe = $this->findOneBy(['user_id' => $userId, 'follower_id' => $relativedId, 'deletedAt' => null]);
        $forHim = $this->findOneBy(['user_id' => $relativedId, 'follower_id' => $userId, 'deletedAt' => null]);

        return [
            'for_me' => empty($forMe) ? 'no' : 'yes',
            'for_him' => empty($forHim) ? 'no' : 'yes',
        ];
    }

    public function follow($userId, $followerId)
    {
        // 检查是否已关注过
        $uf = $this->findBy([
            'user_id' => $userId,
            'follower_id' => $followerId,
            'deletedAt' => null,
        ]);
        if (0 !== count($uf)) {
            return;
        }

        $uf = (new UserFollower())
            ->setUserId($userId)
            ->setFollowerId($followerId);

        $this->_em->persist($uf);
        $this->_em->flush();
    }

    public function unFollow($userId, $followerId)
    {
        $ufs = $this->findBy([
            'user_id' => $userId,
            'follower_id' => $followerId,
            'deletedAt' => null,
        ]);
        if (0 < count($ufs)) {
            foreach ($ufs as $uf) {
                $this->_em->remove($uf);
                $this->_em->flush();
            }
        }
        
        return;
    }

    public function followList($userId, $relativedId = 0, $page = 1, $pageSize = 30)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('uf.user_id, u.avatar, u.username')
            // ->addSelect($expr->exists('select * from uf where uf.user_id = :user_id AND uf.follower_id = uid'))
            ->from('App:UserFollower', 'uf')
            ->where('uf.follower_id = :user_id')
            ->andWhere('uf.deletedAt is null')
            ->leftJoin('App:User', 'u', Join::WITH, 'uf.user_id = u.id')
            ->orderBy('uf.id', 'desc')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->setParameter('user_id', $userId);
        // dd($query->getQuery());
        $p = new Paginator($query);
        $p->setUseOutputWalkers(false);

        $res = [];
        if (0 === count($p)) {
            return $res;
        }
        foreach ($p as $i) {
            $uModel = $this->userRepo->find($i['user_id']);
            if (empty($uModel)) {
                continue;
            }
            $u = $this->userRepo->getInfo($uModel);
            $u['follow_status'] = $this->relativeFollowStatus($relativedId, $i['user_id']);
            $res[] = $u;
        }

        return $res;
    }

    public function fansList($userId, $relativedId = 0, $page = 1, $pageSize = 30)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('uf.follower_id, u.avatar, u.username')
            // ->addSelect($expr->exists('select * from uf where uf.user_id = :user_id AND uf.follower_id = uid'))
            ->from('App:UserFollower', 'uf')
            ->where('uf.user_id = :user_id')
            ->andWhere('uf.deletedAt is null')
            ->leftJoin('App:User', 'u', Join::WITH, 'uf.user_id = u.id')
            ->orderBy('uf.id', 'desc')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->setParameter('user_id', $userId);
        // dd($query->getQuery());
        $p = new Paginator($query);
        $p->setUseOutputWalkers(false);
        
        $res = [];
        if (0 === count($p)) {
            return $res;
        }
        foreach ($p as $i) {
            $u = $this->userRepo->getInfo($this->userRepo->findOneBy(['id' => $i['follower_id']]));
            $u['follow_status'] = $this->relativeFollowStatus($relativedId, $i['follower_id']);
            $res[] = $u;
        }

        return $res;
    }

    // /**
    //  * @return UserFollower[] Returns an array of UserFollower objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserFollower
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
