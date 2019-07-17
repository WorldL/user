<?php

namespace App\Repository;

use App\Entity\MajorInfo;
use App\Entity\UserEducation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;
use App\Entity\CollegeEmailDomain;
use App\Service\Email;

/**
 * @method UserEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEducation[]    findAll()
 * @method UserEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEducationRepository extends ServiceEntityRepository
{
    private $userRepo;
    private $collegeRepo;
    private $majorRepo;
    private $majorInfoRepo;

    public function __construct(
        RegistryInterface $registry,
        UserRepository $userRepo,
        CollegeRepository $collegeRepo,
        MajorRepository $majorRepo,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepo = $userRepo;
        $this->collegeRepo = $collegeRepo;
        $this->majorRepo = $majorRepo;
        parent::__construct($registry, UserEducation::class);
        $this->majorInfoRepo = $entityManager->getRepository(MajorInfo::class);
    }

    public function expByUser($userId)
    {
        $query = $this->_em->createQueryBuilder()
            ->select(
                'ue.id, ue.degree, ue.status, ue.graduate_year, ue.graduate_month, ue.as_default',
                'c.name_cn as college_name_cn, c.name_en as college_name_en, m.name_cn as major_name_cn'
            )
            ->from('App:UserEducation', 'ue')
            ->innerJoin('App:College', 'c', Join::WITH, 'ue.college_id = c.id')
            ->leftJoin('App:MajorInfo', 'm', Join::WITH, 'ue.major_id = m.id')
            ->where('ue.user_id = :user_id AND ue.deletedAt is null')
            ->setParameters(['user_id' => $userId])
            ;
        $res = $query->getQuery()->getArrayResult();

        foreach ($res as &$i) {
            $i['degree_show'] = UserEducation::$degreeMap[$i['degree']];
            $i['status_show'] = UserEducation::$statusMap[$i['status']];
            $i['as_default'] = 'YES' === $i['as_default'] ? 'yes' : 'no';
            $i['crest'] = 'http://cdn.xiaohailang.net/common/crest/' . $i['college_name_cn'] . '.png';
            $i['major_name_cn'] = $i['major_name_cn'] ?? '其他';
        }

        return $res;
    }

    public function addEdu($userId, $collegeId, $majorId, $degree, $graduateYear, $graduateMonth)
    {
        $user = $this->userRepo->find($userId);
        if (empty($user)) {
            throw new \Exception('用户不存在');
        }
        $college = $this->collegeRepo->find($collegeId);
        if (empty($college)) {
            throw new \Exception('学校不存在');
        }
        $major = $this->majorInfoRepo->find($majorId);
        if (empty($major) && 0 != $majorId) {
            throw new \Exception('专业不存在');
        }
        if (!isset(UserEducation::$degreeMap[$degree])) {
            throw new \Exception('学位不存在');
        }

        $userEdu = (new UserEducation())
            ->setUserId($userId)
            ->setCollegeId($collegeId)
            ->setMajorId($majorId)
            ->setDegree($degree)
            ->setGraduateYear($graduateYear)
            ->setGraduateMonth($graduateMonth)
            ->setStatus('UNAUTHORIZED');

        $this->_em->persist($userEdu);
        $this->_em->flush();

        return $userEdu;
    }

    public function getDefault($userId)
    {
        $edu = $this->findOneBy([
            'user_id' => $userId,
            'as_default' => 'YES',
            'deletedAt' => null,
        ]);

        if (empty($edu)) {
            return null;
        }

        $college = $this->collegeRepo->find($edu->getCollegeId());

        return [
            'college_id' => (string) $college->getId(),
            'college_name' => $college->getNameCn(),
            'verified' => $edu->getStatus() == 'AUTHORIZED' ? 'yes' : 'no',
            'graduate_year' => substr($edu->getGraduateYear(), 2, 2),
        ];
    }

    // 如果已经是默认则取消默认，如果该记录不是默认则设置为默认
    public function asDefault($eduId)
    {
        $edu = $this->find($eduId);
        if (empty($edu) || $edu->isDeleted()) {
            return;
        }
        $this->_em->transactional(function ($em) use ($edu) {
            // make all this use's edus not default
            $u = $em->createQueryBuilder()
                ->update('App:UserEducation', 'ue')
                ->set('ue.as_default', ':null')
                ->where('ue.user_id = :user_id AND ue.deletedAt is null')
                ->setParameters(['user_id' => $edu->getUserId(), 'null' => null])
                ->getQuery()
                ->execute();
            // make this edu default
            if (empty($edu->getAsDefault())) {
                $edu->setAsDefault('YES');
            }
            $em->merge($edu);

            $em->flush();
        });

        return;
    }

    public function verify($id, $email)
    {
        $edu = $this->findOneBy(['id' => $id]);
        if (empty($edu) || $edu->isDeleted()) {
            throw new \Exception('教育经历不存在或已删除');
        }
        if ('UNAUTHORIZED' !== $edu->getStatus()) {
            throw new \Exception('请不要重复认证');
        }
        $ced = $this->_em->getRepository(CollegeEmailDomain::class);
        try {
            $emailDomain = explode('@', $email)[1];
            $cedModel = $ced->findOneBy(['domain' => $emailDomain]);
            if (empty($cedModel)) {
                // 不存在走审核流程
                $edu->setVerifyEmail($email)
                    ->setStatus('AUTHORIZING');
                $this->_em->merge($edu);
                $this->_em->flush();
                return [
                    'type' => 'pending',
                    'msg' => '我们会尽快审核您的邮箱资料，通过后会给您发送认证邮件，请耐心等待。',
                ];
            } else {
                // 存在，邮箱不是这个学校
                if ($cedModel->getCollegeId() != $edu->getCollegeId()) {
                    throw new \Exception('邮箱域名错误，请检查您填写邮箱是否正确。');
                }
                //
                $edu->setVerifyEmail($email)
                    ->setStatus('AUTHORIZING');
                $this->_em->merge($edu);
                $this->_em->flush();
                // 存在，邮箱正确,发送邮件
                $this->sendVerifyEmail($edu);
                return [
                    'type' => 'ok',
                    'msg' => '认证邮件已发送到您的学校邮箱，请在24小时内点击邮件内链接进行认证。',
                ];
            }
        } catch (\Exception $e) {
            throw new \Exception('邮箱格式错误');
        }
    }

    public function detail($eduId)
    {
        $query = $this->_em->createQueryBuilder()
            ->select(
                'ue.id, ue.user_id, ue.degree, ue.status, ue.graduate_year, ue.graduate_month, ue.as_default',
                'ue.verify_email, c.name_cn as college_name_cn, c.name_en as college_name_en, m.name_cn as major_name_cn'
            )
            ->from('App:UserEducation', 'ue')
            ->innerJoin('App:College', 'c', Join::WITH, 'ue.college_id = c.id')
            ->leftJoin('App:MajorInfo', 'm', Join::WITH, 'ue.major_id = m.id')
            ->where('ue.id = :edu_id AND ue.deletedAt is null')
            ->setParameters(['edu_id' => $eduId])
            ;
        try {
            $edu = $query->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            throw new \Exception('教育经历不存在');
        }
        if ('AUTHORIZING' == $edu['status'] && !empty($edu['verify_email'])) {
            $edu['verify_key'] = $this->calculateVerifyKey($edu['id'], $edu['verify_email']);
        }

        return $edu;
    }

    public function sendVerifyEmail(UserEducation $edu)
    {
        $emailSvc = new Email();
        $key = $this->calculateVerifyKey($edu->getId(), $edu->getVerifyEmail());
        $url = $_ENV['H5_URL'] ?? 'http://h5.xiaohailang.net/';
        $url .= 'authentication-phone/' . $edu->getId() . '/?key='. $key;

        $user = $this->userRepo->find($edu->getUserId());
        $username = $user->getUsername();

        $ced = $this->_em->getRepository(CollegeEmailDomain::class);
        $emailDomain = explode('@', $edu->getVerifyEmail())[1];
        $cedModel = $ced->findOneBy(['domain' => $emailDomain]);
        if (empty($cedModel) || $cedModel->getCollegeId() != $edu->getCollegeId()) {
            throw new \Exception('未审核的邮箱地址，请等待审核');
        }

        $msg = (new \Swift_Message('小海浪教育经历认证'))
            ->setTo($edu->getVerifyEmail())
            ->setBody("$username 您好，\n\n根据您的教育经历认证请求，请点击\n$url\n如果链接不能自动跳转，请尝试拷贝以上链接至浏览器的地址栏访问网页。"
            . "\n如果认证申请不是由您发出，请忽略。\n\n\n小海浪App");

        return $emailSvc->send($msg);
    }

    public function calculateVerifyKey($eduId, $email)
    {
        return md5("$email:la-ola:verify:$eduId");
    }

    // /**
    //  * @return UserEducation[] Returns an array of UserEducation objects
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
    public function findOneBySomeField($value): ?UserEducation
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
