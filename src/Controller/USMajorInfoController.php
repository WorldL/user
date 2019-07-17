<?php

namespace App\Controller;

use App\Entity\USMajorRank;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use App\Entity\USMajorInfo;
use Swagger\Annotations as SWG;

class USMajorInfoController extends AbstractController
{

    /**
     * @var \App\Repository\USMajorInfoRepository $usMajorInfoRepo
     */
    private $usMajorInfoRepo;

    private $usMajorRankRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {

        $this->usMajorInfoRepo = $entityManager->getRepository(USMajorInfo::class);
        $this->serializer = $serializer;
        $this->usMajorRankRepo = $entityManager->getRepository(USMajorRank::class);
    }


    /**
     * 国内专业排名
     * @Route("/usmajor/screen", methods={"POST"})
     * @SWG\Response(response=200, description="搜索筛选条件")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *      )
     * )
     * @swg\tag(name="usMajorRank")
     */
    public function screen(Request $r)
    {
        //筛选条件 列表
        $country = $this->usMajorInfoRepo->screen();

        return new JsonResponse($country);
    }
    /**
     * 国内专业学校排行列表
     * @Route("/usmajor/list", methods={"POST"})
     * @SWG\Response(response=200, description="学校排行列表")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *       @SWG\Property(property="id", type="int", example="1"),
     *       @SWG\Property(property="page", type="int", example="1"),
     *       @SWG\Property(property="pageSize", type="int", example="10"),
     *       @SWG\Property(property="search", type="string", example="哈佛"),
     *      )
     * )
     * @swg\tag(name="usMajorRank")
     */
    public function list(Request $r)
    {
        //id
        if(empty($r->get('id'))){
            $id = 1;
        }else{
            $id = $r->get('id');
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

        $majorRank = $this->usMajorRankRepo->list($id,$page,$pageSize,$search);

        return new JsonResponse($majorRank);
    }

}
