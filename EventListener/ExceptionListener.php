<?php
namespace Ftrrtf\RollbarBundle\EventListener;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ExceptionListener
 *
 * @package Ftrrtf\RollbarBundle\EventListener
 */
class ExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        // Skip HTTP exceptions
        if ($exception instanceof HttpException) {
            return;
        }

        \Rollbar::report_exception($exception);
    }
}