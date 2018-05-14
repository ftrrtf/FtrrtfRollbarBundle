<?php

namespace spec\Ftrrtf\RollbarBundle\Twig;

use Ftrrtf\RollbarBundle\Provider\TransformPayloadFunctionProviderInterface;
use Ftrrtf\RollbarBundle\Tests\Fake\Application;
use Ftrrtf\RollbarBundle\Helper\UserHelper;
use Ftrrtf\RollbarBundle\Provider\CheckIgnoreFunctionProviderInterface;
use Ftrrtf\RollbarBundle\Twig\RollbarExtension;
use PhpSpec\ObjectBehavior;
use Twig_Extension as TwigExtension;

/**
 * @mixin RollbarExtension
 */
class RollbarExtensionSpec extends ObjectBehavior
{
    const EXPECTED_EXTENSION_NAME = 'ftrrtf_rollbar';

    function let(
        UserHelper $helper,
        CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider,
        TransformPayloadFunctionProviderInterface $transformPayloadFunctionProvider
    ) {
        $this->beConstructedWith(
            [],
            [],
            $helper,
            $checkIgnoreFunctionProvider,
            $transformPayloadFunctionProvider
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RollbarExtension::class);
        $this->shouldHaveType(TwigExtension::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn(self::EXPECTED_EXTENSION_NAME);
    }

    function it_uses_the_newest_version_of_rollbarjs(
        UserHelper $helper,
        Application $app,
        CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider,
        TransformPayloadFunctionProviderInterface $transformPayloadFunctionProvider
    ) {
        $this->beConstructedWith(
            [
                'access_token' => 'access_token',
                'source_map_enabled' => false,
                'allowed_js_hosts' => [],
                'check_ignore_function_provider' => null,
                'transform_payload_function_provider' => null,
                'rollbarjs_version' => 'v1',
            ],
            [
                'environment' => 'test',
            ],
            $helper,
            $checkIgnoreFunctionProvider,
            $transformPayloadFunctionProvider
        );

        $this->getInitRollbarCode(['app' => $app])->shouldMatch('/v1/');
    }

    function it_allows_to_select_rollbarjs_version(
        UserHelper $helper,
        Application $app,
        CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider,
        TransformPayloadFunctionProviderInterface $transformPayloadFunctionProvider
    ) {
        $this->beConstructedWith(
            [
                'access_token' => 'access_token',
                'source_map_enabled' => false,
                'allowed_js_hosts' => [],
                'check_ignore_function_provider' => null,
                'transform_payload_function_provider' => null,
                'rollbarjs_version' => 'v1.7',
            ],
            [
                'environment' => 'test',
            ],
            $helper,
            $checkIgnoreFunctionProvider,
            $transformPayloadFunctionProvider
        );

        $this->getInitRollbarCode(['app' => $app])->shouldMatch('/v1.7/');
    }
}
