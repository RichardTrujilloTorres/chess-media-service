<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListenerSubscriber implements EventSubscriberInterface
{
    public const INTERNAL_SERVER_ERROR_MESSAGE = 'Internal server error.';
    public const CUSTOM_EXCEPTION_ERROR_CODES = [
        Response::HTTP_BAD_REQUEST,
        Response::HTTP_UNAUTHORIZED,
        Response::HTTP_NOT_FOUND,
    ];

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logError($exception);

        $response = (new JsonResponse())
            ->setContent(json_encode($this->resolveExceptionBody($exception)))
            ->setStatusCode($this->resolveStatusCode($exception->getCode()));
        $event->setResponse($response);
    }

    protected function logError(\Throwable $exception): void
    {
        if ('prod' === $_SERVER['APP_ENV']) {
            error_log(json_encode(['message' => $exception->getMessage()]));
        }
        if ('dev' === $_SERVER['APP_ENV']) {
            error_log(json_encode(['message' => $exception->getMessage()]));
            error_log(json_encode(['trace' => $exception->getTrace()]));
        }
    }

    protected function resolveExceptionBody(\Throwable $exception): array
    {
        return 'dev' === $_SERVER['APP_ENV'] || 'test' === $_SERVER['APP_ENV'] ?
            [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ] :
            [
                'message' => $this->resolveMessage($exception),
            ];
    }

    protected function resolveMessage(\Throwable $exception): string
    {
        if ('prod' !== $_SERVER['APP_ENV']) {
            return $exception->getMessage();
        }

        return $this->shouldShowMessage($exception->getCode()) ?
            $exception->getMessage() :
            self::INTERNAL_SERVER_ERROR_MESSAGE;
    }

    protected function shouldShowMessage(int $statusCode): bool
    {
        return in_array($statusCode, self::CUSTOM_EXCEPTION_ERROR_CODES);
    }

    protected function resolveStatusCode(int $statusCode): int
    {
        return in_array($statusCode, self::CUSTOM_EXCEPTION_ERROR_CODES) ?
            $statusCode :
            Response::HTTP_BAD_REQUEST;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
