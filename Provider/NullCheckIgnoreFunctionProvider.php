<?php

namespace Ftrrtf\RollbarBundle\Provider;

class NullCheckIgnoreFunctionProvider implements CheckIgnoreFunctionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCheckIgnoreFunctionCode()
    {
        return 'function () { return false; }';
    }
}
