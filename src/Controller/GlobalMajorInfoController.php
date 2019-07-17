<?php

namespace App\Controller;

use App\Entity\GlobalMajorInfo;
use App\Entity\GlobalMajorRank;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class GlobalMajorInfoController extends AbstractController
{


    /**
     * @var \App\Repository\GlobalMajorInfoRepository $globalMajorInfoRepo
     */
    private $globalMajorInfoRepo;
    /**
     * @var \App\Repository\GlobalMajorRankRepository $globalMajorRankRepo
     */
    private $globalMajorRankRepo;
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        $this->globalMajorInfoRepo = $entityManager->getRepository(GlobalMajorInfo::class);
        $this->globalMajorRankRepo = $entityManager->getRepository(GlobalMajorRank::class);
    }


    /**
     * 全球专业排名 筛选条件
     * @Route("/globalmajor/screen", methods={"POST"})
     * @SWG\Response(response=200, description="搜索筛选条件")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *      )
     * )
     * @swg\tag(name="globalMajorRank")
     */
    public function screen()
    {
        //筛选条件 列表
        $screenList = $this->globalMajorInfoRepo->screen();

        return new JsonResponse($screenList);
    }
    /**
     * 全球专业学校排行列表
     * @Route("/globalmajor/list", methods={"POST"})
     * @SWG\Response(response=200, description="学校排行列表")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *       @SWG\Property(property="id", type="int", example="1"),
     *       @SWG\Property(property="rank_country", type="string", example="1"),
     *       @SWG\Property(property="page", type="int", example="1"),
     *       @SWG\Property(property="pageSize", type="int", example="10"),
     *       @SWG\Property(property="search", type="string", example="哈佛"),
     *      )
     * )
     * @swg\tag(name="globalMajorRank")
     */
    public function list(Request $r)
    {
        //id
        if(empty($r->get('id'))){
            $id = 1;
        }else{
            $id = $r->get('id');
        }
        //国家/地区
        if(empty($r->get('rank_country'))){
            $country = 'global';
        }else{
            $country = $r->get('rank_country');
        }
        //分页
        if(empty($r->get('page'))){
            $page = 1;
        }else{
            $page = $r->get('page');
        }
        if(empty($r->get('pageSize'))){
            $pageSize = 10;
        }else{
            $pageSize=$r->get('pageSize');
        }
        //模糊搜索
        if(empty($r->get('search'))){
            $search = '';
        }else{
            $search = $r->get('search');
        }

        $majorRank = $this->globalMajorRankRepo->list($id,$country,$page,$pageSize,$search);

        return new JsonResponse($majorRank);
    }


}
