<?php

namespace App\Controller;

use App\Entity\MajorInfo;
use App\Entity\USMajorInfo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Major;

class MajorController extends AbstractController
{
    /**
     * @var \App\Repository\MajorRepository $majorRepo
     */
    private $majorRepo;

    /**
     * @var \JMS\Serializer\Serializer $serializer
     */
    private $serializer;
    /**
     * @var \App\Repository\USMajorInfoRepository $usMajorInfoRepo
     */
    private $usMajorInfoRepo;

    //个人信息  教育经历中 专业选择
    private $majorInfoRepo;

    //国家列表
    private $country=[
        'US'=>'美国'
    ];
    //学位列表
    private $education=[
        'und'=>'学士',
        'gra'=>'硕士'

    ];
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->majorRepo = $entityManager->getRepository(Major::class);
        $this->usMajorInfoRepo = $entityManager->getRepository(USMajorInfo::class);
        $this->majorInfoRepo = $entityManager->getRepository(MajorInfo::class);
        $this->serializer = $serializer;
    }

    /**
     * 专业库 列表页面
     * @Route("/major", methods={"POST"})
     * @SWG\Response(response=200, description="专业列表")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *      @SWG\Property(property="search", type="string", example="航空"),
     *      )
     * )
     * @swg\tag(name="major")
     */

    public function index(Request $r)
    {
        //获取搜索条件
        if(empty($r->get('search'))){
            $search ='';
        }else{
            $search = $r->get('search');
        }

        $list = $this->majorRepo->first($search);

        return new JsonResponse($list);
    }
    /**
     * 专业详情
     * @Route("/major/details",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取专业详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="major")
     */
    public function details(Request $r)
    {
        $id = $r->get('id');
        $details = $this->majorRepo->details($id);
        if(empty($details)){
            $details = [];
        }
        return new JsonResponse($details);
    }
    /**
     * 专业相关排名
     * @Route("/major/rank",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取相关专业排名")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="major")
     */
    public function rank(Request $r)
    {
        $id = $r->get('id');

        //查询usmajor_info 表查看 相关专业排名

        $relevant = $this->usMajorInfoRepo->findBy(['major_id'=>$id]);

        $relevant = $this->serializer->toArray($relevant);

        if(empty($relevant)){
            $majorList =[];
        }else{

        foreach ($relevant as $value){
            //国家
            $country = [
              'en'=>$value['country'],
              'cn'=>$this->country[$value['country']]
            ];
            //教育
            $education = [
                'en'=>$value['education'],
                'cn'=>$this->education[$value['education']]
            ];
            //排行类别
            $rankCategory = $value['rank_category'];
            //专业名称
            $majorName = $value['major_name'];
            //专业类别
            $majorCategory = $value['major_category'];


            $major =[
              'id'=>$value['id'],
              'major'=>$majorName
            ];

            $major1 = [
                'name' =>$majorCategory,
                'value'=>$major
            ];

//            var_dump($major1);
            //专业所属教育类型
            $majorEducation= $value['education'];

            $majorList[] = [
                'year'=>'2019',
                'country'=>$country,
                'education'=>$education,
                'rank_category'=>$rankCategory,
                "$majorEducation"=> $major1
            ];
        }
        }
        return new JsonResponse($majorList);
    }


    /**
     * 专业列表
     * @Route("/major/list", methods={"POST"})
     * @SWG\Response(response=200, description="专业列表")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *      )
     * )
     * @swg\tag(name="major")
     */
    public function list(Request $r)
    {

        $list = $this->majorInfoRepo->list();

        return new JsonResponse($list);

    }

}
