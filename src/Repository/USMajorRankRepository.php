<?php

namespace App\Repository;

use App\Entity\USMajorRank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method USMajorRank|null find($id, $lockMode = null, $lockVersion = null)
 * @method USMajorRank|null findOneBy(array $criteria, array $orderBy = null)
 * @method USMajorRank[]    findAll()
 * @method USMajorRank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class USMajorRankRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, USMajorRank::class);
    }


    //专业排行列表
    public function list($id,$page,$pageSize,$search)
    {
        $majorRank = $this->_em->createQueryBuilder()
                ->select('u.college_name_cn,u.college_name_en,u.rank,u.college_id')
                ->from('App:USMajorRank','u')
                ->where('u.us_major_info_id = :id')
                ->setParameter('id',$id);

        //搜索条件模糊查询
        if(!empty($search)){
            $majorRank->andWhere('u.college_name_cn LIKE :search or u.college_name_en LIKE :search')
                ->setParameter('search','%'.$search."%");
        }

        $totalNum = count($majorRank->getQuery()->getResult(Query::HYDRATE_ARRAY));

        $majorRank = $majorRank
                ->setFirstResult(($page - 1) * $pageSize)
                ->setMaxResults($pageSize)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $majorRankList = [];
        foreach ($majorRank as $value){


            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';

//            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$value['college_name_cn'].'.png';

            //判断图片存不存在
               $majorRankList[] = [
                   'id'=>$value['college_id'],
                   'name_cn'=>$value['college_name_cn'],
                   'name_en'=>$value['college_name_en'],
                   'crest' =>$crest,
                   'rank' => $value['rank']
               ];

        }

        $majorRankList=[
            'total_num' =>$totalNum,
            'data'=>$majorRankList
        ];

    return $majorRankList;
    }








    // /**
    //  * @return USMajorRank[] Returns an array of USMajorRank objects
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
    public function findOneBySomeField($value): ?USMajorRank
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
