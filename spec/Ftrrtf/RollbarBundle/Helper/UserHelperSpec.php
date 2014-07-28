<?php

namespace spec\Ftrrtf\RollbarBundle\Helper;

use PhpSpec\ObjectBehavior;

class UserHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ftrrtf\RollbarBundle\Helper\UserHelper');
    }

    function it_should_build_user_data_if_user_is_a_string() {
        $user = 'string_user';

        $this->buildUserData($user)->shouldReturn(array(
            'id' => 'string_user',
            'username' => 'string_user',
        ));
    }

    function it_get_user_data_if_user_is_simple_token_user(TokenUserInterface $user) {
        $user->__toString()->willReturn('username');

        $this->buildUserData($user)->shouldReturn(array(
            'id' => 'username',
            'username' => 'username',
        ));
    }

    function it_get_user_data_if_user_with_id_and_email(
        ExtendedUserInterface $user
    ) {
        $user->getId()->willReturn(123);
        $user->getEmail()->willReturn('mail@host');
        $user->__toString()->willReturn('username');

        $this->buildUserData($user)->shouldReturn(array(
            'id'       => 123,
            'username' => 'username',
            'email'    => 'mail@host',
        ));
    }

}

interface TokenUserInterface
{
    function getUsername();
    function __toString();
}

interface ExtendedUserInterface
{
    function getId();
    function getEmail();
    function __toString();
}
