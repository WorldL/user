<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\UserAccessToken;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;
use App\Service\SMS\LoginCode;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SMS\RegCode;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserFollower;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use App\Service\MockClient;
use Symfony\Component\Routing\RouterInterface;
use App\Entity\UserEducation;
use App\Service\SMS\ChangePassword;
use App\Service\SMS\ChangePhone;
use App\Entity\Feedback;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Service\Email;

class UserController extends AbstractController
{
    private $em;
    /**
     * @var \App\Repository\UserRepository $userRepo
     */
    private $userRepo;

    /**
     * @var \App\Repository\UserAccessTokenRepository $userAccessTokenRepository
     */
    private $userAccessTokenRepo;

    /**
     * @var \App\Repository\UserFollowerRepository $userFollowerRepo
     */
    private $userFollowerRepo;

    /**
     * @var \App\Repository\UserEducationRepository $userEduRepo
     */
    private $userEduRepo;

    /**
     * @var \App\Repository\FeedbackRepository $feedback
     */
    private $feedbackRepo;
    /**
     * @var \JMS\Serializer\Serializer $serializer
     */
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->em = $entityManager;
        $this->userRepo = $entityManager->getRepository(User::class);
        $this->userAccessTokenRepo = $entityManager->getRepository(UserAccessToken::class);
        $this->userFollowerRepo = $entityManager->getRepository(UserFollower::class);
        $this->userEduRepo = $entityManager->getRepository(UserEducation::class);
        $this->feedbackRepo = $entityManager->getRepository(Feedback::class);
        $this->serializer = $serializer;
    }
    /**
     * @Route("/user/create", methods={"POST"})
     * @SWG\Response(response=200, description="创建用户")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="username", type="string", example="牙签搅水缸"),
     *          @SWG\Property(property="region", type="string", example="086"),
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="password", type="string", example="123456"),
     *          @SWG\Property(property="gender", type="string", example="M"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function create(Request $r)
    {
        $user = $this->userRepo->create(
            $r->get('region'),
            $r->get('phone'),
            $r->get('username'),
            $r->get('password'),
            $r->get('gender'),
            $r->get('avatar')
        );

        $user = $this->serializer->toArray($user);
        unset($user['password']);

        return new JsonResponse($user);
    }

    /**
     * 批量添加用户
     * @Route("/user/batch-add", methods={"POST"})
     * @SWG\Response(response=200, description="批量添加用户")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="data", type="any", example=""),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function batchAdd(Request $r)
    {
        $data = $r->get('data');
        $this->userRepo->batchAdd($data);
        
        return new JsonResponse(['result' => 'ok']);
    }


    /**
     * @Route("/user/add-education", methods={"POST"})
     * @SWG\Response(response=200, description="添加教育背景")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="8"),
     *          @SWG\Property(property="college_id", type="integer", example="8"),
     *          @SWG\Property(property="major_id", type="integer", example="8"),
     *          @SWG\Property(property="degree", type="string", example="UNDERGRADUATE"),
     *          @SWG\Property(property="graduate_year", type="integer", example="2018"),
     *          @SWG\Property(property="graduate_month", type="integer", example="8"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function addEdu(Request $r)
    {
        $userEdu = $this->userEduRepo->addEdu(
            $r->get('user_id'),
            $r->get('college_id'),
            $r->get('major_id'),
            $r->get('degree'),
            $r->get('graduate_year'),
            $r->get('graduate_month')
        );

        $res = $this->serializer->toArray($userEdu);

        return new JsonResponse($res);
    }

    /**
     * @Route("/user/delete-education", methods={"POST"})
     * @SWG\Response(response=200, description="删教育经历")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function deleteEdu(Request $r)
    {
        $userEdu = $this->userEduRepo->findOneBy([
            'id' => $r->get('education_id'),
            'deletedAt' => null,
        ]);
        if (empty($userEdu)) {
            return new JsonResponse(['result' => 'ok']);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($userEdu);
        $em->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/user/verify-education", methods={"POST"})
     * @SWG\Response(response=200, description="认证教育经历")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *          @swg\property(property="email", type="string", example="abc@nyu.edu"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function verifyEdu(Request $r)
    {
        $res = $this->userEduRepo->verify($r->get('education_id'), $r->get('email'));

        return new JsonResponse($res);
    }
    /**
     * @Route("/user/send-verify-education-email", methods={"POST"})
     * @SWG\Response(response=200, description="发送认证教育经历邮件")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function sendVerifyEduEmail(Request $r, Email $emailSvc)
    {
        $educationId = $r->get('education_id');
        
        $edu = $this->userEduRepo->find($educationId);
        if (empty($edu) || $edu->isDeleted() || empty($edu->getVerifyEmail() || 'AUTHORIZING' !== $edu->getStatus())) {
            throw new \Exception('教育经历不存在或未提交认证');
        }
        $this->userEduRepo->sendVerifyEmail($edu);

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/user/education-detail", methods={"POST"})
     * @SWG\Response(response=200, description="教育经历信息")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function eduInfo(Request $r)
    {
        $edu = $this->userEduRepo->detail($r->get('education_id'));
        
        return new JsonResponse($edu);
    }

    /**
     * @Route("/user/mark-education-as-authorized", methods={"POST"})
     * @SWG\Response(response=200, description="标记教育经历已认证")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function markVerifyAsAuthorized(Request $r)
    {
        $edu = $this->userEduRepo->find($r->get('education_id'));
        if (empty($edu) || $edu->isDeleted() || empty($edu->getVerifyEmail() || 'AUTHORIZING' !== $edu->getStatus())) {
            throw new \Exception('教育经历不存在或未提交认证');
        }
        $edu->setStatus('AUTHORIZED');
        $this->em->merge($edu);
        $this->em->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 搜索用户
     * @Route("/user/search", methods={"POST"})
     * @SWG\Response(response=200, description="搜索用户")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="search", type="string", example="牙签搅水缸"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function search(Request $r)
    {
        $res = $this->userRepo->findByName(
            $r->get('name'),
            $r->get('page', 1),
            $r->get('pagesize', 20)
        );

        return new JsonResponse($res);
    }

    /**
     * 设置默经历育
     * @Route("/user/set-default-education", methods={"POST"})
     * @SWG\Response(response=200, description="设置默经历育")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="education_id", type="integer", example="2"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function setDefaultEdu(Request $r)
    {
        $this->userEduRepo->asDefault($r->get('education_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/user/edit", methods={"POST"})
     * @SWG\Response(response=200, description="编辑用户")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="id", type="integer", example="12"),
     *          @swg\property(property="changes", type="object", example="{}"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function edit(Request $r)
    {
        $user = $this->userRepo->findOneBy(['id' => $r->get('id')]);

        if (empty($user)) {
            throw new \Exception('用户不存在');
        }

        try {
            $this->userRepo->edit($user, $r->get('changes'));
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception('用户名已存在');
        } catch (\Exception $e) {
            throw new \Exception('修改失败，请重新尝试');
        }

        return new JsonResponse($this->userRepo->getInfo($user, 'detail'));
    }

    /**
     * @Route("/user/delete", methods={"POST"})
     * @SWG\Response(response=200, description="删除用户")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="name", type="string", example="牙签搅水缸"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function delete()
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => '牙签搅水缸']);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
    }

    /**
     * @Route("/user/education-experience", methods={"POST"})
     * @SWG\Response(response=200, description="查看用户教育背景")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="user_id", type="integer", example="8"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function getEduExp(Request $r)
    {
        $res = $this->userEduRepo->expByUser($r->get('user_id'));

        return new JsonResponse($res);
    }

    /**
     * @Route("/user/view", methods={"POST"})
     * @SWG\Response(response=200, description="查看用户")
     * @swg\parameter(
     *      name="body", in="body",
     *      @swg\schema(
     *          type="object",
     *          @swg\property(property="name", type="string", example="牙签搅水缸"),
     *      )
     * )
     * @swg\tag(name="user")
     */
    public function view(Request $r)
    {
        // 用户基础信息和详情获取参数
        $scope = $r->get('scope', 'base');
        // 有token试用token模式找用户
        if (!empty($r->get('token'))) {
            $token = $this->userAccessTokenRepo->findOneBy(['token' => $r->get('token')]);
            if (empty($token)) {
                throw new \Exception('token不存在');
            }
            $user = $this->userRepo->findOneBy(['id' => $token->getUserId()]);
            if (empty($user)) {
                throw new \Exception('用户不存在');
            }

            return new JsonResponse($this->userRepo->getInfo($user, $scope));
        }

        $condition = [];
        if (!empty($r->get('phone'))) {
            $condition['phone'] = $r->get('phone');
        }
        if (!empty($r->get('username'))) {
            $condition['username'] = $r->get('username');
        }
        if (!empty($r->get('id'))) {
            $condition['id'] = $r->get('id');
        }
        if (0 === count($condition)) {
            throw new \Exception('请填写条件');
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy($condition);
        if (empty($user)) {
            return new JsonResponse([]);
        }
        // $avatarUrl = $user->getAvatarUrl();
        // $user = $this->serializer->toArray($user);
        // $user['avatar'] = $avatarUrl;
        // unset($user['password']);

        return new JsonResponse($this->userRepo->getInfo($user, $scope));
    }

    /**
     * 确认账号密码是否正确
     * @Route("/user/check-password", methods={"POST"})*
     * @SWG\Response(response=200, description="验证手机号密码是否正确")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="region", type="string", example="86"),
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="password", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function checkPassword(Request $r)
    {
        $res = $this->userRepo->checkPassword($r->get('phone'), $r->get('password'))
               ? 'correct' : 'wrong';

        return new JsonResponse(['result' => $res]);
    }
    /**
     * 发送修改手机号验证码短信
     * @Route("/user/change-phone-sms", methods={"POST"})
     *
     * @SWG\Response(response=200, description="发送修改手机号短信")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="code", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function changePhoneSms(Request $r, ChangePhone $smsCode)
    {
        $res = $smsCode->send($r->get('phone'), ['code' => $r->get('code')], $r->get('region', '86'));

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * 修改手机号
     * @Route("/user/change-phone", methods={"POST"})
     *
     * @SWG\Response(response=200, description="修改手机号")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="1"),
     *          @SWG\Property(property="region", type="string", example="123456"),
     *          @SWG\Property(property="phone", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function changePhone(Request $r)
    {
        $user = $this->userRepo->find($r->get('user_id'));
        if (empty($user)) {
            throw new \Exception('用户不存在');
        }
        $user->setRegionCode($r->get('region'));
        $user->setPhone($r->get('phone'));

        $this->em->merge($user);
        $this->em->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 发送修改密码验证码短信
     * @Route("/user/change-password-sms", methods={"POST"})
     *
     * @SWG\Response(response=200, description="发送修改密码短信")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="code", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function changePasswordSms(Request $r, ChangePassword $loginCode)
    {
        $res = $loginCode->send($r->get('phone'), ['code' => $r->get('code')], $r->get('region', '86'));

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * 修改密码
     * @Route("/user/change-password", methods={"POST"})
     *
     * @SWG\Response(response=200, description="修改密码")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="1"),
     *          @SWG\Property(property="password", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function changePassword(Request $r, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->userRepo->find($r->get('user_id'));
        if (empty($user)) {
            throw new \Exception('用户不存在');
        }

        $user->setPassword($encoder->encodePassword($user, $r->get('password')));
        $this->em->merge($user);
        $this->em->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 发送登录验证码短信
     * @Route("/user/login-sms", methods={"POST"})
     *
     * @SWG\Response(response=200, description="发送登录短信")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="code", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function loginSms(Request $r, LoginCode $loginCode)
    {
        $res = $loginCode->send($r->get('phone'), ['code' => $r->get('code')], $r->get('region', '86'));

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * 发送注册验证码短信
     * @Route("/user/reg-sms", methods={"POST"})
     *
     * @SWG\Response(response=200, description="发送注册短信")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="region", type="string", example="86"),
     *          @SWG\Property(property="phone", type="string", example="13888888888"),
     *          @SWG\Property(property="code", type="string", example="123456"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function regSms(Request $r, RegCode $regCode)
    {
        $region = empty($r->get('region')) ? '86' : $r->get('region');
        $res = $regCode->send($r->get('phone'), ['code' => $r->get('code')], $region);

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * 获取授权token
     *
     * 通过用户名密码获取授权token
     *
     * @Route("/user/make-token", methods={"POST"})
     *
     * @SWG\Response(response=200, description="获取token成功")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="phone", type="string", example="user"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function makeToken(Request $r)
    {
        // if (!$this->userRepo->checkPassword($r->get('phone'), $r->get('password'))) {
        //     throw new \Exception("password error");
        // }
        $platform = $r->headers->get('Platform');
        $version = $r->headers->get('App-Version');
        $user = $this->userRepo->findOneBy(['phone' => $r->get('phone')]);
        $token = $this->userAccessTokenRepo->makeToken($user, [
            'platform' => $platform, 'version' => $version
        ]);

        return new JsonResponse($this->serializer->toArray($token));
    }

    /**
     * 刷新token有效期
     *
     * @Route("/user/refresh-token", methods={"POST"})
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
     * @SWG\Tag(name="user")
     * @return void
     */
    public function refreshToken(Request $r)
    {
        $token = $this->userAccessTokenRepo->refreshToken($r->get('token'), $r->get('refresh_token'));

        return new JsonResponse($this->serializer->toArray($token));
    }

    /**
     * 查看是否关注
     * @Route("/user/follow-status", methods={"POST"})
     *
     * @SWG\Response(response=200, description="查看是否关注")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="follower_id", type="int", example="6"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function followStatus(Request $r)
    {
        $m = $this->userFollowerRepo->findOneBy([
            'user_id' => $r->get('user_id'),
            'follower_id' => $r->get('follower_id'),
            'deletedAt' => null,
        ]);

        return new JsonResponse([
            'result' => empty($m) ? 'no' : 'yes',
        ]);
    }

    /**
     * 相对关注状态
     * @Route("/user/relative-follow-status", methods={"POST"})
     *
     * @SWG\Response(response=200, description="相对关注状态")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="relatived_id", type="int", example="6"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function relativeFollowStatus(Request $r)
    {
        $res = $this->userFollowerRepo->relativeFollowStatus(
            $r->get('user_id', 0),
            $r->get('relatived_id', 0)
        );
        return new JsonResponse($res);
    }
    /**
     * 查看是否关注
     * @Route("/user/follow", methods={"POST"})
     *
     * @SWG\Response(response=200, description="查看是否关注")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="follower_id", type="int", example="6"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function follow(Request $r)
    {
        $this->userFollowerRepo->follow($r->get('user_id'), $r->get('follower_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 查看是否关注
     * @Route("/user/un-follow", methods={"POST"})
     *
     * @SWG\Response(response=200, description="查看是否关注")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="follower_id", type="int", example="6"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function unFollow(Request $r)
    {
        $this->userFollowerRepo->unFollow($r->get('user_id'), $r->get('follower_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * 查看关注列表
     * @Route("/user/follow-list", methods={"POST"})
     *
     * @SWG\Response(response=200, description="查看关注列表")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="relatived_id", type="int", example="4"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function followList(Request $r)
    {
        return new JsonResponse($this->userFollowerRepo->followList(
            $r->get('user_id'),
            $r->get('relatived_id', 0),
            $r->get('page', 1),
            $r->get('pagesize', 30)
        ));
    }

    /**
     * 查看粉丝列表
     * @Route("/user/fans-list", methods={"POST"})
     *
     * @SWG\Response(response=200, description="查看粉丝列表")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="relatived_id", type="int", example="4"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function fansList(Request $r)
    {
        return new JsonResponse($this->userFollowerRepo->fansList(
            $r->get('user_id'),
            $r->get('relatived_id', 0),
            $r->get('page', 1),
            $r->get('pagesize', 30)
        ));
    }

    /**
     * 意见反馈
     * @Route("/user/feedback", methods={"POST"})
     *
     * @SWG\Response(response=200, description="意见反馈")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="int", example="4"),
     *          @SWG\Property(property="content", type="string", example="4"),
     *      )
     * )
     * @SWG\Tag(name="user")
     */
    public function feeedback(Request $r)
    {
        $this->feedbackRepo->add($r->get('user_id'), $r->get('content'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * t
     * @Route("t", methods={"GET", "POST"})
     */
    public function t(RpcClient $rpcClient, MockClient $mockClient, RouterInterface $router)
    {
        $transport = (new \Swift_SmtpTransport('smtp.exmail.qq.com', 25))
            ->setUsername('it@sanzhi-net.com')
            ->setPassword('Sz123!@#');
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message("教育经历认证"))
            ->setFrom('it@sanzhi-net.com')
            ->setTo('zhushiya@sanzhi-net.com')
            ->setBody('请点击以下链接完成认证:http://m.xiaohailang.net');
        dd($mailer->send($message));
        // $req = Request::create('/user/view', 'POST', ['id' => 8, 'scope' => 'detail']);
        // $kernel = new \App\Kernel('env', true);
        // $res = $kernel->handle($req);

        // return $res;

        // $rpcClient->addRequest(json_encode([
        //     'route' => '/user/view',
        //     'request' => ['id' => 8, 'scope' => 'detail'],
        // ]), 'user', 't');
        // return new JsonResponse($rpcClient->getReplies()['t']);

        // $res = $mockClient->request('user', '/force-stop', ['id' => 8]);
        $res = $mockClient->request('info', '/info/detail', ['info_id' => 25]);

        return new JsonResponse($res);
    }
}
