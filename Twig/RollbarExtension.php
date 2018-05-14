<?php

namespace Ftrrtf\RollbarBundle\Twig;

use Ftrrtf\RollbarBundle\Helper\UserHelper;
use Ftrrtf\RollbarBundle\Provider\CheckIgnoreFunctionProviderInterface;
use Ftrrtf\RollbarBundle\Provider\TransformPayloadFunctionProviderInterface;

/**
 * Rollbar twig extension.
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
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var CheckIgnoreFunctionProviderInterface
     */
    private $checkIgnoreFunctionProvider;

    /**
     * @var TransformPayloadFunctionProviderInterface
     */
    private $transformPayloadFunctionProvider;

    /**
     * @param array                                     $notifierOptions
     * @param array                                     $environmentOptions
     * @param UserHelper                                $userHelper
     * @param CheckIgnoreFunctionProviderInterface      $checkIgnoreFunctionProvider
     * @param TransformPayloadFunctionProviderInterface $transformPayloadFunctionProvider
     */
    public function __construct(
        array $notifierOptions,
        array $environmentOptions,
        UserHelper $userHelper,
        CheckIgnoreFunctionProviderInterface $checkIgnoreFunctionProvider,
        TransformPayloadFunctionProviderInterface $transformPayloadFunctionProvider
    ) {
        $this->notifierOptions = $notifierOptions;
        $this->environmentOptions = $environmentOptions;
        $this->userHelper = $userHelper;
        $this->checkIgnoreFunctionProvider = $checkIgnoreFunctionProvider;
        $this->transformPayloadFunctionProvider = $transformPayloadFunctionProvider;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'rollbarjs',
                array($this, 'getInitRollbarCode'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * @param array $context
     *
     * @return string
     */
    public function getInitRollbarCode(array $context)
    {
        $accessToken = $this->notifierOptions['access_token'];

        $config = array(
            'accessToken' => $accessToken,
            'captureUncaught' => true,
            'payload' => array(
                'environment' => $this->environmentOptions['environment'],
            ),
        );

        $user = $context['app']->getUser();
        if (isset($user)) {
            $config['payload']['person'] = $this->userHelper->buildUserData($user);
        }

        if ($this->notifierOptions['source_map_enabled']) {
            $config['payload']['client'] = array(
                'javascript' => array(
                    'source_map_enabled' => $this->notifierOptions['source_map_enabled'],
                    'code_version' => $this->notifierOptions['code_version'],
                    'guess_uncaught_frames' => $this->notifierOptions['guess_uncaught_frames'],
                ),
            );
        }

        $rollbarJsVersion = $this->notifierOptions['rollbarjs_version'];

        $config = json_encode($config);

        $checkIgnoreConfig = $this->getCheckIgnoreConfig();

        $transform = $this->transformPayloadFunctionProvider->getTransformFunctionCode();

        return <<<END_HTML
<script>
var _rollbarConfig = {$config};
_rollbarConfig.transform = {$transform};
// Rollbar Snippet
!function(r){function o(e){if(t[e])return t[e].exports;var n=t[e]={exports:{},id:e,loaded:!1};return r[e].call(n.exports,n,n.exports,o),n.loaded=!0,n.exports}var t={};return o.m=r,o.c=t,o.p="",o(0)}([function(r,o,t){"use strict";var e=t(1).Rollbar,n=t(2);_rollbarConfig.rollbarJsUrl=_rollbarConfig.rollbarJsUrl||"https://d37gvrvc0wt4s1.cloudfront.net/js/{$rollbarJsVersion}/rollbar.min.js";var a=e.init(window,_rollbarConfig),i=n(a,_rollbarConfig);a.loadFull(window,document,!_rollbarConfig.async,_rollbarConfig,i)},function(r,o){"use strict";function t(r){return function(){try{return r.apply(this,arguments)}catch(o){try{console.error("[Rollbar]: Internal error",o)}catch(t){}}}}function e(r,o,t){window._rollbarWrappedError&&(t[4]||(t[4]=window._rollbarWrappedError),t[5]||(t[5]=window._rollbarWrappedError._rollbarContext),window._rollbarWrappedError=null),r.uncaughtError.apply(r,t),o&&o.apply(window,t)}function n(r){var o=function(){var o=Array.prototype.slice.call(arguments,0);e(r,r._rollbarOldOnError,o)};return o.belongsToShim=!0,o}function a(r){this.shimId=++s,this.notifier=null,this.parentShim=r,this._rollbarOldOnError=null}function i(r){var o=a;return t(function(){if(this.notifier)return this.notifier[r].apply(this.notifier,arguments);var t=this,e="scope"===r;e&&(t=new o(this));var n=Array.prototype.slice.call(arguments,0),a={shim:t,method:r,args:n,ts:new Date};return window._rollbarShimQueue.push(a),e?t:void 0})}function l(r,o){if(o.hasOwnProperty&&o.hasOwnProperty("addEventListener")){var t=o.addEventListener;o.addEventListener=function(o,e,n){t.call(this,o,r.wrap(e),n)};var e=o.removeEventListener;o.removeEventListener=function(r,o,t){e.call(this,r,o&&o._wrapped?o._wrapped:o,t)}}}var s=0;a.init=function(r,o){var e=o.globalAlias||"Rollbar";if("object"==typeof r[e])return r[e];r._rollbarShimQueue=[],r._rollbarWrappedError=null,o=o||{};var i=new a;return t(function(){if(i.configure(o),o.captureUncaught){i._rollbarOldOnError=r.onerror,r.onerror=n(i);var t,a,s="EventTarget,Window,Node,ApplicationCache,AudioTrackList,ChannelMergerNode,CryptoOperation,EventSource,FileReader,HTMLUnknownElement,IDBDatabase,IDBRequest,IDBTransaction,KeyOperation,MediaController,MessagePort,ModalWindow,Notification,SVGElementInstance,Screen,TextTrack,TextTrackCue,TextTrackList,WebSocket,WebSocketWorker,Worker,XMLHttpRequest,XMLHttpRequestEventTarget,XMLHttpRequestUpload".split(",");for(t=0;t<s.length;++t)a=s[t],r[a]&&r[a].prototype&&l(i,r[a].prototype)}return r[e]=i,i})()},a.prototype.loadFull=function(r,o,e,n,a){var i=function(){var o;if(void 0===r._rollbarPayloadQueue){var t,e,n,i;for(o=new Error("rollbar.js did not load");t=r._rollbarShimQueue.shift();)for(n=t.args,i=0;i<n.length;++i)if(e=n[i],"function"==typeof e){e(o);break}}"function"==typeof a&&a(o)},l=!1,s=o.createElement("script"),u=o.getElementsByTagName("script")[0],p=u.parentNode;s.crossOrigin="",s.src=n.rollbarJsUrl,s.async=!e,s.onload=s.onreadystatechange=t(function(){if(!(l||this.readyState&&"loaded"!==this.readyState&&"complete"!==this.readyState)){s.onload=s.onreadystatechange=null;try{p.removeChild(s)}catch(r){}l=!0,i()}}),p.insertBefore(s,u)},a.prototype.wrap=function(r,o){try{var t;if(t="function"==typeof o?o:function(){return o||{}},"function"!=typeof r)return r;if(r._isWrap)return r;if(!r._wrapped){r._wrapped=function(){try{return r.apply(this,arguments)}catch(o){throw o._rollbarContext=t()||{},o._rollbarContext._wrappedSource=r.toString(),window._rollbarWrappedError=o,o}},r._wrapped._isWrap=!0;for(var e in r)r.hasOwnProperty(e)&&(r._wrapped[e]=r[e])}return r._wrapped}catch(n){return r}};for(var u="log,debug,info,warn,warning,error,critical,global,configure,scope,uncaughtError".split(","),p=0;p<u.length;++p)a.prototype[u[p]]=i(u[p]);r.exports={Rollbar:a,_rollbarWindowOnError:e}},function(r,o){"use strict";r.exports=function(r,o){return function(t){if(!t&&!window._rollbarInitialized){var e=window.RollbarNotifier,n=o||{},a=n.globalAlias||"Rollbar",i=window.Rollbar.init(n,r);i._processShimQueue(window._rollbarShimQueue||[]),window[a]=i,window._rollbarInitialized=!0,e.processPayloads()}}}}]);
// End Rollbar Snippet
{$checkIgnoreConfig}
</script>
END_HTML;
    }

    /**
     * Configure and return JS for "ignore errors" feature.
     *
     * @return string
     */
    protected function getCheckIgnoreConfig()
    {
        $allowedHosts = json_encode($this->notifierOptions['allowed_js_hosts']);
        $customCheckIgnoreFunction = $this->checkIgnoreFunctionProvider->getCheckIgnoreFunctionCode();

        return <<<END_HTML
(function(Rollbar) {
    var allowedHosts = {$allowedHosts};
    var customCheckIgnoreFunction = {$customCheckIgnoreFunction};
    if (allowedHosts.length === 0) {
        allowedHosts.push(window.location.origin);
    }

    function isFromAllowedHosts(filename) {
        for (var i = 0; i < allowedHosts.length; i++) {
            if (filename.match(allowedHosts[i])) {
                return true;
            }
        }

        return false;
    }

    function isLogMessage(payload) {
        try {
            if (payload.data.body.message !== undefined) {
                return true;
            }
        } catch (e) {
        }

        return false;
    }

    function ignoreRemoteUncaught(isUncaught, args, payload) {
        try {
            if (typeof customCheckIgnoreFunction === 'function' && customCheckIgnoreFunction(isUncaught, args, payload)) {
                return true;
            }

            //this prevents breaking simple string reporting
            if (isLogMessage(payload)) {
                return false;
            }

            var filename = payload.data.body.trace.frames[0].filename;
            if (isUncaught && !isFromAllowedHosts(filename)) {
                return true;
            }
        } catch (e) {
            // Most likely there was no filename or the frame doesn't exist.
            return true;
        }

        return false;
    }

    Rollbar.configure({checkIgnore: ignoreRemoteUncaught});
})(Rollbar);
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
