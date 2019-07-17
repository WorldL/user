<?php

namespace App\Repository;

use App\Entity\MajorInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method MajorInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method MajorInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method MajorInfo[]    findAll()
 * @method MajorInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MajorInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MajorInfo::class);
    }

    public function list()
    {

        //获取专业列表的专业类型
        $all = $this->_em->createQueryBuilder()
            ->select('m.category_cn')
            ->from('App:MajorInfo', 'm')
            ->getQuery();


        $all = $all->getResult(Query::HYDRATE_ARRAY);

        $categoryList=[];
        foreach ($all as $value){

            $categoryList[] = $value['category_cn'];
        }
        $categoryList = array_unique($categoryList);

        //根据类型获取专业列表

        foreach ($categoryList as $value) {
            $majorName = $this->_em->createQueryBuilder()
                ->select('m.id,m.name_cn')
                ->from('App:MajorInfo', 'm')
                ->where('m.category_cn = :category_cn')
                ->setParameter('category_cn', $value)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

            $majorCategoryList[] = [
                'category_name' => $value,
                'list' => $majorName
            ];

        }

        foreach ($majorCategoryList as $value){

            $majorHot[] = [
                'category'=>$value['category_name'],
                'list'=>$value['list']
            ];
        }

        return $majorHot;
    }





    // /**
    //  * @return MajorInfo[] Returns an array of MajorInfo objects
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
    public function findOneBySomeField($value): ?MajorInfo
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
