<?php

namespace App\Controller;

use App\Entity\CollegeRank;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class CollegeRankController extends AbstractController
{

    private $em;
    private $serializer;
    //CollegeRank 表
    private $collegeRankRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        $this->em = $entityManager;
        $this->serializer = $serializer;
        $this->collegeRankRepo = $entityManager->getRepository(CollegeRank::class);
    }

    /**
     * 国内文理学院排名 筛选条件
     * @Route("/domestic/liberalArt-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取筛选条件")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */

    public function liberalArtScreen()
    {
        //地域筛选条件

        //地域
        $regionUS = [
            ['en' => 'northeast', 'cn' => '东北部'],
            ['en' => 'midwest', 'cn' => '中西部'],
            ['en' => 'west', 'cn' => '西部'],
            ['en' => 'south', 'cn' => '南部'],
            ['en' => 'westCoast', 'cn' => '西海岸']
        ];

        return new JsonResponse($regionUS);

    }

    /**
     * 国内文理学院排名 列表
     * @Route("/domestic/liberalArt-list",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取排行列表")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *        @SWG\Property(property="page", type="int", example="1"),
     *        @SWG\Property(property="pageSize", type="int", example="10"),
     *        @SWG\Property(property="search", type="string", example="哈佛"),
     *        @SWG\Property(property="region", type="array",
     *              @SWG\Items(type="string",
     *                  example="northeast"
     *              )
     *          ),
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function liberalArtList(Request $r)
    {
        //地域筛选条件
        if (empty($r->get('region'))){
            $regionList = [];
        }else{
            $regionList = $r->get('region');
            if (count($regionList) == 5){
                    $regionList = [];
                }
        }
        //分页
        //获取学校列表  页数及每页显示的条数
        if (empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }

        if (empty($r->get('pageSize'))){
            $pageSize = 10;
        }else{
            $pageSize = $r->get('pageSize');
        }

        //获取搜索条件
        if (empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        $liberalArtList = $this->collegeRankRepo->liberalArtList($regionList,$search,$page,$pageSize);

        return new JsonResponse($liberalArtList);
    }



    /**
     * 国内综合排名 筛选条件
     * @Route("/domestic/domestic-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function domesticScreen()
    {
        //排行类别
        $rankOrg = $this->collegeRankRepo->domesticRankOrg();
        //国家  地域
        $rankCountry = $this->collegeRankRepo->domesticRankCountry();
        //学校类型
        $collegeType = $this->collegeRankRepo->domesticCollegeType();

        $domesticScreen = [
            'rank_org'=>$rankOrg,
            'rank_country'=>$rankCountry['country'],
            'college_type'=>$collegeType,
            'region'=>$rankCountry['region']
        ];

        return new JsonResponse($domesticScreen);

    }
    /**
     * 国内综合排名 列表
     * @Route("/domestic/domestic-list",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取排行列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *        @SWG\Property(property="rank_org", type="string", example="forbes"),
     *        @SWG\Property(property="rank_country", type="string", example="US"),
     *        @SWG\Property(property="page", type="int", example="1"),
     *        @SWG\Property(property="pageSize", type="int", example="10"),
     *        @SWG\Property(property="search", type="string", example="哈佛"),
     *        @SWG\Property(property="region", type="array",
     *              @SWG\Items(type="string",
     *                  example="northeast"
     *              )
     *          ),
     *        @SWG\Property(property="category", type="array",
     *              @SWG\Items(type="string",
     *                 example="comprehensive"
     *              )
     *          ),
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function domesticList(Request $r)
    {
        //排行类别
        if(empty($r->get('rank_org'))){
            $rankOrg = 'forbes';
        }else{
            $rankOrg = $r->get('rank_org');
        }
        //国家筛选
        if(empty($r->get('rank_country'))){
            $rankCountry = 'US';
        }else{
            $rankCountry = $r->get('rank_country');
        }
        //地域筛选条件
        if(empty($r->get('region'))){
            $regionList = [];
        }else{
            $regionList = $r->get('region');
            if(count($regionList) == 5){
                $regionList = [];
            }
        }

        // 学校类型筛选
        if(empty($r->get('category'))){
            $categoryList = [];
        }else{
            $categoryList = $r->get('category');
            if(count($categoryList) == 3){
                $categoryList = [];
            }
        }

        //获取搜索条件
        if(empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        //分页
        //获取学校列表  页数及每页显示的条数
        if(empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
        if(empty($r->get('pageSize'))){
            $pageSize = 200;
        }else{
            $pageSize = $r->get('pageSize');
        }

        $domesticList = $this->collegeRankRepo->domesticList($rankOrg,$rankCountry,$regionList,$categoryList,$search,$page,$pageSize);

        return new JsonResponse($domesticList);
    }


    /**
     * 艺术类院校国内综合排名 筛选条件
     * @Route("/domestic/art-domestic-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function artDomesticScreen()
    {
        //筛选条件
        $artDomesticScreen = $this->collegeRankRepo->artDomesticScreen();
        return new JsonResponse($artDomesticScreen);
    }
    /**
     * 艺术类院校国内综合排名 列表
     * @Route("/domestic/art-domestic-list",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取排行列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *        @SWG\Property(property="rank_org", type="string", example="USNEWS"),
     *        @SWG\Property(property="rank_country", type="string", example="US"),
     *        @SWG\Property(property="page", type="int", example="1"),
     *        @SWG\Property(property="pageSize", type="int", example="10"),
     *        @SWG\Property(property="search", type="string", example="哈佛"),
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function artDomesticList(Request $r)
    {
        //排行类别
        if(empty($r->get('rank_org'))){
            $rankOrg = 'USNEWS';
        }else{
            $rankOrg = $r->get('rank_org');
        }
        //国家筛选
        if(empty($r->get('rank_country'))){
            $rankCountry ='';
        }else{
            $rankCountry = $r->get('rank_country');
        }

        //获取搜索条件
        if(empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        //分页
        //获取学校列表  页数及每页显示的条数
        if(empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
        if(empty($r->get('pageSize'))){
            $pageSize = 2;
        }else{
            $pageSize = $r->get('pageSize');
        }

        $artDomesticList = $this->collegeRankRepo->artDomesticList($rankOrg,$rankCountry,$search,$page,$pageSize);

        return new JsonResponse($artDomesticList);

    }



    /**
     * 艺术类院校全球综合排名 筛选条件
     * @Route("/global/art-global-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function artGlobalScreen()
    {
        //排行类别
        $rankOrg = $this->collegeRankRepo->artRankOrg();
        //国家/地区
        $rankCountry = $this->collegeRankRepo->artRankCountry();

        $artGlobalScreen = [
            'rank_org' => $rankOrg,
            'rank_country'=>$rankCountry
        ];
        return new JsonResponse($artGlobalScreen);
    }

    /**
     * 艺术类院校全球综合排名 列表
     * @Route("/global/art-global-list",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取排行列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *        @SWG\Property(property="rank_org", type="string", example="QS"),
     *        @SWG\Property(property="rank_country", type="string", example="US"),
     *        @SWG\Property(property="page", type="int", example="1"),
     *        @SWG\Property(property="pageSize", type="int", example="10"),
     *        @SWG\Property(property="search", type="string", example="哈佛"),
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function artGlobalList(Request $r)
    {

        //排行类别
        if(empty($r->get('rank_org'))){
            $rankOrg = 'QS';
        }else{
            $rankOrg = $r->get('rank_org');
        }
        //国家筛选
        if(empty($r->get('rank_country'))){
            $rankCountry ='global';
        }else{
            $rankCountry = $r->get('rank_country');
        }

        //获取搜索条件
        if(empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        //分页
        //获取学校列表  页数及每页显示的条数
        if(empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
        if(empty($r->get('pageSize'))){
            $pageSize = 2;
        }else{
            $pageSize = $r->get('pageSize');
        }

        $artGlobalRankList = $this->collegeRankRepo->artGlobalRankList($rankOrg,$rankCountry,$search,$page,$pageSize);

        return new JsonResponse($artGlobalRankList);
    }

    /**
     * 全球综合排名 筛选条件
     * @Route("/global/global-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function globalScreen(Request $r)
    {
        //学校排行类别
        $rankOrg = $this->collegeRankRepo->rankOrg();
        //学校排行国家和地区
        $rankCountry = $this->collegeRankRepo->rankCountry();
        //学校类型
        $collegeType = $this->collegeRankRepo->collegeType();

       $globalScreen = [
           'rank_org'=>$rankOrg,
           'rank_country'=>$rankCountry,
           'college_type'=>$collegeType
       ];
       return new JsonResponse($globalScreen);
    }
    /**
     * 全球综合排名 列表
     * @Route("/global/global-list",methods = {"POST"})
     * @SWG\Response(response =200 , description = "获取排行列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *        @SWG\Property(property="rank_org", type="string", example="QS"),
     *        @SWG\Property(property="rank_country", type="string", example="US"),
     *        @SWG\Property(property="page", type="int", example="1"),
     *        @SWG\Property(property="pageSize", type="int", example="10"),
     *        @SWG\Property(property="search", type="string", example="哈佛"),
     *        @SWG\Property(property="region", type="array",
     *              @SWG\Items(type="string",
     *                  example="northeast"
     *              )
     *          ),
     *        @SWG\Property(property="category", type="array",
     *              @SWG\Items(type="string",
     *                 example="comprehensive"
     *              )
     *          ),
     *      )
     * )
     * @SWG\Tag(name="collegeRank")
     */
    public function globalList(Request $r)
    {
        //排行类别
        if(empty($r->get('rank_org'))){
            $rankOrg = 'shjd';
        }else{
            $rankOrg = $r->get('rank_org');
        }
        //国家筛选
        if(empty($r->get('rank_country'))){
            $rankCountry = 'global';
        }else{
            $rankCountry = $r->get('rank_country');
        }
        //地域筛选条件
        if(empty($r->get('region'))){
            $regionList = [];
        }else{
            $regionList = $r->get('region');
            if(count($regionList) == 5){
                $regionList = [];
            }
        }
        // 学校类型筛选
        if(empty($r->get('category'))){
            $categoryList = [];
        }else{
            $categoryList = $r->get('category');
            if(count($categoryList) == 3){
                $categoryList = [];
            }
        }

        //获取搜索条件
        if(empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        //分页
        //获取学校列表  页数及每页显示的条数
        if(empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
        if(empty($r->get('pageSize'))){
            $pageSize = 2;
        }else{
            $pageSize = $r->get('pageSize');
        }
        $globalRankList = $this->collegeRankRepo->globalRankList($rankOrg,$rankCountry,$regionList,$categoryList,$search,$page,$pageSize);

        return new JsonResponse($globalRankList);
    }

}