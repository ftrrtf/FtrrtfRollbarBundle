<?php

namespace Ftrrtf\RollbarBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FtrrtfRollbarBundle
 *
 * @package Ftrrtf\RollbarBundle
 */
class FtrrtfRollbarBundle extends Bundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        parent::boot();

        if ($this->container->hasParameter('ftrrtf_rollbar.access_token')) {
            $container = $this->container;
            $accessToken = $container->getParameter('ftrrtf_rollbar.access_token');
            $host        = $container->getParameter('ftrrtf_rollbar.host');
            $branch      = $container->getParameter('ftrrtf_rollbar.branch');
            $hostEnv     = $container->getParameter('ftrrtf_rollbar.environment');
            $appEnv      = $container->getParameter('kernel.environment');


//            $getCurrentUser = function() use ($container) {
//                $token =  $container->get('security.context')->getToken();
//
//                if ($token) {
//                    $user = $token->getUser();

//                    return array(
//                        'id' => $user->getId(),
//                        'username' => $user->getUsername(),
//                        'email' => $user->getEmail()
//                    );
//                }
//
//                return null;
//            };


            // @todo get rid of the include
            include_once "rollbar.php";

            // Installs global error and exception handlers
            \Rollbar::init(array(
                'access_token' => $accessToken,
                'environment'  => sprintf('%s[%s]', $hostEnv, $appEnv),
                'host'         => $host,
                'branch'       => $branch,
//                'person_fn'    => $getCurrentUser
            ));
        }
    }

}
