<?php

namespace spec\Ftrrtf\RollbarBundle\Helper;

use Ftrrtf\RollbarBundle\Helper\UserHelper;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @mixin UserHelper
 */
class UserHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UserHelper::class);
    }

    function it_builds_user_data_if_user_is_a_string() {
        $user = 'string_user';

        $this->buildUserData($user)->shouldReturn(array(
            'id' => 'string_user'
        ));
    }

    function it_gets_user_data_if_user_is_a_simple_user(UserInterface $user) {
        $user->getUsername()->willReturn('username');

        $this->buildUserData($user)->shouldReturn(array(
            'id' => 'username',
            'username' => 'username',
        ));
    }

    function it_gets_user_data_if_user_with_id_and_email(ExtendedUser $user) {
        $user->getId()->willReturn(123);
        $user->getUsername()->willReturn('username');
        $user->getEmail()->willReturn('mail@host');

        $this->buildUserData($user)->shouldReturn(array(
            'id' => 123,
            'username' => 'username',
            'email' => 'mail@host',
        ));
    }
}

abstract class ExtendedUser implements UserInterface
{
    function getId(){}
    function getEmail(){}
    function getUsername(){}
}
