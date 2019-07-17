<?php

namespace App\Controller;

use App\Entity\College;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\DocumentCol;

class DocumentController extends AbstractController
{


    //document 表
    private $documentRepo;

    //college 表
    private $collegeRepo;

    private $documentColRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->documentColRepo = $entityManager->getRepository(DocumentCol::class);
        $this->documentRepo = $entityManager->getRepository(Document::class);
        $this->collegeRepo = $entityManager->getRepository(College::class);
    }


    /**
     *文书列表
     * @Route("/document",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="college_id", type="int", example="1"),
     *          @SWG\Property(property="education", type="string", example="und"),
     *          @SWG\Property(property="major_en", type="string", example="Computer Science"),
     *          @SWG\Property(property="doc_type", type="string", example="Personal Statement"),
     *          @SWG\Property(property="page", type="int", example="1"),
     *          @SWG\Property(property="pagesize", type="int", example="10"),
     *          @SWG\Property(property="user_id", type="int", example="10"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function index(Request $r)
    {
        //获取学校ID
        if (empty($r->get('college_id'))) {
            $collegeId = '';
        } else {
            $collegeId = $r->get('college_id');
        }
        //获取学位信息
        if (empty($r->get('education'))) {
            $education = '';
        } else {
            $education = $r->get('education');
        }
        //获取专业信息
        if (empty($r->get('major_en'))) {
            $majorEn = '';
        } else {
            $majorEn = $r->get('major_en');
        }
        //获取文书类型
        if (empty($r->get('doc_type'))) {
            $docType = '';
        } else {
            $docType = $r->get('doc_type');
        }
        // 页数及每页显示的条数
        if (empty($r->get('page'))) {
            $page = 1;
        } else {
            $page = $r->get('page');
        }
        if (empty($r->get('pageSize'))) {
            $pageSize = 10;
        } else {
            $pageSize = $r->get('pagesize');
        }

        $docList = $this->documentRepo->list($collegeId, $education, $majorEn, $docType, $page, $pageSize, $r->get('user_id'));
        return new JsonResponse($docList);
    }
    /**
     *文书筛选条件(学校列表)
     * @Route("/document/college-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="majorEN", type="string", example="Mathematics"),
     *          @SWG\Property(property="search", type="string", example="哈佛"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */

    public function collegeScreen(Request $r)
    {
        //选择专业后筛选学校列表
        if (empty($r->get('majorEN'))) {
            $majorEn = '';
        } else {
            $majorEn = $r->get('majorEN');
        }

        //学校列表模糊查询
        if (empty($r->get('search'))) {
            $search = '';
        } else {
            $search = $r->get('search');
        }

        //文书库库学校id
        $docCollegeIdList =  $this->documentRepo->docCollegeList($majorEn);

        //获取学校列表
        $docCollegeList = $this->collegeRepo->docCollegeList($docCollegeIdList, $search);

        return new JsonResponse($docCollegeList);
    }
    /**
     *文书筛选条件(专业列表)
     * @Route("/document/major-screen",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书筛选条件")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="collegeId", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function majorScreen(Request $r)
    {
        if (empty($r->get('collegeId'))) {
            $collegeId = '';
        } else {
            $collegeId = $r->get('collegeId');
        }

        $docMajorList = $this->documentRepo->docMajorList($collegeId);

        return new JsonResponse($docMajorList);
    }

    /**
     *文书详情
     * @Route("/document/details",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书详情")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function details(Request $r)
    {
        if (empty($r->get('id'))) {
            $docDetails = [];
        } else {
            $id = $r->get('id');
            $docDetails = $this->documentRepo->details($id, $r->get('user_id'));
        }
        return new JsonResponse($docDetails);
    }

    /**
     * 文书收藏
     * @Route("/document/col",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书收藏")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="1"),
     *          @SWG\Property(property="doc_id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function col(Request $r)
    {
        $this->documentColRepo->col($r->get('user_id'), $r->get('doc_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 取消文书收藏
     * @Route("/document/un-col",methods = {"POST"})
     * @SWG\Response(response =200 , description = "取消文书收藏")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="1"),
     *          @SWG\Property(property="doc_id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function unCol(Request $r)
    {
        $this->documentColRepo->unCol($r->get('user_id'), $r->get('doc_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 文书收藏状态
     * @Route("/document/col-status",methods = {"POST"})
     * @SWG\Response(response =200 , description = "文书收藏状态")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="1"),
     *          @SWG\Property(property="doc_id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function colStatus(Request $r)
    {
        $res = $this->documentColRepo->colStatus($r->get('user_id'), $r->get('doc_id'));

        return new JsonResponse(['result' => $res]);
    }

    /**
     * 文书收藏列表
     * @Route("/document/col-list",methods = {"POST"})
     * @SWG\Response(response=200 , description = "文书收藏列表")
     *  @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="1"),
     *      )
     * )
     * @SWG\Tag(name="document")
     */
    public function colList(Request $r)
    {
        $res = $this->documentColRepo->colList($r->get('user_id'), $r->get('page', 1), $r->get('pagesize', 10));

        return new JsonResponse($res);
    }
}
