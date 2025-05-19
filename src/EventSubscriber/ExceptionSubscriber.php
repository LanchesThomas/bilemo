<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{   
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Si c’est une NotFoundHttpException causée par l’EntityResolver
        if (
            $exception instanceof NotFoundHttpException
        ) {
            $data = [
                'status' => 404,
                'message' => 'Ressource non trouvée : ' . $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
            return;
        }

        // Cas général pour les HttpException (403, 401, etc.)
        if ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
            return;
        }

        // Cas générique (erreur serveur, non HttpException)
        $data = [
            'status' => 500,
            'message' => 'Erreur serveur : ' . $exception->getMessage()
        ];
        $event->setResponse(new JsonResponse($data));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
