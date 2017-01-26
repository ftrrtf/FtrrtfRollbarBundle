<?php

namespace spec\Ftrrtf\RollbarBundle\Provider;

use Ftrrtf\RollbarBundle\Provider\CheckIgnoreFunctionProviderInterface;
use Ftrrtf\RollbarBundle\Provider\NullCheckIgnoreFunctionProvider;
use PhpSpec\ObjectBehavior;

/**
 * @mixin NullCheckIgnoreFunctionProvider
 */
class NullCheckIgnoreFunctionProviderSpec extends ObjectBehavior
{
    const EXPECTED_EMPTY_FUNCTION = 'function () { return false; }';

    function it_is_initializable()
    {
        $this->shouldHaveType(NullCheckIgnoreFunctionProvider::class);
        $this->shouldHaveType(CheckIgnoreFunctionProviderInterface::class);
    }

    function it_returns_empty_function()
    {
        $this->getCheckIgnoreFunctionCode()->shouldReturn(self::EXPECTED_EMPTY_FUNCTION);
    }
}
