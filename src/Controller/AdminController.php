<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin;
use App\Entity\AdminAccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @var \App\Repository\AdminRepository $adminRepo
     */
    private $adminRepo;

    /**
     * @var \App\Repository\AdminAccessTokenRepository $adminAccessTokenRepository
     */
    private $adminAccessTokenRepo;

    /**
     * @var \JMS\Serializer\Serializer $serializer
     */
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->adminRepo = $entityManager->getRepository(Admin::class);
        $this->adminAccessTokenRepo = $entityManager->getRepository(AdminAccessToken::class);
        $this->serializer = $serializer;
    }
    /**
     * 获取授权token
     *
     * 通过用户名密码获取授权token
     *
     * @Route("/admin/make-token", methods={"POST"})
     *
     * @SWG\Response(response=200, description="获取token成功")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user", type="string", example="user"),
     *          @SWG\Property(property="pass", type="string", example="user"),
     *      )
     * )
     * @SWG\Tag(name="admin")
     */
    public function makeToken(Request $r)
    {
        if (!$this->adminRepo->checkPassword($r->get('user'), $r->get('pass'))) {
            throw new \Exception("password error");
        }
        $a = $this->adminRepo->findOneBy(['username' => $r->get('user')]);
        $token = $this->adminAccessTokenRepo->makeToken($a, ['platform' => 'Web']);

        return new JsonResponse($this->serializer->toArray($token));
    }

    /**
     * 刷新token有效期
     *
     * @Route("/admin/refresh-token", methods={"POST"})
     *
     * @SWG\Response(response=200, description="刷新token成功")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="token", type="string", example="user"),
     *          @SWG\Property(property="refresh_token", type="string", example="user"),
     *      )
     * )
     * @SWG\Tag(name="admin")
     * @return void
     */
    public function refreshToken(Request $r)
    {
        $token = $this->adminAccessTokenRepo->refreshToken($r->get('token'), $r->get('refresh_token'));

        return new JsonResponse($this->serializer->toArray($token));
    }

    /**
     * 创建管理员用户
     *
     * 通过用户名+密码创建管理员账号
     *
     * @Route("/admin/create", methods={"POST"})
     *
     * @SWG\Response(response=200, description="创建成功状态")
     * @SWG\Parameter(name="user",in="formData", type="string", description="user name")
     * @SWG\Parameter(name="pass", in="formData", type="string", description="password")
     *
     * @SWG\Tag(name="admin")
     *
     * @return void
     */
    public function create(Request $r)
    {
        $admin = $this->adminRepo->create($r->get('user'), $r->get('pass'));

        return new JsonResponse($this->serializer->toArray($admin));
    }
    
    /**
     * 删除管理员账号
     *
     * @Route("/admin/remove", methods={"POST"})
     * @SWG\Response(response=200, description="删除成功")
     * @SWG\Parameter(name="user", in="formData", type="string", description="user name")
     * @SWG\Tag(name="admin")
     *
     * @param Request $request
     * @return void
     */
    public function remove(Request $r)
    {
        try {
            $this->adminRepo->remove($r->get('user'));
        } catch (\Exception $e) {
            throw $e;
        }
        return new JsonResponse(['status' => 0]);
    }

    /**
     * 通过token或用户id获取用户信息
     *
     * admin_id和token参数同时存在时token优先，可用此方法验证用户token合法性
     *
     * @Route("/admin/info", methods={"POST"})
     *
     * @SWG\Response(response=200, description="获取admin用户信息")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="token", type="string", example="abc"),
     *          @SWG\Property(property="admin_id", type="integer", example=123),
     *      )
     * )
     * @SWG\Tag(name="admin")
     */
    public function info(Request $r)
    {
        $token = $r->get('token');
        if (is_string($token) && !empty($token)) {
            $aat = $this->adminAccessTokenRepo->findOneBy(['token' => $token]);
        }
        if (empty($aat) || $aat->isDeleted()) {
            $adminId = $r->get('admin_id');
            if (!is_numeric($adminId) || $adminId <= 0) {
                throw new \Exception('用户不存在');
            }
        } else {
            $adminId = $aat->getAdminId();
        }
        $admin = $this->adminRepo->findOneBy(['id' => $adminId]);
        if (empty($admin)) {
            throw new \Exception('用户不存在');
        }
        return new JsonResponse($this->serializer->toArray($admin));
    }

    public function edit()
    {
    }
}
