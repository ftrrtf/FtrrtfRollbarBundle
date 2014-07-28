<?php
namespace Ftrrtf\RollbarBundle\EventListener;

use Ftrrtf\Rollbar\ErrorHandler;
use Ftrrtf\Rollbar\Notifier;
use Ftrrtf\RollbarBundle\Helper\UserHelper;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class RollbarListener
 *
 * @package Ftrrtf\RollbarBundle\EventListener
 */
class RollbarListener
{
    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;
    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * Init
     *
     * @param Notifier                 $notifier
     * @param ErrorHandler             $errorHandler
     * @param SecurityContextInterface $securityContext
     * @param UserHelper               $userHelper
     */
    public function __construct(
        Notifier $notifier,
        ErrorHandler $errorHandler,
        SecurityContextInterface $securityContext,
        UserHelper $userHelper
    ) {
        $this->notifier        = $notifier;
        $this->errorHandler    = $errorHandler;
        $this->securityContext = $securityContext;
        $this->userHelper      = $userHelper;

        $self = $this;
        $this->notifier->getEnvironment()
            ->setOption('person_callback', function() use ($self) {
                return $self->getUserData();
            });
    }

    /**
     * Register error handler
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->errorHandler->registerErrorHandler($this->notifier);
        $this->errorHandler->registerShutdownHandler($this->notifier);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // Skip HTTP exception
        if ($event->getException() instanceof HttpException) {
            return;
        }

        $this->setException($event->getException());
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $this->notifier->reportException($event->getException());
    }

    /**
     * Wrap exception with additional info
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->getException()) {
            $this->notifier->reportException($this->getException());
            $this->setException(null);
        }
    }

    /**
     * Get current user info
     *
     * @return null|array
     */
    public function getUserData()
    {
        if (!$this->securityContext->getToken() || !$this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return null;
        }

        $user = $this->securityContext->getToken()->getUser();

        if (!$user) {
            return null;
        }

        return $this->userHelper->buildUserData($user);
    }


    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception|null $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

}
