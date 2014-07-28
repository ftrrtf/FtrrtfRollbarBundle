<?php

namespace Ftrrtf\RollbarBundle\Rollbar;

use Ftrrtf\Rollbar\Environment as BaseEnvironment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class Request
 *
 * @package Ftrrtf\Rollbar
 */
class Environment extends BaseEnvironment
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Cached values for request
     *
     * @return array|null
     */
    public function getRequestData()
    {
        parent::getRequestData();

        if ($this->getRequest() instanceof Request) {
            if (in_array($this->getRequest()->getMethod(), array('PUT', 'DELETE'))) {
                $this->requestData[$this->getRequest()->getMethod()] = $this->getRequest()->request->all();
            }
        }

        return $this->requestData;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->replaceDefaults(
            array(
                'framework' => Kernel::VERSION
            )
        );
    }
}
