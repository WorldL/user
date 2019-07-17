<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\Query;
use Doctrine\Common\Inflector\Inflector;
use App\Service\InfoClient;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\GenericSerializationVisitor;
use App\Entity\UserEducation;
use App\Entity\MsgSystem;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    const WELCOM_MSG = '欢迎您加入小海浪大家族，期间如有任何问题，可与小海浪客服联系~';

    const INFO_FAV_COUNT_URL = '/info/fav-count';
    const INFO_COL_COUNT_URL = '/info/col-count';

    /**
     * UserPasswordEncoder
     *
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $encoder
     */
    private $encoder;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \JMS\Serializer\Serializer $serializer
     */
    private $serializer;

    private $infoClient;

    private $eduRepo;

    public function __construct(
        RegistryInterface $registry,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        CollegeRepository $collegeRepository,
        MajorRepository $majorRepository,
        InfoClient $infoClient,
        EntityManagerInterface $entityManager
    ) {
        $this->encoder = $encoder;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->eduRepo = new UserEducationRepository($registry, $this, $collegeRepository, $majorRepository,$entityManager);
        $this->infoClient = $infoClient;
        parent::__construct($registry, User::class);
    }

    public function edit(User $user, array $changes)
    {
        $allowEdit = ['username', 'gender', 'birthday', 'avatar', 'sign'];
        // 编辑用户基础信息
        foreach ($changes as $k => $v) {
            if (property_exists($user, $k) && in_array($k, $allowEdit)) {
                $user->{'set'.Inflector::camelize($k)}($v);
            }
        }
        $this->em->merge($user);
        $this->em->flush();

        return $user;
    }

    public function getInfo(User $user, $scope = 'base')
    {
        // $serializer = SerializerBuilder::create()->setSerializationVisitor('json', new class extends GenericSerializationVisitor
        // {
        //     public function visitNull($data, array $type, Context $context)
        //     {
        //         return '';
        //     }
        // });
        $info = $this->serializer->toArray($user, SerializationContext::create()->setSerializeNull(true));
        // dd($this->serializer);
        $info['avatar'] = $user->getAvatarUrl();
        unset($info['password']);
        unset($info['roles']);
        unset($info['deleted_at']);
        $info['phone'] = substr($info['phone'], 0, 3) . '****' . substr($info['phone'], strlen($info['phone']) - 3, 3);
        // 生日转年龄
        $info['age'] = empty($user->getBirthday()) ? '' : $user->getBirthday()->diff(new \DateTime())->y;
        $info['birthday'] = empty($info['birthday']) ? '' : $info['birthday'];
        $info['sign'] = empty($info['sign']) ? '' : $info['sign'];
        // 学校信息+毕业年份+KOL
        $defaultEdu = $this->eduRepo->getDefault($user->getId());
        // TODO KOL
        $info['is_kol'] = strtolower($info['is_kol']);
        if ($defaultEdu) {
            $info['edu'] = $defaultEdu;
        } else {
            $info['edu'] = [
                'college_id' => '',
                'college_name' => '',
                'verified' => 'no',
                'graduate_year' => '',
            ];
        }

        if ('detail' !== $scope) {
            return $info;
        }

        // 关注与粉丝数
        $info['fans_count'] = $this->_em->createQueryBuilder()
            ->select('count(uf.id) as count')
            ->where('uf.user_id = :user_id AND uf.deletedAt IS NULL')
            ->from('App:UserFollower', 'uf')
            ->setParameters(['user_id' => $user->getId()])
            ->getQuery()
            ->getSingleScalarResult();
        $info['follows_count'] = $this->_em->createQueryBuilder()
            ->select('count(uf.id) as count')
            ->where('uf.follower_id = :user_id AND uf.deletedAt IS NULL')
            ->from('App:UserFollower', 'uf')
            ->setParameters(['user_id' => $user->getId()])
            ->getQuery()
            ->getSingleScalarResult();
        // 获赞与收藏
        $info['fav_count'] = $this->infoClient->call(self::INFO_FAV_COUNT_URL, ['user_id' => $user->getId()])['count'];
        $info['col_count'] = $this->infoClient->call(self::INFO_COL_COUNT_URL, ['user_id' => $user->getId()])['count'];
        $info['fav_and_col_count'] = (string) ($info['fav_count'] + $info['col_count']);

        return $info;
    }

    public function findByName($name, $page, $pagesize)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id')
            ->from('App:User', 'u')
            ->where('u.username LIKE :name AND u.deletedAt is null')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->setParameters(['name' => "%$name%"])
            ;
        $p = new Paginator($query);
        $p->setUseOutputWalkers(false);

        $list = [];
        foreach ($p as $i) {
            $list[] = $this->getInfo($this->find($i['id']));
        }

        return $list;
    }

    public function create(
        $region,
        $phone,
        $username,
        $password,
        $gender = 'F',
        $avatar = ''
    ) {
        // 检查有没有重复用户
        if (!empty($this->findOneBy(['phone' => $phone]))
            || !empty($this->findOneBy(['username' => $username]))
        ) {
            throw new \Exception('用户已存在');
        }
        // 创建用户
        $user = new User();
        $user->setUsername($username)
             ->setPassword($this->encoder->encodePassword($user, $password))
             ->setRegionCode($region)
             ->setPhone($phone)
             ->setGender($gender)
             ->setIsKol('NO')
             ->setAvatar($avatar);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $this->_em->getRepository(MsgSystem::class)
            ->addMsg($user->getId(), self::WELCOM_MSG);

        return $user;
    }

    public function batchAdd($data)
    {
        $this->_em->transactional(function ($em) use ($data) {
            foreach ($data as $item) {
                $avatar = 'F' == $item['gender'] ?
                    'avatar/common/f/'.rand(1, 6064).'.jpeg' :
                    'avatar/common/m/'.rand(1, 4482).'.jpeg';
                $user = new User();
                $user->setUsername($item['username'])
                    ->setPassword($this->encoder->encodePassword($user, $item['password']))
                    ->setRegionCode($item['region'])
                    ->setPhone($item['phone'])
                    ->setGender($item['gender'])
                    ->setIsKol('NO')
                    ->setAvatar($avatar);
                $this->_em->persist($user);
            }
            $this->_em->flush();
        });
    }

    public function checkPassword($phone, $password)
    {
        $user = $this->findOneBy(['phone' => $phone]);
        
        if (empty($user)) {
            return false;
        }

        return $this->encoder->isPasswordValid($user, $password);
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
