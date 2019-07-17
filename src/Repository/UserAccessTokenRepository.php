<?php

namespace App\Repository;

use App\Entity\UserAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\User;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityNotFoundException;
use Gedmo\SoftDeleteable\Query\TreeWalker\SoftDeleteableWalker;

/**
 * @method UserAccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAccessToken[]    findAll()
 * @method UserAccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAccessTokenRepository extends ServiceEntityRepository
{
    const EXPIRED_TIME = '+ 12hour'; //access token过期时间12小时
    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserAccessToken::class);
    }

    public function makeToken(User $user, $client): UserAccessToken
    {
        // 生成token
        $userAccessToken = new UserAccessToken();
        $userAccessToken->setUserId($user->getId());
        $userAccessToken->setToken($this->generateToken());
        $userAccessToken->setRefreshToken($this->generateToken());
        $userAccessToken->setExpires(new \DateTime(self::EXPIRED_TIME));
        $userAccessToken->setScope([]);
        $userAccessToken->setClient($client);

        $this->_em->transactional(function ($em) use ($user, $client, $userAccessToken) {
            // 生成新的token先清理掉对应平台的已经生效的token
            $this->clearExistedToken($user, $client);

            $em->persist($userAccessToken);
            $em->flush();
        });

        return $userAccessToken;
    }

    public function refreshToken($token, $refreshToken)
    {
        $t = $this->findOneBy([
            'token' => $token,
            'refresh_token' => $refreshToken,
        ]);
        if (empty($t) || $t->isDeleted()) {
            throw new EntityNotFoundException('token不存在');
        }
        $t->setToken($this->generateToken());
        $t->setExpires(new \DateTime(self::EXPIRED_TIME));
        $this->_em->flush();

        return $t;
    }

    protected function clearExistedToken(User $user, $client)
    {
        $r = $this->_em->createQueryBuilder()
            ->delete('App:UserAccessToken', 'aat')
            ->where('aat.user_id = :aid')
            ->andWhere('JSON_EXTRACT(aat.client, \'$.platform\') = :platform')
            ->andWhere('aat.expires > :date')
            ->setParameters([
                'aid' => $user->getId(),
                'platform' => $client['platform'],
                'date' => new \DateTime(),
            ]);
        $r->getQuery()
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SoftDeleteableWalker::class)
            ->getResult();
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    // /**
    //  * @return UserAccessToken[] Returns an array of UserAccessToken objects
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
    public function findOneBySomeField($value): ?UserAccessToken
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
