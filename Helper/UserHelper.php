<?php

namespace Ftrrtf\RollbarBundle\Helper;

use Symfony\Component\Security\Core\User\UserInterface;

class UserHelper
{
    /**
     * Get current user info.
     *
     * @param string|UserInterface $user
     *
     * @return null|array
     */
    public function buildUserData($user)
    {
        if (is_string($user)) {
            return array(
                'id' => $user,
            );
        }

        if (!($user instanceof UserInterface)) {
            return array();
        }

        $userData['id'] = method_exists($user, 'getId')
            ? $user->getId()
            : $user->getUsername();

        $userData['username'] = $user->getUsername();

        if (method_exists($user, 'getEmail')) {
            $userData['email'] = $user->getEmail();
        }

        return $userData;
    }
}
