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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['access_token'])) {
            $loader->load('services.yml');
            $container->setParameter('ftrrtf_rollbar.access_token', $config['access_token']);
            $container->setParameter('ftrrtf_rollbar.host', $config['host']);
            $container->setParameter('ftrrtf_rollbar.environment', $config['environment']);
            $container->setParameter('ftrrtf_rollbar.root_dir', $config['root_dir']);
            $container->setParameter('ftrrtf_rollbar.branch', $config['branch']);
//            if ($config['git_branch_auto_detect']) {
//                $gitHeadFile = $config['root_dir'] . '.git/HEAD';
//                if (file_exists($gitHeadFile)) {
//                    $parts = explode('/', file_get_contents($gitHeadFile));
//                    $detectedBranch = trim(array_pop($parts));
//                    $container->setParameter('ftrrtf_rollbar.branch', $detectedBranch);
//                }
//            }
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
