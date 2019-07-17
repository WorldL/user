<?php

namespace App\Repository;

use App\Entity\GlobalMajorInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GlobalMajorInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalMajorInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalMajorInfo[]    findAll()
 * @method GlobalMajorInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalMajorInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GlobalMajorInfo::class);
    }


    //筛选条件
    public function screen()
    {
        //国家/地区
        $countryUS  = [
            'en' => 'US',
            'cn' => '美国'
        ];
        $countryGlobal =[
            'en' => 'global',
            'cn' => '全球'
        ];
        $countryRegion = [
            $countryUS,
            $countryGlobal
        ];

        //专业列表
        $majorCategory= $this->_em->createQueryBuilder()
                    ->select('g.major_category')
                    ->from('App:GlobalMajorInfo','g')
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
        $majorCategory = array_unique($majorCategory,SORT_REGULAR);

        foreach ($majorCategory as $value){
            $category = $value['major_category'];
            $majorList = $this->_em->createQueryBuilder()
                    ->select('g.id,g.major_name')
                    ->from('App:GlobalMajorInfo','g')
                    ->where('g.major_category = :major_category')
                    ->setParameter('major_category',$category)
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
            foreach ($majorList as $v){
                $globalTmp[$category][] = [
                    'id' => $v['id'],
                    'major'=>$v['major_name']
                ];
            }
            $globalMajorList = $globalTmp;
        }

        $majorScreen = [
            'country' =>$countryRegion,
            'major'=>$globalMajorList
        ];
        return $majorScreen;
    }
















    // /**
    //  * @return GlobalMajorInfo[] Returns an array of GlobalMajorInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GlobalMajorInfo
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
