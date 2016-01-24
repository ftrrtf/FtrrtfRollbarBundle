<?php

namespace Ftrrtf\RollbarBundle\Tests\DependencyInjection;

use Ftrrtf\RollbarBundle\DependencyInjection\FtrrtfRollbarExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class AbstractExtensionTest extends \PHPUnit_Framework_TestCase
{
    private static $containerCache = array();
    
    protected function createContainerFromFile($fileName, $parameters = array(), $resetContainer = false)
    {
        $cacheKey = md5($fileName . serialize($parameters));
        if (!$resetContainer && isset(self::$containerCache[$cacheKey])) {
            return self::$containerCache[$cacheKey];
        }
        $container = $this->createContainer($parameters);
        $container->registerExtension(new FtrrtfRollbarExtension());
        $this->loadFromFile($container, $fileName);
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return self::$containerCache[$cacheKey] = $container;
    }

    private function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures/yml'));
        $loader->load($file . '.yml');
    }

    private function createContainer(array $parameters = array())
    {
        return new ContainerBuilder(
            new ParameterBag($parameters)
        );
    }

    protected function assertParameter(ContainerBuilder $container, $expected, $key)
    {
        $parameter = $container->getParameter($key);

        static::assertEquals($expected, $parameter, sprintf('%s parameter is correct', $key));
    }

    protected function assertHasDefinition(ContainerBuilder $container, $id)
    {
        $actual = $container->hasDefinition($id) ?: $container->hasAlias($id);

        static::assertTrue($actual);
    }

    protected function assertDICConstructorArguments(Definition $definition, array $arguments)
    {
        static::assertEquals(
            $arguments,
            $definition->getArguments(),
            sprintf(
                'Expected and actual DIC Service constructor arguments of definition "%s" do not match.',
                $definition->getClass()
            )
        );
    }

    protected function assertDICTags(Definition $definition, array $tags)
    {
        static::assertEquals(
            $tags,
            $definition->getTags(),
            sprintf(
                'Expected and actual DIC Service tags of definition "%s" do not match.',
                $definition->getClass()
            )
        );
    }
}
