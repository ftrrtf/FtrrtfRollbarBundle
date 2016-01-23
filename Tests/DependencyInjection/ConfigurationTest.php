<?php

namespace Symfony\Bundle\WebProfilerBundle\Tests\DependencyInjection;

use Ftrrtf\RollbarBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidConfiguration()
    {
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), array());
    }

    /**
     * @dataProvider configurationDataProvider
     */
    public function testValidConfiguration($options, $expectedConfig)
    {
        $processor = new Processor();
        $actualConfig = $processor->processConfiguration(
            new Configuration(), array($options)
        );
        static::assertEquals($expectedConfig, $actualConfig);
    }

    public function configurationDataProvider()
    {
        return array(
            'server curl transport configuration' => array(
                array(
                    'notifier' => array(
                        'server' => array(
                            'transport' => array(
                                'type' => 'curl',
                                'access_token' => 'token'
                            ),
                        ),
                    ),
                ),
                array(
                    'notifier' => array(
                        'server' => array(
                            'batched' => false,
                            'batch_size' => '50',
                            'transport' => array(
                                'type' => 'curl',
                                'access_token' => 'token'
                            ),
                        ),
                    ),
                ),
            ),
            'server agent transport configuration' => array(
                array(
                    'notifier' => array(
                        'server' => array(
                            'transport' => array(
                                'type' => 'agent',
                                'agent_log_location' => '/path/to/log'
                            ),
                        ),
                    ),
                ),
                array(
                    'notifier' => array(
                        'server' => array(
                            'batched' => false,
                            'batch_size' => '50',
                            'transport' => array(
                                'type' => 'agent',
                                'agent_log_location' => '/path/to/log'
                            ),
                        ),
                    )
                )
            ),
            'client js configuration' => array(
                array(
                    'notifier' => array(
                        'client' => array(
                            'access_token' => 'token'
                        ),
                    ),
                ),
                array(
                    'notifier' => array(
                        'client' => array(
                            'access_token' => 'token',
                            'source_map_enabled' => false,
                            'code_version' => '',
                            'guess_uncaught_frames' => false,
                            'rollbarjs_version' => 'v1',
                            'allowed_js_hosts' => array(),
                        ),
                    ),
                ),
            ),
            'environment configuration' => array(
                array(
                    'notifier' => array(
                        'client' => array(
                            'access_token' => 'token'
                        )
                    ),
                    'environment' => array(
                        'environment' => 'production',
                        'branch' => 'master',
                        'code_version' => '12345'
                    ),
                ),
                array(
                    'notifier' => array(
                        'client' => array(
                            'access_token' => 'token',
                            'source_map_enabled' => false,
                            'code_version' => '',
                            'guess_uncaught_frames' => false,
                            'rollbarjs_version' => 'v1',
                            'allowed_js_hosts' => array(),
                        ),
                    ),
                    'environment' => array(
                        'environment' => 'production',
                        'branch' => 'master',
                        'root_dir' => '',
                        'code_version' => '12345'
                    ),
                )
            ),
        );
    }
}
