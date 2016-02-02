<?php

namespace spec\Ftrrtf\RollbarBundle\Twig;

use Ftrrtf\RollbarBundle\Tests\Fake\Application;
use Ftrrtf\RollbarBundle\Helper\UserHelper;
use Ftrrtf\RollbarBundle\Provider\CheckIgnoreFunctionProviderInterface;
use Ftrrtf\RollbarBundle\Twig\RollbarExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin RollbarExtension
 */
class RollbarExtensionSpec extends ObjectBehavior
{
    function let(UserHelper $helper, CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider)
    {
        $this->beConstructedWith([], [], $helper, $checkIgnoreFunctionProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Ftrrtf\RollbarBundle\Twig\RollbarExtension');
    }

    function it_uses_the_newest_version_of_rollbarjs(UserHelper $helper, Application $app, CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider)
    {
        $this->beConstructedWith(
            [
                'access_token' => 'access_token',
                'source_map_enabled' => false,
                'allowed_js_hosts' => [],
                'check_ignore_function_provider' => null,
                'rollbarjs_version' => 'v1',
            ],
            [
                'environment' => 'test',
            ],
            $helper,
            $checkIgnoreFunctionProvider
        );

        $this->getInitRollbarCode(['app' => $app])->shouldMatch('/v1/');
    }

    function it_allows_to_select_rollbarjs_version(UserHelper $helper, Application $app, CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider)
    {
        $this->beConstructedWith(
            [
                'access_token' => 'access_token',
                'source_map_enabled' => false,
                'allowed_js_hosts' => [],
                'check_ignore_function_provider' => null,
                'rollbarjs_version' => 'v1.7',
            ],
            [
                'environment' => 'test',
            ],
            $helper,
            $checkIgnoreFunctionProvider
        );

        $this->getInitRollbarCode(['app' => $app])->shouldMatch('/v1.7/');
    }
}
