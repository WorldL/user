<?php

namespace App\Repository;

use App\Entity\Major;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;
use JMS\Serializer\SerializerInterface;

/**
 * @method Major|null find($id, $lockMode = null, $lockVersion = null)
 * @method Major|null findOneBy(array $criteria, array $orderBy = null)
 * @method Major[]    findAll()
 * @method Major[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MajorRepository extends ServiceEntityRepository
{
    private $serializer;
    private $hotMajorList = [
        '金融学','会计学','航空航天工程','机械工程','土木工程','计算机工程','环境工程','市场营销学',
        '音乐','国际贸易','金融工程','计算机科学','市场营销学','人力资源','精算学'
    ];
    public function __construct(
        RegistryInterface $registry,
        SerializerInterface $serializer
    )
    {
        parent::__construct($registry, Major::class);
        $this->serializer = $serializer;
    }

    public function list()
    {
        $all = $this->_em->createQueryBuilder()
            ->select('m')
            ->from('App:Major', 'm')
            ->getQuery();
        
        return $all->getResult(Query::HYDRATE_ARRAY);
    }

    //专业库 一级
    public function first($search)
    {

        if(!empty($search)){
            //通过模糊搜索查询出有哪些专业
            $searchMajor = $this->_em->createQueryBuilder()
                    ->select('m.id,m.name_cn,m.name_en,m.category_cn')
                    ->from('App:Major','m')
                    ->where('m.name_cn like :search')
                    ->setParameter('search','%'.$search.'%')
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);

    //        var_dump($searchMajor);
            $majorCategory =[];
            foreach ($searchMajor as $key=>$value){

                $majorCategory[$value['category_cn']][] = [
                    'id' => $value['id'],
                    'name_cn'=>$value['name_cn']
                ];
            }

//            var_dump($majorCategory);


            $majorCategoryList =[];
            foreach ($majorCategory as $key=>$value){
                $majorCategoryList[] = [
                    'category_name' => $key,
                    'list' => $value
                ];
            }

        }else{
            //获取专业类别
            $all = $this->_em->createQueryBuilder()
                        ->select('m.category_cn')
                        ->from('App:Major','m');

             $all = $all->getQuery();
            $major =  $all->getResult(Query::HYDRATE_ARRAY);
            foreach ($major as $value){
                $majorCategory[] = $value['category_cn'];
            }
            //去重
            $majorCategory = array_unique($majorCategory);
            //根绝类别查询出专业名称
            foreach ($majorCategory as $value){
                $majorName = $this->_em->createQueryBuilder()
                                                ->select('m.id,m.name_cn,m.name_en')
                                                ->from('App:Major','m')
                                                ->where('m.category_cn = :category_cn')
                                                ->setParameter('category_cn',$value)
                                                ->getQuery()
                                                ->getResult(Query::HYDRATE_ARRAY);



                $majorCategoryList[] = [
                    'category_name' => $value,
                    'list' => $majorName
                ];
            }

        }


        $majorHot =[];
        foreach ($majorCategoryList as $value){
            foreach ($value['list'] as $key=>&$v){
                if(in_array($v['name_cn'],$this->hotMajorList)){
                  $v['is_hot_major'] = 'true';

                }else{
                    $v['is_hot_major'] = 'false';

                }
            }

            $sort = array_column($value['list'],'is_hot_major');
            array_multisort($sort,SORT_DESC,$value['list']);


//            var_dump($value['major_name']);
            $majorHot[] = [
                'category'=>$value['category_name'],
                'list'=>$value['list']
            ];
        }

        return $majorHot;
    }


    //专业详情
    public function details($id)
    {

        $major = $this->_em->getRepository(Major::class)->findOneBy(['id'=>$id]);
        if(empty($major)){
            $majorDetails =[];
        }else{
            $major = $this->serializer->toArray($major);
    //        var_dump($major);
    //        die;
            //男女比例 $proportion
            if(empty($major['gender_ratio'])){
                $proportion = null;
            }else{
                $genderRatio = $major['gender_ratio'];
                $genderRatio = explode(":",$genderRatio);
                $proportion['male'] = $genderRatio[0]."%";
                $proportion['femal'] = $genderRatio[1]."%";
            }
            //课程列表 $course
            if(empty($major['course'])){
                $course = null;
            }else{
                $courseList = $major['course'];
                $courseList = explode('-',$courseList);
                foreach ($courseList as $value){
                    $value = explode("(",$value);
                    $tmp[] =[
                        'nameCn' => $value[0],
                        'nameEn' => substr($value[1],0,-1)
                    ];
                    $course = $tmp;
                }
            }
            //未来发展
            if(empty($major['future_trend'])){
                $future = null;
            }else{
                $name = '未来发展';
               $future = $name.':'.$major['future_trend'];
            }
            $majorDetails = [
                'intro' => $major['intro'],
                'genderRadio' => $proportion,
                'visa' => $major['visa_sensitivity'],
                'employment' => $major['employment_status'],
                'futureTrend' => $future,
                'course' => $course
            ];
        }
        return $majorDetails;
    }


    // /**
    //  * @return Major[] Returns an array of Major objects
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
    public function findOneBySomeField($value): ?Major
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
