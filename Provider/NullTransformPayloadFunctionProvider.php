<?php

namespace Ftrrtf\RollbarBundle\Provider;

class NullTransformPayloadFunctionProvider implements TransformPayloadFunctionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTransformFunctionCode()
    {
        return 'function (payload) { return payload; }';
    }
}
