<?php

namespace Ftrrtf\RollbarBundle\Tests\DependencyInjection;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

class FtrrtfRollbarExtensionTest extends AbstractExtensionTest
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $fs;

    private static $parameters = array();

    protected function setUp()
    {
        $this->fs = vfsStream::setup('logs');

        static::$parameters = array(
            'kernel.logs_dir' => $this->fs->url(),
            'kernel.environment' => 'test',
            'rollbar_environment' => 'staging',
            'rollbar_server_access_token' => 'server_token',
            'rollbar_client_access_token' => 'client_token',
        );
    }

    public function testEnvironment()
    {
        $container = $this->createContainerFromFile('full', static::$parameters);

        $this->assertParameter(
            $container,
            array(
                'environment' => 'staging[test]',
                'branch' => 'feature',
                'root_dir' => 'path/to',
                'framework' => 'sf',
                'code_version' => 'somehash',
            ),
            'ftrrtf_rollbar.environment.options'
        );

        $this->assertHasDefinition($container, 'ftrrtf_rollbar.environment');
        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.environment'),
            array(
                '%ftrrtf_rollbar.environment.options%',
            )
        );
    }

    public function testServerNotifier()
    {
        $container = $this->createContainerFromFile('full', static::$parameters);

        $this->assertParameter(
            $container,
            array(
                'batched' => true,
                'batch_size' => 50,
            ),
            'ftrrtf_rollbar.notifier.server.options'
        );

        $this->assertHasDefinition($container, 'ftrrtf_rollbar.notifier');
        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.notifier'),
            array(
                new Reference('ftrrtf_rollbar.environment', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, false),
                new Reference('ftrrtf_rollbar.transport'),
                '%ftrrtf_rollbar.notifier.server.options%',
            )
        );
    }

    public function testListener()
    {
        $container = $this->createContainerFromFile('full', static::$parameters);

        $this->assertHasDefinition($container, 'ftrrtf_rollbar.helper.user');
        $this->assertHasDefinition($container, 'ftrrtf_rollbar.error_handler');
        $this->assertHasDefinition($container, 'ftrrtf_rollbar.rollbar_listener');

        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.rollbar_listener'),
            array(
                new Reference('ftrrtf_rollbar.notifier'),
                new Reference('ftrrtf_rollbar.error_handler'),
                new Reference('security.token_storage'),
                new Reference('security.authorization_checker'),
                new Reference('ftrrtf_rollbar.helper.user'),
            )
        );

        $this->assertDICTags(
            $container->getDefinition('ftrrtf_rollbar.rollbar_listener'),
            array(
                'kernel.event_listener' => array(
                    array(
                        'event' => 'kernel.exception',
                        'method' => 'onKernelException',
                        'priority' => -100,
                    ),
                    array(
                        'event' => 'kernel.response',
                        'method' => 'onKernelResponse',
                        'priority' => -200,
                    ),
                    array(
                        'event' => 'kernel.request',
                        'method' => 'onKernelRequest',
                    ),
                    array(
                        'event' => 'console.exception',
                        'method' => 'onConsoleException',
                    ),
                ),
            )
        );
    }

    public function testClientNotifier()
    {
        $container = $this->createContainerFromFile('full', static::$parameters);

        $this->assertParameter(
            $container,
            array(
                'code_version' => 'somehash',
                'access_token' => 'client_token',
                'source_map_enabled' => true,
                'guess_uncaught_frames' => true,
                'rollbarjs_version' => 'v1.7',
                'allowed_js_hosts' => array(
                    'http://myhost.mydomain.com',
                    'http://myhost2.mydomain.com',
                ),
                'check_ignore_function_provider' => 'ftrrtf_rollbar.check_ignore_function_provider.default'
            ),
            'ftrrtf_rollbar.notifier.client.options'
        );

        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.twig_extension'),
            array(
                '%ftrrtf_rollbar.notifier.client.options%',
                '%ftrrtf_rollbar.environment.options%',
                new Reference('ftrrtf_rollbar.helper.user'),
                new Expression("service(parameter('ftrrtf_rollbar.notifier.client.check_ignore_function_provider'))"),
            )
        );

        $this->assertDICTags(
            $container->getDefinition('ftrrtf_rollbar.twig_extension'),
            array(
                'twig.extension' => array(array()),
            )
        );
    }

    public function testServerNotifierTransportCurl()
    {
        $container = $this->createContainerFromFile('full', static::$parameters);

        $this->assertParameter(
            $container,
            'server_token',
            'ftrrtf_rollbar.transport.access_token'
        );

        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.transport'),
            array(
                '%ftrrtf_rollbar.transport.access_token%',
            )
        );

        $this->assertParameter(
            $container,
            'Ftrrtf\Rollbar\Transport\Curl',
            'ftrrtf_rollbar.transport.class'
        );
    }

    public function testServerNotifierTransportAgent()
    {
        $container = $this->createContainerFromFile('agent', static::$parameters);

        $this->assertParameter(
            $container,
            'vfs://logs/test.rollbar',
            'ftrrtf_rollbar.transport.agent_log_location'
        );
        static::assertTrue($this->fs->hasChild('test.rollbar'));

        $this->assertDICConstructorArguments(
            $container->getDefinition('ftrrtf_rollbar.transport'),
            array(
                '%ftrrtf_rollbar.transport.agent_log_location%',
            )
        );

        $this->assertParameter(
            $container,
            'Ftrrtf\Rollbar\Transport\Agent',
            'ftrrtf_rollbar.transport.class'
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to create the logs directory (vfs://logs/test.rollbar)
     */
    public function testServerNotifierTransportAgentMkDirFailure()
    {
        $this->fs->chmod(0);
        $this->createContainerFromFile('agent', static::$parameters, true);
    }

    protected function tearDown()
    {
        vfsStreamWrapper::unregister();
        $this->fs = null;
    }
}
