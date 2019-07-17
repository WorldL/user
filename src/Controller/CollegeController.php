<?php

namespace App\Controller;

use App\Entity\College;
use App\Entity\CollegeArt;
use App\Entity\CollegeCrime;
use App\Entity\CollegeGraduate;
use App\Entity\CollegeRace;
use App\Entity\CollegeRank;
use App\Entity\CollegeScore;
use App\Entity\CollegeUndergraduate;
use App\Entity\FamousAlumni;
use App\Entity\Major;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

class CollegeController extends AbstractController
{
    //实体
    private $em;
    //college 表
    private $collegeRepo;
    //college_score 表
    private $collegeScoreRepo;
    //college_rank 表
    private $collegeRankRepo;
    //college_race 表
    private $collegeRaceRepo;
    //college_crime 表
    private $collegeCrimeRepo;
    //college_undergraduate 表
    private $collegeUndRepo;
    //college_graduate 表
    private $collegeGraRepo;
    //college_art 表
    private $collegeArtRepo;
    //famous_alumni 表
    private $famousAlumniRepo;
    //major biao
    private $majorRepo;
    //上学费用列表
    private $feeList=[
        'total_fee',//总花费
        'education_fee',//学费
        'boarding_fee',//食宿费
        'living_fee',//其他费用
        'book_fee'//书本费
    ];
    //上学费用列表
    private $feeListDic=[
        'total_fee'=>'总花费',
        'education_fee'=>'学费',
        'boarding_fee'=>'食宿费',
        'living_fee'=>'其他费用',
        'book_fee'=>'书本费'
    ];
    //学校成绩要求列表
    private $requirements=[
        'toefl_score',//托福
        'ielts_score',//雅思
        'sat_score',//SAT
        'sat2_score',//SAT2
        'act_score',//ACT
        'gpa',//GPA
        'gre_score',//GRE
        'gmat_score'//GMAT
    ];
    //学校成绩要求列表
    private $requirementsList=[
        'toefl_score'=>'托福',
        'ielts_score'=>'雅思',
        'sat_score'=>'SAT',
        'sat2_score'=>'SAT2',
        'act_score'=>'ACT',
        'gpa'=>'GPA',
        'gre_score'=>'GRE',
        'gmat_score'=>'GMAT'
    ];
    //学校申请时间列表
    private $apply = [
        'RD',
        'ED',
        'ED1',
        'ED2',
        'EA',
        'REA'
    ];
    //学校列表筛选条件
    private $collegeCountryList = [
        '美国' => 'US'
    ];
    //学校列表筛选条件英转汉
    private $collegeCountryListETC = [
        'US' => '美国'
    ];
    //学校地域筛选列表
    private $collegeRegionList = [
        '东北部' => 'northeast',
        '中西部' => 'midwest',
        '西部' => 'west',
        '南部' => 'south',
        '西海岸' => 'westCoast'
    ];
    //学校地域筛选列表英转汉
    private $collegeRegionListETC = [
        'northeast' => '东北部',
        'midwest' => '中西部',
        'west' => '西部',
        'south' => '南部',
        'westCoast' => '西海岸'
    ];
    //学校类型筛选条件
    private $collegeCategoryList = [

    ];
    //学校类型筛选条件英转汉
    private $collegeCategoryListETC = [
        'comprehensive' => '综合性大学',
        'liberalArt' => '文理学院',
        'art' => '艺术院校'
    ];
    //学校排名机构排序
    private $rankOrgRank = [
        'USNEWS'=>1,
        'QS'=>2,
        'forbes'=>3,
        'thames'=>4,
        'shjd'=>5
    ];
    //学校列表ID
    private $collegeId;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        $this->em = $entityManager;
        $this->collegeRepo = $entityManager->getRepository(College::class);
        $this->collegeScoreRepo = $entityManager->getRepository(CollegeScore::class);
        $this->serializer = $serializer;
        $this->collegeRankRepo = $entityManager->getRepository(CollegeRank::class);
        $this->collegeRaceRepo = $entityManager->getRepository(CollegeRace::class);
        $this->collegeCrimeRepo = $entityManager->getRepository(CollegeCrime::class);
        $this->collegeUndRepo = $entityManager->getRepository(CollegeUndergraduate::class);
        $this->collegeGraRepo = $entityManager->getRepository(CollegeGraduate::class);
        $this->collegeArtRepo = $entityManager->getRepository(CollegeArt::class);
        $this->famousAlumniRepo = $entityManager->getRepository(FamousAlumni::class);
        $this->majorRepo = $entityManager->getRepository(Major::class);

    }

    /**
     * @Route("/college", methods={"POST"})
     * @SWG\Response(response =200 , description = "获取学校列表")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="country", type="string", example="US"),
     *          @SWG\Property(property="page", type="int", example="1"),
     *          @SWG\Property(property="pageSize", type="int", example="10"),
     *          @SWG\Property(property="search", type="string", example="哈佛"),
     *          @SWG\Property(property="tuition", type="object",
     *                  @SWG\Property(property="min", type="string", example="10"),
     *                  @SWG\Property(property="max", type="string", example="45"),
     *          ),
     *          @SWG\Property(property="toefl", type="object",
     *                  @SWG\Property(property="min", type="string", example="10"),
     *                  @SWG\Property(property="max", type="string", example="76"),
     *          ),
     *          @SWG\Property(property="ielts", type="object",
     *                  @SWG\Property(property="min", type="string", example="2"),
     *                  @SWG\Property(property="max", type="string", example="6.5"),
     *          ),
     *          @SWG\Property(property="category", type="array",
     *              @SWG\Items(type="string",
     *                 example="comprehensive"
     *              )
     *          ),
     *          @SWG\Property(property="region", type="array",
     *              @SWG\Items(type="string",
     *                  example="northeast"
     *              )
     *          ),
     *
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function index(Request $r)
    {

        //获取学校库列表(国家判断)
        if (empty($r->get('country'))){
            $country = 'US';
        }else{
            $country = $r->get('country');
        }
//        var_dump($r->get('region'));
//        die;
        //获取学校库列表 (地域判断)
        if (empty($r->get('region'))){
            $regionList = [];
        }else{
            $regionList = $r->get('region');
            if(count($regionList) == 5){
                $regionList = [];
            }
        }


        //获取学校库列表 学费判断
        if (empty($r->get('tuition'))){
            $tuition = [];
        }else{
            $tuition = $r->get('tuition');
        }


        //获取学校库列表 托福成绩要求
        if (empty($r->get('toefl'))){
            $toefl = [];
        }else{
            $toefl = $r->get('toefl');
        }

        //获取学校库列表 雅思成绩要求
        if (empty($r->get('ielts'))){
            $ielts = [];
        }else{
            $ielts = $r->get('ielts');
        }


        //获取学校库列表 学校类型筛选
        if (empty($r->get('category'))){
            $categoryList = [];
        }else{
            $categoryList = $r->get('category');
            if(count($categoryList) == 3){
                $categoryList = [];
            }
        }
        //获取学校列表  页数及每页显示的条数
        if (empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
         if(empty($r->get('pageSize'))){
             $pageSize = 1000;
         }else{
             $pageSize = $r->get('pageSize');
         }

         //获取搜索条件
        if (empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        $college = $this->collegeRepo->list($country,$regionList,$tuition,$toefl,$ielts,$categoryList,$page,$pageSize,$search);


         if (empty($college['data'])){
             $collegeList = [];
         }else{
             foreach ($college['data'] as $key=>$value){

                 $nameEn = str_replace(' ','',$value['name_en']);

                 $schoolBadge = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
                 $collegeList[] = [
                     'id' => $value['id'],
                     'name_cn' => $value['name_cn'],
                     'name_en' => $value['name_en'],
                     'address' => $value['address'],
                     'crest' => $schoolBadge
                 ];
             }
         }

        return new JsonResponse($collegeList);
    }

    /**
     * @Route("/college/screen", methods= {"POST"})
     * @SWG\Response(response =200 , description = "学校列表筛选条件")
     * @SWG\Tag(name="college")
     */
    public function screen()
    {
        //获取列表
        $college = $this->collegeRepo->screen();
        foreach ($college as $key=>$value){
            $country[] = $value['country'];
            $region[] = $value['region'];
            $category[] = $value['category'];
        }
        //国家列表
        //增加数据后临时处理
        $country = 'US';

            $countryList[] = [
                'en' => $country,
                'cn' => $this->collegeCountryListETC[$country]
            ];



//            var_dump($countryList);
//die;
        //没有增加数据之前的代码
//        $country = array_unique($country);
//        foreach ($country as $value){
//            $countryList[] = [
//                'en' => $value,
//                'cn' => $this->collegeCountryListETC[$value]
//            ];
//        }
        //地域列表
        $region = array_unique($region);

        foreach ($region as $value){

            if(!empty($value)){
                $regionList[] = [
                    'en' => $value,
                    'cn' => $this->collegeRegionListETC[$value]
                ];
            }

        }
        //学校类型
        $category = array_unique($category);
        foreach ($category as $value){
            if(!empty($value)){
                $categoryList[] = [
                    'en'=> $value,
                    'cn'=> $this->collegeCategoryListETC[$value]
                ];
            }
        }

        $screenList = [
            'country' => $countryList,
            'region' => $regionList,
            'category' => $categoryList
        ];

//        var_dump($screenList);
//        die;
        return new JsonResponse($screenList);
    }


    /**
     * 学校详情一级页面
     * @Route("/college/first",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校分数及校训")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function first(Request $r)
    {


        //获取学校id
        $id = $r->get('id');
        //获取college表数据
        if(!empty($id)){
            $college = $this->collegeRepo->findOneBy(['id'=>$id]);
        }


        if(empty($college)){
           return new JsonResponse([]);
        }
        $college = $this->serializer->toArray($college,SerializationContext::create()->setSerializeNull(true));

//       if($college['is_show'] == 'NO' || $college[''])

        //查询性价比
        $collegeScore = $this->collegeScoreRepo->findOneBy(['college_id'=>$id]);




        if (empty($collegeScore)){

            $collegeScore = [
                'college_id'=>$id,
                'cost'=>null,
                'academics'=>null,
                'application'=>null,
                'safety'=>null,
                'diversity'=>null,
                'name_cn'=> $college['name_cn'],
                'name_en'=> $college['name_en'],
                'school_motto'=>null,
                'is_show'=>$college['is_show']
            ];

            $nameEn = str_replace(' ','',$collegeScore['name_en']);
            $schoolBadge = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
            $collegeScore['background'] = 'http://cdn.xiaohailang.net/common/school_bg/'.$nameEn.'.jpg';
            $collegeScore['school_badge'] = $schoolBadge;

        }else{

            $collegeScore = $this->serializer->toArray($collegeScore);
            $collegeScore['name_cn'] = $college['name_cn'];
            $collegeScore['name_en'] = $college['name_en'];
            $collegeScore['is_show'] = $college['is_show'];
            if(!array_key_exists('school_motto',$collegeScore)){
                $collegeScore['school_motto'] = null;
            }
            $nameEn = str_replace(' ','',$collegeScore['name_en']);
            $schoolBadge = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';
            $collegeScore['background'] = 'http://cdn.xiaohailang.net/common/school_bg/'.$nameEn.'.jpg';
            $collegeScore['school_badge'] = $schoolBadge;
        }




        return new JsonResponse($collegeScore);
    }
    /**
     * 学校详情界面 知名校友
     * @Route("/college/alumnus",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function alumnus(Request $r)
    {
        //获取id
        $id = $r ->get('id');
        if(!empty($id)){
            $college = $this->collegeRepo->findOneBy(['id'=>$id]);
        }
        if(empty($college)){
            return new JsonResponse([]);
        }

        $college = $this->serializer->toArray($college);

        $alumnus = $this->famousAlumniRepo->findBy(['college_id'=>$id]);
        if(empty($alumnus)){
            $alumnusList = [];
        }else{
            $alumnus = $this->serializer->toArray($alumnus);

            $collegeName = str_replace(' ','',$college['name_en']);

            $alumnusPhoto ='http://cdn.xiaohailang.net/common/famousAlumni/'.$collegeName.'/';

            foreach ($alumnus as $value){
                $nameEn = str_replace(' ','',$value['name_en']);
                $alumnusList[] =[
                    'name' => $value['name_cn'],
                    'intro' => $value['intro'],
                    'photo' => $alumnusPhoto.$nameEn.'.png'
                ];
            }
        }

        return new JsonResponse($alumnusList);
    }


    /**
     * 学校详情界面 开始到学生数量(无知名校友)
     * @Route("/college/details",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function details(Request $r)
    {
        //获取学校id
        $id = $r->get('id');

        //获取college表数据
        if(!empty($id)){
            $college = $this->collegeRepo->findOneBy(['id'=>$id]);
        }

        if(empty($college)){
            return new JsonResponse([]);
        }

        $college = $this->serializer->toArray($college);

//        var_dump($college);
//        die;
        //男女比例 $proportion
        if(empty($college['male_female_ratio'])){
            $proportion = [];
        }else{
            $male_femal_ratio = $college['male_female_ratio'];

            $male_femal_ratio = explode(":",$male_femal_ratio);

            $proportion['male'] = $male_femal_ratio[0]."%";
            $proportion['femal'] = $male_femal_ratio[1]."%";
        }


//        //师生比例 $facultyStudentRatio
//        if(empty($college['faculty_student_ratio'])){
//            $facultyStudentRatio = [];
//        }else{
//            $facultyStudentRatio = $college['faculty_student_ratio'];
//        }




        //学生数量 $studentNum
          $studentNum=[
              'total_amount_students' => $college['total_amount_students'],
              'total_amount_und' => $college['total_amount_undergraduate'],
              'total_amount_gra' => $college['total_amount_graduate']
          ];





        //招生数据
        $studentNum['total_amount_gra'] = $college['total_amount_graduate'];



        //获取学校的学术能力
        $collegeRank = $this->collegeRankRepo->findBy(['college_id'=>$id]);


        $collegeRank = $this->serializer->toArray($collegeRank);

        //国际排名
        $worldRankings = [];
        foreach ($collegeRank as $key=>$value){

            if($value['rank_scope']=='global' && $value['rank_type'] == 'comprehensive'){

                if(strlen($value['rank'])<=3){
                    $worldRankings[$key]=[
                        'rank_org' => $value['rank_org'],
                        'rank' => $value['rank'],
                        'title' => '国际排名',
                        'rank_org_rank'=>$this->rankOrgRank[$value['rank_org']]
                    ];
                }

            }
        }


        $sort = array_column($worldRankings,'rank');

        $sort1 = array_column($worldRankings,'rank_org_rank');

        array_multisort($sort,SORT_ASC,$sort1,SORT_ASC,$worldRankings);


        if(count($worldRankings)>3){
            $worldRankings = array_slice($worldRankings,0,3);
        }

        $worldRankings_1=[];
        foreach ($worldRankings as $value){

            $worldRankings_1[]=[
                'rank_org' => $value['rank_org'],
                'rank' => $value['rank'],
                'title' => '国际排名',
            ];
        }



        //国内排名
        $guonei = [];
        foreach ($collegeRank as $key=>$value){

            if($value['rank_scope']=='domestic' && $value['rank_type'] == 'comprehensive'){

                $guonei[] = $value;
            }
        }

        $localRankings = [];
        foreach ($guonei as $key=>$value){
            if(strlen($value['rank'])<=3) {
                $localRankings[$key] = [
                    'rank_org' => $value['rank_org'],
                    'rank' => $value['rank'],
                    'title' => '国内排名',
                    'rank_org_rank' => $this->rankOrgRank[$value['rank_org']]
                ];
            }
        }

        $sort = array_column($localRankings,'rank');
        $sort1 = array_column($localRankings,'rank_org_rank');
        array_multisort($sort,SORT_ASC,$sort1,SORT_ASC,$localRankings);

        $localRankings_1=[];
        foreach ($localRankings as $value){
            $localRankings_1[]=[
                'rank_org' => $value['rank_org'],
                'rank' => $value['rank'],
                'title' => '国内排名',
            ];
        }
        //优势学科
//        var_dump($college['pro_subject']);
        $proSubject = explode(',',$college['pro_subject']);

        foreach ($proSubject as $value){

            if(empty($value)){
                $proSubjectMajor =[];
            }else{
                $m = str_replace('专业','',$value);
                $majorImage = $this->majorRepo->findOneBy(['name_cn'=>$m]);
                if(!empty($majorImage)){
                    $majorImage = $this->serializer->toArray($majorImage);
                }
                if($majorImage==null){
                    $proSubjectMajor[] =[
                        'id' => null,
                        'major'=>$value
                    ];
                }else{
                    $proSubjectMajor[] =[
                        'id' =>$majorImage['id'],
                        'major'=>$value
                    ];
                }
            }
        }


        if(empty($college['faculty_student_ratio'])){
            $college['faculty_student_ratio'] = null;
        }
        $collegeDetails=[
            'name_cn' => $college['name_cn'],
            'introduction'=> $college['introduction'],
            'world_rankings'=> $worldRankings_1,
            'local_rankings'=> $localRankings_1,
            'pro_subject' => $proSubjectMajor,
            'faculty_student_ratio' => $college['faculty_student_ratio'],
            'male_female_ratio'=> $proportion,

            'student_num' => $studentNum
        ];


        return new JsonResponse($collegeDetails);
    }
    /**
     * 学校详情界面 种族分布
     * @Route("/college/race",methods = {"POST"},)
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function race(Request $r)
    {
        //获取学校id
        $id = $r->get('id');

        //获取college表数据
        if(!empty($id)){
            $collegeRace = $this->collegeRaceRepo->findBy(['college_id'=>$id]);
        }
        if(empty($collegeRace)){
            return new JsonResponse([]);
        }
        $collegeRace = $this->serializer->toArray($collegeRace);
        $gra=[];
        $und=[];
        foreach ($collegeRace as $key=>$value){
            if($value['diploma'] == '研究生'){
                $gra=$value;
            }else{
                $und=$value;
            }
        }
        $collegeRace=[
            'gra' => $gra,
            'und' => $und
        ];
      return new JsonResponse($collegeRace);
    }
    /**
     * 学校详情界面 安全程度
     * @Route("/college/crime",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function crime(Request $r)
    {
        //获取学校id
        $id = $r->get('id');

        //获取college_crime表数据
        if(!empty($id)){
            $collegeCrime = $this->collegeCrimeRepo->findBy(['college_id'=>$id]);
        }

        if(empty($collegeCrime)){
            return new JsonResponse([]);
        }
        $collegeCri = $this->serializer->toArray($collegeCrime);

        $collegeCrime=[];
        foreach ($collegeCri as $key=>$value){
            $collegeCrime[] = [
                'year'   =>$value['year'],
                '持枪记过'=>$value['gunmen_recorded'],
                '毒品记过'=>$value['drug_recorded'],
                '酗酒记过'=>$value['drunk_recorded'],
                '约会犯罪'=>$value['dating_crime'],
                '跟踪'   =>$value['track'],
                '强奸'   =>$value['rape'],
                '袭击'   =>$value['assault'],
                '性扰'   =>$value['sexual_harassment'],
                '盗窃'   =>$value['steal'],
                '偷车'   =>$value['vehicle_steal']
            ];
        }

        $collegeCrime = array_slice($collegeCrime,0,4);


        return new JsonResponse($collegeCrime);
    }
    /**
     * 学校详情界面 成绩要求&重要日期
     * @Route("/college/grade",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function grade(Request $r)
    {
        //获取学校id
        $id = $r->get('id');
        //获取college_und表数据
        if(!empty($id)){
            $collegeUnd = $this->collegeUndRepo->findOneBy(['college_id'=>$id]);
        }
        //$undScore  本科生成绩要求
        if(empty($collegeUnd)){
            $undScore= [];
        }else{
            $collegeUnd = $this->serializer->toArray($collegeUnd);
            foreach ($collegeUnd as $key=>$value){
//            提取不为空的
                if(in_array($key,$this->requirements) && $value){

                    $undScore[]=[
                        'name' => $this->requirementsList[$key],
                        'requirements' => $value
                    ];
                }
            }
        }
        if(!isset($undScore)){
            $undScore=[];
        }


        //本科生 申请时间和录取时间
        //本科生申请时间



        if(empty($collegeUnd['apply_deadline'])){
            $undApply = [];
        }else{
            $undApplyDeadline = explode(';',$collegeUnd['apply_deadline']);

//            var_dump($undApplyDeadline);
//            die;
           if(count($undApplyDeadline) > 1){
               foreach ($undApplyDeadline as $key=>$value){
                   $value = explode(':',$value);
                   if(count($value)>1){
                       $undApply []= [
                           'applyType'=>str_replace(array("/r/n", "/r", "\n"), '', $value[0]),
                           'date' => $value[1]
                       ];
                   }
               }
           }else{
               $undApply=[];
           }

        }



        //本科生 录取时间
        if(empty($collegeUnd['offer_distribution_date'])){
            $undOffer = [];
        }else{
            $undOfferDate = explode(';',$collegeUnd['offer_distribution_date']);

            if(count($undOfferDate) >1){
                foreach ($undOfferDate as $key=>$value){
                    $value = explode(':',$value);
                    if(count($value)>1){
                        $undOffer[] = [
                            'offerType' => $value[0],
                            'date' => $value[1]
                        ];
                    }
                }
            }else{
                $undOffer = [];
            }

        }

        $gradeData['und'] = [
            'score' => $undScore,
            'apply' => $undApply,
            'offer'=> $undOffer
        ];


        //研究生 成绩要求
        //获取college_gradate 表数据
        if(!empty($id)){
            $collegeGra = $this->collegeGraRepo->findOneBy(['college_id'=>$id]);
        }
        //graScore 成绩要求
        if(empty($collegeGra)){
            $graScore = [];
        }else{
            $collegeGra = $this->serializer->toArray($collegeGra);
            foreach ($collegeGra as $key=>$value){
                //            提取不为空的
                if(in_array($key,$this->requirements) && $value){
                    $graScore[]=[
                        'name' => $this->requirementsList[$key],
                        'requirements' => $value
                    ];
                }
            }
        }
        if(!isset($graScore)){
            $graScore=[];
        }



        //研究生 申请时间录取时间
        if(empty($collegeGra['apply_deadline'])){
            $graApply = [];
        }else{
            $graApply[]=[
                'name' => '申请截止时间',
                'date' => $collegeGra['apply_deadline']
            ];
        }
        //研究生 录取时间
        $graOffer = [];
        $gradeData['gra'] = [
            'score'=> $graScore,
            'apply'=> $graApply,
            'offer'=> $graOffer
        ];
        //艺术生
        if(!empty($id)){
            $collegeArt = $this->collegeArtRepo->findOneBy(['college_id'=>$id]);
        }
        //$artScore 成绩要求
        $artSore = [];
        //艺术生录取时间
        $artOffer = [];
        //艺术生 申请截止时间
        if(empty($collegeArt)){
            $artApply = [];
        }else{
            $collegeArt = $this->serializer->toArray($collegeArt);
            if(empty($collegeArt['apply_deadline'])){
                $artApply = [];
            }else{
                $artApply[] = [
                    'name' => '申请截止时间',
                    'date' => $collegeArt['apply_deadline']
                ];
            }
        }
        $gradeData['art'] = [
            'score' => $artSore,
            'apply' => $artApply,
            'offer' => $artOffer
        ];
        return new JsonResponse($gradeData);
    }
    /**
     * 学校详情界面 招生数据&性比价&就业率&毕业起薪
     * @Route("/college/price",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function price(Request $r)
    {

        //获取学校id
        $id = $r->get('id');
        //招生数据
        $college = $this->collegeRepo->findOneBy(['id'=>$id]);


        if(empty($college)){
            $enrollmentData=[];
        }else{
            //解决 字段是空时  直接过滤掉的问题
            $college = $this->serializer->toArray($college,SerializationContext::create()->setSerializeNull(true));

            $enrollmentData=[
                'applications' => $college['applications'],
                'enrollment' => $college['enrollment'],
                'actualEnrollment' => $college['actual_enrollment']
            ];

        }

        //获取college_und表数据
        $collegeUnd = $this->collegeUndRepo->findOneBy(['college_id'=>$id]);

        //学校本科费用
        $undFee=[];
        if(empty($collegeUnd)){
           $undFee =[];
        }else{
            $collegeUnd = $this->serializer->toArray($collegeUnd,SerializationContext::create()->setSerializeNull(true));

//            'apply_fee'=>'申请费',
            //获取费用不为空的本科生费用
            foreach ($collegeUnd as $key=>$value){
                //提取费用不为空的
                if(in_array($key,$this->feeList) && $value){
                    $undFee['fee'][]=[
                        'name'=>$this->feeListDic[$key],
                        'fee'=>$value
                    ];
                }
            }
            $undFee['applyFee']= $collegeUnd['apply_fee'];
            $undFee['employment'] = $college['employment'];
            $undFee['startingSalary'] = $college['starting_salary'];
        }


        $collegeGra =$this->collegeGraRepo->findOneBy(['college_id'=>$id]);
        //学校研究生费用
        $graFee=[];
        if(empty($collegeGra)){
            $graFee =[];
        }else{
            $collegeGra = $this->serializer->toArray($collegeGra);
            //获取费用不为空的研究生费用
            foreach ($collegeGra as $key=>$value){
                //提取费用不为空的
                if(in_array($key,$this->feeList) && $value){
                    $graFee['fee'][] = [
                        'name' => $this->feeListDic[$key],
                        'fee'  => $value
                    ];
                }
            }
            if(isset($collegeGra['apply_fee']) && !empty($collegeGra['apply_fee'])){
                $graFee['applyFee'] =$collegeGra['apply_fee'];
            }else{
                $graFee['applyFee'] =null;
            }
            $graFee['employment'] = null;
            $graFee['startingSalary'] = null;
        }
         $collegeArt = $this->collegeArtRepo->findOneBy(['college_id'=>$id]);
        //学校艺术生费用
        $artFee =[];
         if(empty($collegeArt)){
             $artFee=[];
         }else{
             $collegeArt = $this->serializer->toArray($collegeArt);

             //获取费用不为空的研究生费用
             foreach($collegeArt as $key=>$value){
                 //提取费用不为空
                 if(in_array($key,$this->feeList) && $value){
                     $artFee['fee'][]=[
                         'name'=>$this->feeListDic[$key],
                         'fee' =>$value
                     ];
                 }
             }
             if(isset($collegeArt['apply_fee'])){
                 $artFee['applyFee'] =$collegeArt['apply_fee'];
             }else{
                 $artFee['applyFee'] =null;
             }
             $artFee['employment'] = null;
             $artFee['startingSalary'] = null;

         }

         $fee=[
           'enrollmentData' => $enrollmentData,
           'undFee'=>$undFee,
           'graFee'=>$graFee,
           'artFee'=>$artFee
         ];

        return new JsonResponse($fee);
    }
    /**
     * 学校详情界面 三级页面 招生信息
     * @Route("/college/recruit",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取学校详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="college")
     */
    public function recruit(Request $r)
    {
        $id = $r->get('id');
        //获取college 信息
        $college = $this->collegeRepo->findOneBy(['id'=>$id]);
        if(empty($college)){
            $introduction = [];
        }else{
            $college = $this->serializer->toArray($college);
            $register = $college['register_office_info'];
            $register = str_replace("\n", "<br />",$register);
            $introduction = [
                'schoolintroduction' => $college['introduction'],
                'establishDate' => $college['establish_date'],
                'schoolType' => $college['type'],
                'registerOfficeInfo' => $register
            ];
//            var_dump($college['register_office_info']);
        }
       return new JsonResponse($introduction);
    }
}
