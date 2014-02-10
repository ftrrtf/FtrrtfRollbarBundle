<?php

namespace Ftrrtf\RollbarBundle\Twig;

/**
 * Class RollbarExtension
 *
 * @package Ftrrtf\RollbarBundle\Twig
 */
class RollbarExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $notifierOptions;

    /**
     * @var array
     */
    protected $environmentOptions;

    /**
     * @param $notifierOptions
     * @param $environmentOptions
     */
    public function __construct($notifierOptions, $environmentOptions)
    {
        $this->notifierOptions    = $notifierOptions;
        $this->environmentOptions = $environmentOptions;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'rollbarjs' => new \Twig_Function_Method(
                $this,
                'getInitRollbarCode',
                array(
                    'needs_context' => true,
                    'is_safe' => array('html')
                )
            )
        );
    }

    /**
     * @param array $context
     *
     * @return string
     */
    public function getInitRollbarCode(array $context)
    {
        $accessToken = $this->notifierOptions['client_access_token'];

        $params = array(
            'server.environment' => $this->environmentOptions['environment']
        );

        $user   = $context['app']->getUser();
        if (isset($user)) {
            $params['person'] = array(
                'id'       => method_exists($user, 'getId') ? $user->getId() : $user->getUsername(),
                'username' => $user->getUsername(),
                'email'    => method_exists($user, 'getEmail') ? $user->getEmail() : ''
            );
        }

        $params = json_encode($params);
        return <<<END_HTML
<script>
var _rollbarParams = {$params};
_rollbarParams["notifier.snippet_version"] = "2"; var _rollbar=["{$accessToken}", _rollbarParams]; var _ratchet=_rollbar;
(function(w,d){w.onerror=function(e,u,l,c,err){_rollbar.push({_t:'uncaught',e:e,u:u,l:l,c:c,err:err});};var i=function(){var s=d.createElement("script");var
f=d.getElementsByTagName("script")[0];s.src="//d37gvrvc0wt4s1.cloudfront.net/js/1/rollbar.min.js";s.async=!0;
f.parentNode.insertBefore(s,f);};if(w.addEventListener){w.addEventListener("load",i,!1);}else{w.attachEvent("onload",i);}})(window,document);
</script>
END_HTML;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ftrrtf_rollbar';
    }
}
