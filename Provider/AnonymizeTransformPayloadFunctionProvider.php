<?php

namespace Ftrrtf\RollbarBundle\Provider;

class AnonymizeTransformPayloadFunctionProvider implements TransformPayloadFunctionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTransformFunctionCode()
    {
        return 'function (payload) {
  payload.data.person = {};
  payload.data.request.user_ip = \'\';
  return payload;
}';
    }
}
