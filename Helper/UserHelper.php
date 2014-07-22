<?php

namespace Ftrrtf\RollbarBundle\Helper;

class UserHelper {

    /**
     * Get current user info
     *
     * @param mixed $user
     *
     * @return null|array
     */
    public function buildUserData($user)
    {
        $userData = array();
        $userData['id'] = method_exists($user, 'getId')
            ? $user->getId()
            : (string) $user;

        $userData['username'] = (string) $user;

        if (method_exists($user, 'getEmail')) {
            $userData['email'] = $user->getEmail();
        }

        return $userData;
    }
}