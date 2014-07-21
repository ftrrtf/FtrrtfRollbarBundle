<?php

namespace spec\Ftrrtf\RollbarBundle\EventListener;

use Ftrrtf\Rollbar\Environment;
use Ftrrtf\Rollbar\ErrorHandler;
use Ftrrtf\Rollbar\Notifier;
use Ftrrtf\Rollbar;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RollbarListenerSpec extends ObjectBehavior
{
    function let(
        Notifier $notifier,
        ErrorHandler $errorHandler,
        SecurityContextInterface $securityContext,
        Environment $environment
    ) {
        $notifier->getEnvironment()->willReturn($environment);
        $environment->setOption('person_callback', Argument::type('\Closure'))->shouldBeCalled();

        $this->beConstructedWith($notifier, $errorHandler, $securityContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Ftrrtf\RollbarBundle\EventListener\RollbarListener');
    }

    function it_register_handlers_on_kernel_request(
        ErrorHandler $errorHandler,
        Notifier $notifier,
        GetResponseEvent $event
    ) {
        $errorHandler->registerErrorHandler($notifier)->shouldBeCalled();
        $errorHandler->registerShutdownHandler($notifier)->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_catch_exception(GetResponseForExceptionEvent $event, \Exception $exception)
    {
        $event->getException()->willReturn($exception);
        $this->onKernelException($event);
        $this->getException()->shouldReturn($exception);
    }

    function it_skip_HTTP_exception(GetResponseForExceptionEvent $event, HttpException $httpException)
    {
        $event->getException()->willReturn($httpException);
        $this->onKernelException($event);
        $this->getException()->shouldReturn(null);
    }

    function it_report_exception_on_console_exception(Notifier $notifier, \Exception $exception, ConsoleExceptionEvent $event)
    {
        $this->setException($exception);
        $event->getException()->willReturn($exception);

        $notifier->reportException($exception)->shouldBeCalled();
        $this->onConsoleException($event);
    }

    function it_report_exception_on_kernel_response(Notifier $notifier, \Exception $exception, FilterResponseEvent $event)
    {
        $this->setException($exception);
        $notifier->reportException($exception)->shouldBeCalled();
        $this->onKernelResponse($event);
    }

    function it_clear_exception_after_report(Notifier $notifier, \Exception $exception, FilterResponseEvent $event)
    {
        $this->setException($exception);

        $notifier->reportException($exception)->shouldBeCalled();
        $this->onKernelResponse($event);

        $this->getException()->shouldReturn(null);
    }

    function it_skip_report_if_there_is_no_exception_on_kernel_response(Notifier $notifier, FilterResponseEvent $event)
    {
        $this->setException(null);
        $notifier->reportException(Argument::any())->shouldNotBeCalled();
        $this->onKernelResponse($event);
    }

    function it_get_user_data_if_user_is_a_string(
        SecurityContextInterface $securityContext,
        TokenInterface $token
    ) {
        $user = 'string_user';

        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->getUserData()->shouldReturn(array(
            'id' => 'string_user',
            'username' => 'string_user',
        ));
    }


    function it_get_user_data_if_user_is_simple_token_user(
        SecurityContextInterface $securityContext,
        TokenInterface $token,
        TokenUserInterface $user
    ) {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $user->__toString()->will(function() use ($user){
            return 'username';
        });

        $this->getUserData()->shouldReturn(array(
            'id' => 'username',
            'username' => 'username',
        ));
    }

    function it_get_user_data_if_user_is_not_defined()
    {
        $this->getUserData()->shouldReturn(null);
    }

    function it_get_user_data_if_user_with_id_and_email(
        SecurityContextInterface $securityContext,
        TokenInterface $token,
        ExtendedUserInterface $user
    ) {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $user->getId()->willReturn(123);
        $user->getEmail()->willReturn('mail@host');
        $user->__toString()->willReturn('username');

        $this->getUserData()->shouldReturn(array(
            'id'       => 123,
            'username' => 'username',
            'email'    => 'mail@host',
        ));
    }
}

interface TokenUserInterface
{
    function getUsername();
    function __toString();
}

interface ExtendedUserInterface
{
    function getId();
    function getEmail();
    function __toString();
}
