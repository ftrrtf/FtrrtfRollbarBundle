<?php

namespace Ftrrtf\RollbarBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FtrrtfRollbarExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['notifier'])) {
            $loader->load('services.xml');
            $container->setParameter('ftrrtf_rollbar.environment.options', $config['environment']);
        }

        if (isset($config['notifier']['client'])) {
            $container->setParameter('ftrrtf_rollbar.notifier.client.options', $config['notifier']['client']);
            if (isset($config['notifier']['client']['check_ignore_function_provider'])) {
                $container->setParameter('ftrrtf_rollbar.notifier.client.check_ignore_function_provider', $config['notifier']['client']['check_ignore_function_provider']);
            }
            $loader->load('client.xml');
        }


        if (isset($config['notifier']['server'])) {
            if (isset($config['notifier']['server']['transport']['type'])) {
                $transport = $config['notifier']['server']['transport'];
                switch ($transport['type']) {
                    case 'agent':
                        $container->setParameter('ftrrtf_rollbar.transport.agent_log_location', $transport['agent_log_location']);
                        $loader->load('transport_agent.xml');

                        // Prepare log dir
                        $logDir = $container->getParameterBag()->resolveValue($transport['agent_log_location']);
                        if (!is_dir($logDir)) {
                            if (false === @mkdir($logDir, 0777, true)) {
                                throw new \RuntimeException(sprintf('Could not create log directory "%s".', $logDir));
                            }
                        }
                        break;
                    case 'curl':
                    default:
                        $container->setParameter('ftrrtf_rollbar.transport.access_token', $transport['access_token']);
                        $loader->load('transport_curl.xml');
                }

                unset($config['notifier']['server']['transport']);
            }

            $container->setParameter('ftrrtf_rollbar.notifier.server.options', $config['notifier']['server']);

            $loader->load('server.xml');
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'ftrrtf_rollbar';
    }
}
