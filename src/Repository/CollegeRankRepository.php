<?php

namespace App\Repository;

use App\Entity\CollegeRank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollegeRank|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeRank|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeRank[]    findAll()
 * @method CollegeRank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeRankRepository extends ServiceEntityRepository
{

    //排行类别列表
    private $rankOrgList=[
        'QS'=>'QS',
        'shjd'=>'上海交大',
        'thames'=>'泰晤士',
        'USNEWS'=>'USNEWS',
        'forbes'=>'福布斯'
    ];

    //学校排名机构排序
    private $rankOrgRank = [
        'USNEWS'=>1,
        'QS'=>2,
        'forbes'=>3,
        'thames'=>4,
        'shjd'=>5
    ];
    //国家列表
    private $countryList=
        ['US'=>'美国','GBR'=>'英国','FRA'=>'法国','AUS'=>'澳大利亚','CAN'=>'加拿大','ITA'=>'意大利',
         'FIN'=>'芬兰','SIN'=>'新加坡','BEL'=>'比利时','ESP'=>'西班牙','NOR'=>'挪威','CHN'=>'中国',
         'KOR'=>'韩国','DEN'=>'丹麦','GER'=>'德国','JPN'=>'日本','NZL'=>'新西兰','RUS'=>'俄罗斯','NED'=>'荷兰'
        ];
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollegeRank::class);
    }

    //国内文理学院排名

    public function liberalArtList($region,$search,$page,$pageSize)
    {
        $rankList = $this->_em->createQueryBuilder()
            ->select('c.id,c.college_id,c.college_name_cn,c.college_name_en,c.rank')
            ->from('App:CollegeRank','c')
            ->andwhere('c.rank_org = :rank_org')
            ->setParameter('rank_org','USNEWS')
            ->andWhere('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','liberalArt');

        //增加地域筛选条件
        if (!empty($region)){
            $rankList ->andWhere('c.region in (:region)')
                ->setParameter('region',$region);
        }

        //模糊条件查询
        if (!empty($search)){
            $rankList->andWhere('c.college_name_en like :search or c.college_name_cn like :search')
                ->setParameter('search','%'.$search.'%');
        }

        //总条数
        $totalNum = count($rankList->getQuery()->getResult(Query::HYDRATE_ARRAY));

        //分页
        $rankList = $rankList
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $liberalArtRankList = [];
        foreach ($rankList as $value){

            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';

            //判断校徽存不存在(判断已取消)
            $liberalArtRankList[] = [
                'id' => $value['college_id'],
                'name_cn' => $value['college_name_cn'],
                'name_en' => $value['college_name_en'],
                'crest' => $crest,
                'rank'  => $value['rank']
            ];

        }

        $collegeList = [
            'total_num' => $totalNum,
            'data' => $liberalArtRankList
        ];

       return $collegeList;
    }


    /**
     * 国内综合排名
     */
    //国内院校综合排名 排名类型筛选条件 rank_org
    public function domesticRankOrg()
    {
        $rankOrg = $this->_em->createQueryBuilder()
            ->select('c.rank_org')
            ->from('App:CollegeRank','c')
            ->where('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','comprehensive')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $rankOrg = array_unique($rankOrg,SORT_REGULAR);
        foreach ($rankOrg as $value){
            $rankOrgList[] = [
                'en' => $value['rank_org'],
                'cn' => $this->rankOrgList[$value['rank_org']],
                'rank_org_rank'=>$this->rankOrgRank[$value['rank_org']]
            ];
        }

        //根据排名机构权重进行排序

        $sort1 = array_column($rankOrgList,'rank_org_rank');

        array_multisort($sort1,SORT_ASC,$rankOrgList);

        $rankOrgList_1=[];
        foreach ($rankOrgList as $value){

            $rankOrgList_1[]=[
                'en' => $value['en'],
                'cn' => $value['cn']
            ];
        }

        return $rankOrgList_1;
    }

    //国内院校综合排名 国家/地域/学校类型筛选条件
    public function domesticRankCountry()
    {
        $rankCountry = $this->_em->createQueryBuilder()
            ->select('c.country')
            ->from('App:CollegeRank','c')
            ->where('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','comprehensive')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $rankCountry = array_unique($rankCountry,SORT_REGULAR);
        //国家
        foreach ($rankCountry as $value){
            if(!empty($value['country'])){
                $rankCountryList[] = [
                    'en'=>$value['country'],
                    'cn'=>$this->countryList[$value['country']]
                ];
            }
        }
        //地域
        $regionUS = [
            ['en'=>'northeast', 'cn'=>'东北部'],
            ['en'=>'midwest',   'cn'=>'中西部'],
            ['en'=>'west',   'cn'=>'西部'],
            ['en'=>'south',   'cn'=>'南部'],
            ['en'=>'westCoast',   'cn'=>'西海岸']
        ];


        $countryRegionType = [
            'country'=>$rankCountryList,
            'region'=>$regionUS
        ];

        return $countryRegionType;
    }
    //国内综合排名 列表
    public function domesticList($rankOrg,$rankCountry,$region,$categoryList,$search,$page,$pageSize)
    {
        $rankList = $this->_em->createQueryBuilder()
            ->select('c.id,c.college_id,c.college_name_cn,c.college_name_en,c.rank')
            ->from('App:CollegeRank','c')
            ->andwhere('c.rank_org = :rank_org')
            ->setParameter('rank_org',$rankOrg)
            ->andWhere('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','comprehensive');

       //增加筛选条件

        //增加国家筛选条件
        if(!empty($rankCountry)){
            $rankList->andWhere('c.country = :country')
                ->setParameter('country',$rankCountry);
        }
        //增加地域筛选条件
        if(!empty($region)){
            $rankList ->andWhere('c.region in (:region)')
                ->setParameter('region',$region);
        }

        //学校列表  学校类型筛选
        if(!empty($categoryList)){
            $rankList ->andWhere('c.college_type in (:college_type)')
                ->setParameter('college_type',$categoryList);
        }

        //模糊条件查询
        if(!empty($search)){
            $rankList->andWhere('c.college_name_en like :search or c.college_name_cn like :search')
                ->setParameter('search','%'.$search.'%');
        }


        //总条数
        $totalNum = count($rankList->getQuery()->getResult(Query::HYDRATE_ARRAY));
        //分页
        $rankList = $rankList
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $domesticRankList = [];
        foreach ($rankList as $value){

            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';

            //判断校徽存不存在(判断已取消)
                $domesticRankList[] = [
                    'id' => $value['college_id'],
                    'name_cn' => $value['college_name_cn'],
                    'name_en' => $value['college_name_en'],
                    'crest' =>$crest,
                    'rank'  =>$value['rank']
                ];

        }

        $collegeList = [
            'total_num' => $totalNum,
            'data' => $domesticRankList
        ];

       return $collegeList;
    }





    //国内综合排名  学校类型
    public function domesticCollegeType()
    {
        $collegeType = [
            ['en'=>'comprehensive','cn'=>'综合性大学'],
            ['en'=>'liberalArt','cn'=>'文理学院'],
            ['en'=>'art','cn'=>'艺术院校']
        ];
        return $collegeType;
    }

    /**
     * 艺术类院校 国内综合排名
     */
    //艺术类院校国内综合排行 筛选条件 rank_org country
    public function artDomesticScreen()
    {
        $artRankScreen = $this->_em->createQueryBuilder()
            ->select('c.rank_org,c.country')
            ->from('App:CollegeRank','c')
            ->where('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','art')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $artRankScreen = array_unique($artRankScreen,SORT_REGULAR);

        foreach ($artRankScreen as $value){
            $artRankOrg[] = [
                'en'=>$value['rank_org'],
                'cn'=>$this->rankOrgList[$value['rank_org']]
            ];
            $artRankCountry[]=[
                'en'=>$value['country'],
                'cn'=>$this->countryList[$value['country']]
            ];
        }
        $artRankScreenList =[
            'rank_org' => $artRankOrg,
            'rank_country'=>$artRankCountry
        ];
        return $artRankScreenList;
    }

    //艺术类院校国内综合排行 列表
    public function artDomesticList($rankOrg,$rankCountry,$search,$page,$pageSize)
    {
        $rankList = $this->_em->createQueryBuilder()
            ->select('c.id,c.college_id,c.college_name_cn,c.college_name_en,c.rank')
            ->from('App:CollegeRank','c')
            ->andwhere('c.rank_org = :rank_org')
            ->setParameter('rank_org',$rankOrg)
            ->andWhere('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','domestic')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','art');
        //国家筛选条件
        if(!empty($rankCountry)){
            $rankList->andWhere('c.country = :country')
                ->setParameter('country',$rankCountry);
        }
        //模糊条件查询
        if(!empty($search)){
            $rankList->andWhere('c.college_name_en like :search or c.college_name_cn like :search')
                ->setParameter('search','%'.$search.'%');
        }

        $totalNum = count($rankList->getQuery()->getResult(Query::HYDRATE_ARRAY));

        $rankList = $rankList
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $artDomesticRankList = [];
        foreach ($rankList as $value){


            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
            //判断校徽存不存在(判断取消)
                $artDomesticRankList[] = [
                    'id' => $value['college_id'],
                    'name_cn' => $value['college_name_cn'],
                    'name_en' => $value['college_name_en'],
                    'crest' =>$crest,
                    'rank'  =>$value['rank']
                ];

        }

        $collegeList = [
            'total_num' => $totalNum,
            'data' => $artDomesticRankList
        ];

   return $collegeList;

    }


    /**
     * 艺术类院校 全球综合排行
     */
    //艺术类院校全球综合排行 rank_org 筛选类别
    public function artRankOrg()
    {
        $artRankOrg = $this->_em->createQueryBuilder()
                ->select('c.rank_org')
                ->from('App:CollegeRank','c')
                ->where('c.rank_scope = :rank_scope')
                ->setParameter('rank_scope','global')
                ->andWhere('c.rank_type = :rank_type')
                ->setParameter('rank_type','art')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $artRankOrg = array_unique($artRankOrg,SORT_REGULAR);
        foreach ($artRankOrg as $value){
            $artRankOrgList[] = [
                'en' => $value['rank_org'],
                'cn' => $this->rankOrgList[$value['rank_org']]
            ];
        }

       return $artRankOrgList;
    }

    //艺术类院校全球综合排行 rank_country
    public function artRankCountry()
    {
        $countryUS  = [
            'en' => 'US',
            'cn' => '美国'
        ];
        $countryGlobal =[
            'en' => 'global',
            'cn' => '全球'
        ];
        $rankCountry = [
            $countryUS,
            $countryGlobal
        ];
        return $rankCountry;
    }
    //艺术类院校全球综合排名 列表
    public function artGlobalRankList($rankOrg,$rankCountry,$search,$page,$pageSize)
    {
        $rankList = $this->_em->createQueryBuilder()
            ->select('c.id,c.college_id,c.college_name_cn,c.college_name_en,c.rank')
            ->from('App:CollegeRank','c')
            ->andwhere('c.rank_org = :rank_org')
            ->setParameter('rank_org',$rankOrg)
            ->andWhere('c.rank_scope = :rank_scope')
            ->setParameter('rank_scope','global')
            ->andWhere('c.rank_type = :rank_type')
            ->setParameter('rank_type','art');

        //增加国家筛选条件
        if($rankCountry !='global'){
            $rankList->andWhere('c.country = :country')
                ->setParameter('country',$rankCountry);
        }
        //模糊条件查询
        if(!empty($search)){
            $rankList->andWhere('c.college_name_en like :search or c.college_name_cn like :search')
                ->setParameter('search','%'.$search.'%');
        }


        $totalNum = count($rankList->getQuery()->getResult(Query::HYDRATE_ARRAY));

        $rankList = $rankList
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $artGlobalRankList = [];
        foreach ($rankList as $value){



            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
            //判断校徽存不存在
                $artGlobalRankList[] = [
                    'id' => $value['college_id'],
                    'name_cn' => $value['college_name_cn'],
                    'name_en' => $value['college_name_en'],
                    'crest' =>$crest,
                    'rank'  =>$value['rank']
                ];
        }

        $collegeList = [
            'total_num' => $totalNum,
            'data' => $artGlobalRankList
        ];

        return $collegeList;
    }

    /**
     * 全球综合排行
     */
    //全球综合排行 rank_org 筛选类别

    public function rankOrg()
    {
        $rankOrg = $this->_em->createQueryBuilder()
                ->select('c.rank_org')
                ->from('App:CollegeRank','c')
                ->where('c.rank_scope = :rank_scope')
                ->setParameter('rank_scope','global')
                ->andWhere('c.rank_type = :rank_type')
                ->setParameter('rank_type','comprehensive')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $rankOrg = array_unique($rankOrg,SORT_REGULAR);
        foreach ($rankOrg as $value){
            $rankOrgList[] = [
                'en' => $value['rank_org'],
                'cn' => $this->rankOrgList[$value['rank_org']],
                'rank_org_rank'=>$this->rankOrgRank[$value['rank_org']]
            ];
        }

        //根据排名机构权重进行排序

        $sort1 = array_column($rankOrgList,'rank_org_rank');

        array_multisort($sort1,SORT_ASC,$rankOrgList);

        $rankOrgList_1=[];
        foreach ($rankOrgList as $value){

            $rankOrgList_1[]=[
                'en' => $value['en'],
                'cn' => $value['cn']
            ];
        }



        return $rankOrgList_1;
    }
    //全球综合排行 country & region

    public function rankCountry()
    {
        $countryUS  = [
          'en' => 'US',
          'cn' => '美国'
        ];
        $countryGlobal =[
            'en' => 'global',
            'cn' => '全球'
        ];
        $regionUS = [
            ['en'=>'northeast', 'cn'=>'东北部'],
            ['en'=>'midwest',   'cn'=>'中西部'],
            ['en'=>'west',   'cn'=>'西部'],
            ['en'=>'south',   'cn'=>'南部'],
            ['en'=>'westCoast',   'cn'=>'西海岸']
        ];
        $regionGlobal=[];
        $rankCountry = [
           ['country'=>$countryUS, 'region' =>$regionUS],
           ['country'=>$countryGlobal,'region'=>$regionGlobal]
        ];
        return $rankCountry;
    }
    //全球综合排行 college_type
    public function collegeType()
    {
        $collegeType = [
            ['en'=>'comprehensive','cn'=>'综合性大学'],
            ['en'=>'liberalArt','cn'=>'文理学院'],
            ['en'=>'art','cn'=>'艺术院校']
        ];
        return $collegeType;
    }

    //全球综合排名列表
    public function globalRankList($rankOrg,$rankCountry,$region,$categoryList,$search,$page,$pageSize)
    {
        $rankList = $this->_em->createQueryBuilder()
                ->select('c.id,c.college_id,c.college_name_cn,c.college_name_en,c.rank')
                ->from('App:CollegeRank','c')
                ->andwhere('c.rank_org = :rank_org')
                ->setParameter('rank_org',$rankOrg)
                ->andWhere('c.rank_scope = :rank_scope')
                ->setParameter('rank_scope','global')
                ->andWhere('c.rank_type = :rank_type')
                ->setParameter('rank_type','comprehensive')
                ->andWhere('c.rank <= :rank')
                ->setParameter('rank',500);

        //增加国家筛选条件
        if($rankCountry !='global'){
           $rankList->andWhere('c.country = :country')
                ->setParameter('country',$rankCountry);
        }


        //增加地域筛选条件
        if(!empty($region)){
            $rankList ->andWhere('c.region in (:region)')
                    ->setParameter('region',$region);
        }

        //学校列表  学校类型筛选
        if(!empty($categoryList)){
            $rankList ->andWhere('c.college_type in (:college_type)')
                ->setParameter('college_type',$categoryList);
        }

        //模糊条件查询
        if(!empty($search)){
            $rankList->andWhere('c.college_name_en like :search or c.college_name_cn like :search')
                    ->setParameter('search','%'.$search.'%');
        }


        $totalNum = count($rankList->getQuery()->getResult(Query::HYDRATE_ARRAY));

        $rankList = $rankList
                ->setFirstResult(($page - 1) * $pageSize)
                ->setMaxResults($pageSize)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $globalRankList = [];
        foreach ($rankList as $value){


            $nameEn = str_replace(' ','',$value['college_name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
              //判断校徽存不存在(判断取消)

                $globalRankList[] = [
                    'id' => $value['college_id'],
                    'name_cn' => $value['college_name_cn'],
                    'name_en' => $value['college_name_en'],
                    'crest' =>$crest,
                    'rank'  =>$value['rank']
                ];
        }
        $collegeList = [
            'total_num' => $totalNum,
            'data' => $globalRankList
        ];
        return $collegeList;
    }
















    // /**
    //  * @return CollegeRank[] Returns an array of CollegeRank objects
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
    public function findOneBySomeField($value): ?CollegeRank
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
