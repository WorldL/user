<?php

namespace App\Repository;

use App\Entity\USMajorInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use JMS\Serializer\SerializerInterface;

/**
 * @method USMajorInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method USMajorInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method USMajorInfo[]    findAll()
 * @method USMajorInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class USMajorInfoRepository extends ServiceEntityRepository
{
    //国家列表
    private $majorCountry=[
        'US'=>'美国'
    ];
    //学位列表
    private $majorEducation=[
        'und'=>'学士',
        'gra'=>'硕士'

    ];
    private $em;
    private $serializer;
    public function __construct(
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        parent::__construct($registry, USMajorInfo::class);
        $this->em = $entityManager;
        $this->serializer = $serializer;
    }

    //获取筛选条件
    public function screen()
    {
        //获取条件
        $screenList = $this->em->createQueryBuilder()
             ->select('u.major_name,u.major_category,u.rank_category,u.education,u.country')
             ->from('App:USMajorInfo','u')
             ->getQuery()
             ->getResult(Query::HYDRATE_ARRAY);

        foreach ($screenList as $value){
            $country[] = $value['country'];
            $education[] = $value['education'];
            $rankCategory[] = $value['rank_category'];
        }
        //国家列表
        $country = array_unique($country);
        foreach ($country as $value){
            $countryList[] = [
                'en' => $value,
                'cn' => $this->majorCountry[$value]
            ];
        }
        //学位列表
        $education = array_unique($education);
        foreach ($education as $value){
           $educationList[] =[
               'en'=>$value,
               'cn'=>$this->majorEducation[$value]
           ];
       }
        //排行类别
        $rankCategory = array_unique($rankCategory);

        //专业列表 本科对应关系
        $undMajorCategory = $this->em->createQueryBuilder()
            ->select('u.major_category')
            ->from('App:USMajorInfo','u')
            ->where('u.education = :education')
            ->setParameter('education','und')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($undMajorCategory as $value){
            $undMajorCategoryList[] =$value['major_category'];
        }
        $undMajorCategoryList = array_unique($undMajorCategoryList);
        foreach ($undMajorCategoryList as $value){

            $majorList = $this->em->createQueryBuilder()
                ->select('u.id,u.major_name')
                ->from('App:USMajorInfo','u')
                ->where('u.major_category=:major_category')
                ->andWhere('u.education = :education')
                ->setParameters(['major_category'=>$value,'education'=>'und'])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
            foreach ($majorList as $v){
                $UndTmp[$value][] = [
                    'id' => $v['id'],
                    'major'=>$v['major_name']
                ];
            }
            $undMajorList = $UndTmp;
        }
        //专业列表 研究生对应关系
        $graMajorCategory = $this->em->createQueryBuilder()
            ->select('u.major_category')
            ->from('App:USMajorInfo','u')
            ->where('u.education = :education')
            ->setParameter('education','gra')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($graMajorCategory as $value){
            $graMajorCategoryList[] =$value['major_category'];
        }
        $graMajorCategoryList = array_unique($graMajorCategoryList);

        foreach ($graMajorCategoryList as $value){
            $majorList = $this->em->createQueryBuilder()
                ->select('u.id,u.major_name')
                ->from('App:USMajorInfo','u')
                ->where('u.major_category=:major_category')
                ->andWhere('u.education = :education')
                ->setParameters(['major_category'=>$value,'education'=>'gra'])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
//            var_dump($majorList1);
            foreach ($majorList as $v){
                $graTmp[$value][] = [
                    'id' => $v['id'],
                    'major'=>$v['major_name']
                ];
            }
            $graMajorList = $graTmp;
        }

        $majorScreen =[
            'country' => $countryList,
            'education'=>$educationList,
            'rank_category'=>$rankCategory,
            'und'=>$undMajorList,
            'gra'=>$graMajorList
        ];
        return $majorScreen;
    }






    // /**
    //  * @return USMajorInfo[] Returns an array of USMajorInfo objects
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
    public function findOneBySomeField($value): ?USMajorInfo
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
