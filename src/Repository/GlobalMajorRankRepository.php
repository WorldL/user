<?php

namespace App\Repository;

use App\Entity\GlobalMajorRank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method GlobalMajorRank|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalMajorRank|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalMajorRank[]    findAll()
 * @method GlobalMajorRank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalMajorRankRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GlobalMajorRank::class);
    }

    //全球专业排行列表
    public function list($id,$country,$page,$pageSize,$search)
    {
        $majorRank = $this->_em->createQueryBuilder()
                ->select('g.college_name_cn,g.college_name_en,g.rank,g.college_id')
                ->from('App:GlobalMajorRank','g')
                ->where('g.global_major_info_id = :id')
                ->setParameter('id',$id);

        // 国家
        if($country != 'global'){
            $majorRank->andWhere('g.country = :country')
                    ->setParameter('country',$country);
        }

        //搜索条件模糊查询
        if(!empty($search)){
            $majorRank->andWhere('g.college_name_cn LIKE :search or g.college_name_en LIKE :search')
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
    //  * @return GlobalMajorRank[] Returns an array of GlobalMajorRank objects
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
    public function findOneBySomeField($value): ?GlobalMajorRank
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
