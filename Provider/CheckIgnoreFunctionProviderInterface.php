<?php

namespace Ftrrtf\RollbarBundle\Provider;

interface CheckIgnoreFunctionProviderInterface
{
    /**
     * @return string
     */
    public function getCheckIgnoreFunctionCode();
}
