<?php

namespace App\Repository;

use App\Entity\AdminAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Admin;
use Gedmo\SoftDeleteable\Query\TreeWalker\SoftDeleteableWalker;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @method AdminAccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminAccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminAccessToken[]    findAll()
 * @method AdminAccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminAccessTokenRepository extends ServiceEntityRepository
{
    const EXPIRED_TIME = '+ 30day'; //access token过期时间30天

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AdminAccessToken::class);
    }

    public function makeToken(Admin $admin, $client): AdminAccessToken
    {
        // 生成token
        $adminAccessToken = new AdminAccessToken();
        $adminAccessToken->setAdminId($admin->getId());
        $adminAccessToken->setToken($this->generateToken());
        $adminAccessToken->setRefreshToken($this->generateToken());
        $adminAccessToken->setExpires(new \DateTime(self::EXPIRED_TIME));
        $adminAccessToken->setScope([]);
        $adminAccessToken->setClient($client);

        $this->_em->transactional(function ($em) use ($admin, $client, $adminAccessToken) {
            // 生成新的token先清理掉对应平台的已经生效的token
            $this->clearExistedToken($admin, $client);

            $em->persist($adminAccessToken);
            $em->flush();
        });

        return $adminAccessToken;
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

    protected function clearExistedToken(Admin $admin, $client)
    {
        $r = $this->_em->createQueryBuilder()
            ->delete('App:AdminAccessToken', 'aat')
            ->where('aat.admin_id = :aid')
            ->andWhere('JSON_EXTRACT(aat.client, \'$.platform\') = :platform')
            ->andWhere('aat.expires > :date')
            ->setParameters([
                'aid' => $admin->getId(),
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
    //  * @return AdminAccessToken[] Returns an array of AdminAccessToken objects
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
    public function findOneBySomeField($value): ?AdminAccessToken
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
