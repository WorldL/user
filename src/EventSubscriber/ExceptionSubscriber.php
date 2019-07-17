<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\ServerException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();
        $withException = $request->headers->get('Filter-Exception');
        if (empty($withException)) {
            return;
        }

        $status = 1;
        $code = 500;
        $message = $exception->getMessage();
        if ($exception instanceof AuthenticationException) {
            $code = 401;
        }
        if ($exception instanceof InvalidArgumentException) {
            $code = 400;
        }
        if ($exception instanceof ServerException) {
            $code = 500;
            $message = 'Request Service Error.';
        }

        $event->setResponse(new JsonResponse(compact('status', 'message'), $code));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
