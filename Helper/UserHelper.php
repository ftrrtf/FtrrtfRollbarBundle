<?php

namespace Ftrrtf\RollbarBundle\Helper;

class UserHelper
{
    /**
     * Get current user info.
     *
     * @param mixed|object $user
     *
     * @return null|array
     */
    public function buildUserData($user)
    {
        $userData = array();
        if(method_exists($user, 'getId')) 
        {
            $userData['id'] = $user->getId();  
        }
        else
        {
            $userData['id'] = $user->getUsername();
        }

        $userData['username'] = $user->getUsername();

        if (method_exists($user, 'getEmail')) {
            $userData['email'] = $user->getEmail();
        }

        return $userData;
    }
}
