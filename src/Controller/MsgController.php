<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use App\Entity\MsgSystem;
use App\Entity\MsgNotify;
use App\Entity\Activity;

class MsgController extends AbstractController
{
    private $em;
    /**
     * @var \App\Repository\MsgSystemRepository $msgSystemRepo
     */
    private $msgSystemRepo;
    /**
     * @var \App\Repository\MsgNotifyRepository $msgNotifyRepo
     */
    private $msgNotifyRepo;
    /**
     * @var \App\Repository\ActivityRepository $msgActivityRepo
     */
    private $activityRepo;

    /**
     * @var \JMS\Serializer\Serializer $serializer
     */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->msgSystemRepo = $this->em->getRepository(MsgSystem::class);
        $this->msgNotifyRepo = $this->em->getRepository(MsgNotify::class);
        $this->activityRepo = $this->em->getRepository(Activity::class);
        $this->serializer = $serializer;
    }
    /**
     * @Route("/msg/system/add", methods={"POST"})
     *
     * @SWG\Response(response=200, description="添加系统留言")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *          @SWG\Property(property="content", type="text", example="111111111"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function addSystemMsg(Request $r)
    {
        $res = $this->msgSystemRepo->addMsg($r->get('user_id'), $r->get('content'));

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * @Route("/msg/system/mark-as-read", methods={"POST"})
     *
     * @SWG\Response(response=200, description="添加系统留言")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function markSystemMsgAsRead(Request $r)
    {
        $this->msgSystemRepo->markAsRead($r->get('user_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/msg/system/list", methods={"POST"})
     *
     * @SWG\Response(response=200, description="添加系统留言")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function systemMsgList(Request $r)
    {
        $res = $this->msgSystemRepo->list(
            $r->get('user_id'),
            $r->get('page', 1),
            $r->get('pagesize', 20)
        );

        return new JsonResponse($res);
    }

    /**
     * @Route("/msg/system/unread-count", methods={"POST"})
     *
     * @SWG\Response(response=200, description="系统未读计数")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function systemMsgUnreadCount(Request $r)
    {
        $count = $this->msgSystemRepo->unreadCount(
            $r->get('user_id')
        );

        return new JsonResponse(compact('count'));
    }

    /**
     * @Route("/msg/notify/add", methods={"POST"})
     *
     * @SWG\Response(response=200, description="添加通知消息")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *          @SWG\Property(property="notifier_id", type="integer", example="123"),
     *          @SWG\Property(property="info_id", type="integer", example="123"),
     *          @SWG\Property(property="review_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function addNotifyMsg(Request $r)
    {
        $res = $this->msgNotifyRepo->addMsg(
            $r->get('user_id'),
            $r->get('notifier_id'),
            $r->get('info_id'),
            $r->get('review_id')
        );

        return new JsonResponse($this->serializer->toArray($res));
    }

    /**
     * @Route("/msg/notify/mark-as-read", methods={"POST"})
     *
     * @SWG\Response(response=200, description="标记通知消息为已读")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function markNotifyMsgAsRead(Request $r)
    {
        $this->msgNotifyRepo->markAsRead($r->get('user_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/msg/notify/mark-one-as-read", methods={"POST"})
     *
     * @SWG\Response(response=200, description="标记通知消息为已读")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="msg_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function markOneNotifyMsgAsRead(Request $r)
    {
        $msg = $this->msgNotifyRepo->find($r->get('msg_id'));
        if (empty($msg)) {
            return new JsonResponse(['result' => 'ok']);
        }
        $msg->setReadStatus('YES');
        $this->em->merge($msg);
        $this->em->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/msg/notify/list", methods={"POST"})
     *
     * @SWG\Response(response=200, description="添加系统留言")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function nofifyMsgList(Request $r)
    {
        $res = $this->msgNotifyRepo->list(
            $r->get('user_id'),
            $r->get('page', 1),
            $r->get('pagesize', 20)
        );

        return new JsonResponse($res);
    }

    /**
     * @Route("/msg/notify/detail", methods={"POST"})
     *
     * @SWG\Response(response=200, description="通知消息详情")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="msg_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function notifyMsgDetail(Request $r)
    {
        return new JsonResponse($this->serializer->toArray(
            $this->msgNotifyRepo->find($r->get('msg_id'))
        ));
    }

    /**
     * @Route("/msg/notify/unread-count", methods={"POST"})
     *
     * @SWG\Response(response=200, description="通知未读计数")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function notifyMsgUnreadCount(Request $r)
    {
        $count = $this->msgNotifyRepo->unreadCount(
            $r->get('user_id')
        );

        return new JsonResponse(compact('count'));
    }

    /**
     * @Route("/msg/activity/unread-count", methods={"POST"})
     *
     * @SWG\Response(response=200, description="活动未读计数")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function activityUnreadCount(Request $r)
    {
        $count = $this->activityRepo->countUnreadByUser($r->get('user_id'));

        return new JsonResponse(compact('count'));
    }

    /**
     * @Route("/msg/activity/read", methods={"POST"})
     *
     * @SWG\Response(response=200, description="活动未读计数")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="user_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function readActivity(Request $r)
    {
        $this->activityRepo->readActivity($r->get('user_id'));

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/msg/activity/list", methods={"POST"})
     *
     * @SWG\Response(response=200, description="活动列表")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function activityMsgList(Request $r)
    {
        $res = $this->activityRepo->list(
            $r->get('page', 1),
            $r->get('pagesize', 10)
        );

        return new JsonResponse($res);
    }
    /**
     * @Route("/msg/notify/del", methods={"POST"})
     *
     * @SWG\Response(response=200, description="删除通知消息")
     * @SWG\Parameter(
     *      name="body", in="body",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="info_id", type="integer", example="123"),
     *          @SWG\Property(property="review_id", type="integer", example="123"),
     *      )
     * )
     * @SWG\Tag(name="msg")
     */
    public function deleteNotifyMsg(Request $r)
    {
        if (is_int($r->get('info_id'))) {
            $this->msgNotifyRepo->deleteByInfo($r->get('info_id'));
        }
        if (is_int($r->get('review_id'))) {
            $this->msgNotifyRepo->deleteByReview($r->get('review_id'));
        }

        return new JsonResponse(['result' => 'ok']);
    }
}
