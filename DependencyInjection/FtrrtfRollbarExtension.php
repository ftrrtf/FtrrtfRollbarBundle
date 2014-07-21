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

        if (isset($config['notifier']['access_token'])) {

            if (isset($config['notifier']['transport']['type'])) {
                $transport = $config['notifier']['transport'];
                switch ($transport['type']) {
                    case 'agent':
                        $container->setParameter('ftrrtf_rollbar.notifier.transport.agent_log_location', $transport['agent_log_location']);
                        $loader->load('transport_agent.xml');
                        break;
                    case 'curl':
                    default:
                        $container->setParameter('ftrrtf_rollbar.notifier.transport.access_token', $transport['access_token']);
                        $loader->load('transport_curl.xml');
                }

                unset($config['notifier']['transport']);
            }

            $container->setParameter('ftrrtf_rollbar.notifier.options', $config['notifier']);
            $container->setParameter('ftrrtf_rollbar.environment.options', $config['environment']);

            $loader->load('services.xml');
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
