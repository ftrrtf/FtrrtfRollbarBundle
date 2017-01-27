<?php

namespace Ftrrtf\RollbarBundle\Provider;

/**
 * Skip checking.
 */
class NullCheckIgnoreFunctionProvider implements CheckIgnoreFunctionProviderInterface
{
    public function getCheckIgnoreFunctionCode()
    {
        return 'function () { return false; }';
    }
}
