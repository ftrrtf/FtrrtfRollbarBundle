<?php

namespace Ftrrtf\RollbarBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * FtrrtfRollbarExtension.
 */
class FtrrtfRollbarExtension extends Extension
{
    /**
     * {@inheritdoc}
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
                $container->setParameter(
                    'ftrrtf_rollbar.notifier.client.check_ignore_function_provider',
                    $config['notifier']['client']['check_ignore_function_provider']
                );
            }
            $loader->load('client.xml');
        }

        if (isset($config['notifier']['server'])) {
            if (isset($config['notifier']['server']['transport']['type'])) {
                $transport = $config['notifier']['server']['transport'];
                switch ($transport['type']) {
                    case 'agent':
                        $container->setParameter(
                            'ftrrtf_rollbar.transport.agent_log_location',
                            $transport['agent_log_location']
                        );
                        $loader->load('transport_agent.xml');
                        $this->prepareLogsDir(
                            $container->getParameterBag()->resolveValue($transport['agent_log_location'])
                        );
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

    /**
     * Create logs dir if does not exist.
     *
     * @param $logsDir
     *
     * @throws \RuntimeException
     */
    private function prepareLogsDir($logsDir)
    {
        if (!is_dir($logsDir)) {
            if (false === @mkdir($logsDir, 0777, true) && !is_dir($logsDir)) {
                throw new \RuntimeException(sprintf("Unable to create the logs directory (%s)\n", $logsDir));
            }
        } elseif (!is_writable($logsDir)) {
            throw new \RuntimeException(sprintf("Unable to write in the logs directory (%s)\n", $logsDir));
        }
    }
}
