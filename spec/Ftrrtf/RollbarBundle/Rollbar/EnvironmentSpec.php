<?php

namespace spec\Ftrrtf\RollbarBundle\Rollbar;

use Ftrrtf\Rollbar\Environment as BaseEnvironment;
use Ftrrtf\RollbarBundle\Rollbar\Environment;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @mixin Environment
 */
class EnvironmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Environment::class);
        $this->shouldHaveType(BaseEnvironment::class);
    }

    function it_returns_request_object(Request $request)
    {
        $this->setRequest($request);

        $this->getRequest()->shouldReturn($request);
    }

    function it_returns_additional_request_data_when_put_request_is_used()
    {
        $request = new Request(
            $get = [],
            $post = ['id' => 15, 'name' => 'some name'],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = ['REQUEST_METHOD' => 'PUT']
        );

        $this->setRequest($request);

        $this->getRequestData()->shouldReturn(['PUT' => $post]);
    }

    function it_returns_additional_request_data_when_delete_request_is_used()
    {
        $request = new Request(
            $get = [],
            $post = ['id' => 55],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = ['REQUEST_METHOD' => 'DELETE']
        );

        $this->setRequest($request);

        $this->getRequestData()->shouldReturn(['DELETE' => $post]);
    }

    function it_does_not_return_any_additional_request_data_when_any_other_request_type_is_used()
    {
        $request = new Request(
            $get = [],
            $post = ['id' => 33],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = ['REQUEST_METHOD' => 'POST']
        );

        $this->setRequest($request);

        $this->getRequestData()->shouldReturn(null);
    }
    
    function it_returns_framework_kernel_version()
    {
        $this->getFramework()->shouldReturn(Kernel::VERSION);
    }

    function it_anonymizes_user_ip()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->beConstructedWith([
            'anonymize' => true,
        ]);

        $this->getUserIP()->shouldReturn(null);
    }

    function it_returns_user_ip()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->getUserIP()->shouldReturn('127.0.0.1');
    }

    function it_anonymizes_personal_data()
    {
        $this->beConstructedWith([
            'anonymize' => true,
        ]);

        $this->getPersonData()->shouldReturn(null);
    }

    function it_returns_personal_data()
    {
        $this->beConstructedWith([
            'person' => [
                'id' => '12345',
            ],
        ]);

        $this->getPersonData()->shouldNotReturn(null);
    }
}
