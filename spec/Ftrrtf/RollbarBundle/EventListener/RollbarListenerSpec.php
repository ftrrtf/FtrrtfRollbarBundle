<?php

namespace spec\Ftrrtf\RollbarBundle\EventListener;

use Ftrrtf\Rollbar\Environment;
use Ftrrtf\Rollbar\ErrorHandler;
use Ftrrtf\Rollbar\Notifier;
use Ftrrtf\Rollbar;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RollbarListenerSpec extends ObjectBehavior
{
    public function let(
        Notifier $notifier,
        ErrorHandler $errorHandler,
        SecurityContextInterface $securityContext,
        Environment $environment
    ) {
        $notifier->getEnvironment()->willReturn($environment);
        $environment->setOption('person_callback', Argument::type('\Closure'))->shouldBeCalled();

        $this->beConstructedWith($notifier, $errorHandler, $securityContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Ftrrtf\RollbarBundle\EventListener\RollbarListener');
    }

    public function it_register_handlers_on_kernel_request(
        ErrorHandler $errorHandler,
        Notifier $notifier,
        GetResponseEvent $event
    ) {
        $errorHandler->registerErrorHandler($notifier)->shouldBeCalled();
        $errorHandler->registerShutdownHandler($notifier)->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    public function it_catch_exception(GetResponseForExceptionEvent $event, \Exception $exception)
    {
        $event->getException()->willReturn($exception);
        $this->onKernelException($event);
        $this->getException()->shouldReturn($exception);
    }

    public function it_skip_HTTP_exception(GetResponseForExceptionEvent $event, HttpException $exception)
    {
        $event->getException()->willReturn($exception);
        $this->onKernelException($event);
        $this->getException()->shouldReturn(null);
    }

    public function it_report_exception_on_kernel_response(
        Notifier $notifier,
        \Exception $exception,
        FilterResponseEvent $event
    ) {
        $notifier->reportException($exception)->shouldBeCalled();
        $this->setException($exception);
        $this->onKernelResponse($event);
        $this->getException()->shouldReturn(null);
    }

    public function it_skip_report_if_there_is_no_exception_on_kernel_response(
        Notifier $notifier,
        FilterResponseEvent $event
    ) {
        $this->setException(null);
        $notifier->reportException(Argument::any())->shouldNotBeCalled();
        $this->onKernelResponse($event);
    }

    public function it_get_user_data_if_user_is_defined(
        SecurityContextInterface $securityContext,
        TokenInterface $token,
        UserInterface $user
    ) {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $user->getUsername()->willReturn('user');

        $this->getUserData()->shouldReturn(array(
            'id' => 'user',
            'username' => 'user',
        ));
    }

    public function it_get_user_data_if_user_is_not_defined()
    {
        $this->getUserData()->shouldReturn(null);
    }

    public function it_get_user_id_and_email(
        SecurityContextInterface $securityContext,
        TokenInterface $token,
        ExtendedUserInterface $user
    ) {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $user->getId()->willReturn(123);
        $user->getUsername()->willReturn('user');
        $user->getEmail()->willReturn('mail@host');

        $this->getUserData()->shouldReturn(array(
            'id'       => 123,
            'username' => 'user',
            'email'     => 'mail@host',
        ));
    }
}

interface ExtendedUserInterface
{
    public function getId();
    public function getEmail();
    public function getUsername();
}
