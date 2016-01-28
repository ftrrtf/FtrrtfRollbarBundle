<?php

namespace Ftrrtf\RollbarBundle\Provider;

/**
 * Interface for "check ignore" function provider.
 */
interface CheckIgnoreFunctionProviderInterface
{
    /**
     * @return string
     */
    public function getCheckIgnoreFunctionCode();
}
