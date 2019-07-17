<?php

namespace App\Repository;

use App\Entity\College;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Swagger\Annotations\Swagger as SWG;

/**
 * @method College|null find($id, $lockMode = null, $lockVersion = null)
 * @method College|null findOneBy(array $criteria, array $orderBy = null)
 * @method College[]    findAll()
 * @method College[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(
        RegistryInterface $registry,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, College::class);
        $this->em = $entityManager;
    }

    //文书库筛选条件学校列表

    public function docCollegeList($docCollegeIdList,$search)
    {
        $college = $this->em->createQueryBuilder()
                ->select('c.id,c.name_cn,c.name_en')
                ->from('App:College','c')
                ->where('c.id in (:docCollegeIdList)')
                ->setParameter('docCollegeIdList',$docCollegeIdList);
        //搜索条件模糊查询
        if(!empty($search)){
            $college->andWhere('c.name_cn LIKE :search')
                ->orWhere('c.name_en LIKE :search')
                ->setParameter('search','%'.$search."%");
        }

         $college = $college
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);


        return $college;

    }


    // /**
    //  * @return College[] Returns an array of College objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?College
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    //学校列表
    public function list($country,$region,$tuition,$toefl,$ielts,$categoryList,$page,$pageSize,$search)
    {

        //连表college_repo 来进行展示..
        $college = $this->em->createQueryBuilder()
            ->select('c.id,c.name_cn,c.name_en,c.address')
            ->from("App:CollegeRepo","r")
            ->leftJoin('App:College','c','WITH','r.college_id=c.id')
            ->where('c.country=:country')
            ->setParameter('country',$country)
            ->andWhere('c.is_show=:is_show')
            ->setParameter('is_show','yes')
            ->orderBy('r.sort','ASC');


        //增加地域筛选条件
        if(!empty($region)){

            $college->andWhere('c.region in (:region)')

                    ->setParameter('region',$region);

//            if(isset($region[0])){
//                $college->andWhere('c.region=:regionOne')
//                        ->setParameter('regionOne',$region[0]);
//            }
//            if(isset($region[1])){
//                $college->orWhere('c.region=:regionTwo')
//                        ->setParameter('regionTwo',$region[1]);
//            }
//            if(isset($region[2])){
//                $college->orWhere('c.region=:regionThree')
//                        ->setParameter('regionThree',$region[2]);
//            }
//            if(isset($region[3])){
//                $college->orWhere('c.region=:regionFour')
//                        ->setParameter('regionFour',$region[3]);
//            }
        }


//        var_dump($tuition);
//        die;

        //学校列表学费
        if(!empty($tuition)){
            if(isset($tuition['min'])){
                if(!empty($tuition['min'])){
                    $tuitionMin = intval($tuition['min']*10000/6.9);
                    $college->andWhere('c.tuition_fee_undergraduate >= :tuitionMin')
                        ->setParameter('tuitionMin',$tuitionMin);
                }

            }

            if(isset($tuition['max'])){
                if(!empty($tuition['max'])){
                    if($tuition['max'] != "50+"){
                        $tuitionMax = intval($tuition['max']*10000/6.9);
                        $college->andWhere('c.tuition_fee_undergraduate <= :tuitionMax')
                            ->setParameter('tuitionMax',$tuitionMax);
                    }
                }

            }
        }

        //学校列表  托福成绩要求
        if(!empty($toefl)){
            if(isset($toefl['min'])){
                if(!empty($toefl['min'])){
                    $college->andWhere('c.toefl_undergraduate >= :toeflMin')
                        ->setParameter('toeflMin',$toefl['min']);
                }
            }
            if(isset($toefl['max'])){

                if(!empty($toefl['max'])){
                    $college->andWhere('c.toefl_undergraduate <=:toeflMax')
                        ->setParameter('toeflMax',$toefl['max']);
                }
            }
        }

        //学校列表  雅思成绩要求
        if(!empty($ielts)){
            if(isset($ielts['min'])){
                if(!empty($ielts['min'])){
                    $college->andWhere('c.ielts_undergraduate >= :ieltsMin')
                        ->setParameter('ieltsMin',$ielts['min']);
                }
            }
            if(isset($ielts['max'])){
                if(!empty($toefl['max'])){
                    $college->andWhere('c.ielts_undergraduate <=:ieltsMax')
                        ->setParameter('ieltsMax',$ielts['max']);
                }
            }
        }
        //学校列表  学校类型筛选
        if(!empty($categoryList)){

            $college->andWhere('c.category in (:category)')
                    ->setParameter('category',$categoryList);


//            if(isset($categoryList[0])){
//                $college->andWhere('c.category = :categoryOne')
//                        ->setParameter('categoryOne',$categoryList[0]);
//            }
//            if(isset($categoryList[1])){
//                $college->orWhere('c.category = :categoryTwo')
//                        ->setParameter('categoryTwo',$categoryList[1]);
//            }
//            if(isset($categoryList[2])){
//                $college->orWhere('c.category = :categoryThree')
//                        ->setParameter('categoryThree',$categoryList[2]);
//            }
        }


        //搜索条件模糊查询
        if(!empty($search)){
            $college->andWhere('c.name_cn LIKE :search')
                    ->orWhere('c.name_en LIKE :search')
                    ->setParameter('search','%'.$search."%");
        }
//        dd($college);

        $totalNum = count($college->getQuery()->getResult(Query::HYDRATE_ARRAY));



        $college = $college
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery();
        $collegeList = [
            'totalNum' => $totalNum,
            'data' => $college->getResult(Query::HYDRATE_ARRAY)
        ];

        if(empty($collegeList['totalNum'])){
            $collegeList = [];
        }

        return $collegeList;
    }


    //获取筛选条件
    public function screen()
    {
        //获取筛选条件
        $college = $this->em->createQueryBuilder()
            ->select('c.region,c.country,c.category')
            ->from("App:College","c")
            ->getQuery();
        return $college->getResult(Query::HYDRATE_ARRAY);

    }
}
