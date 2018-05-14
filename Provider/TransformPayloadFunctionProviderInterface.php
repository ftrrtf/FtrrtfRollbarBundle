<?php

namespace Ftrrtf\RollbarBundle\Provider;

/**
 * Interface for "transform" function provider.
 */
interface TransformPayloadFunctionProviderInterface
{
    /**
     * @return string
     */
    public function getTransformFunctionCode();
}
