FtrrtfRollbarBundle
===================

[![Latest Stable Version](https://poser.pugx.org/ftrrtf/rollbar-bundle/v/stable)](https://packagist.org/packages/ftrrtf/rollbar-bundle) [![Total Downloads](https://poser.pugx.org/ftrrtf/rollbar-bundle/downloads)](https://packagist.org/packages/ftrrtf/rollbar-bundle) [![Latest Unstable Version](https://poser.pugx.org/ftrrtf/rollbar-bundle/v/unstable)](https://packagist.org/packages/ftrrtf/rollbar-bundle) [![License](https://poser.pugx.org/ftrrtf/rollbar-bundle/license)](https://packagist.org/packages/ftrrtf/rollbar-bundle)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ftrrtf/FtrrtfRollbarBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ftrrtf/FtrrtfRollbarBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ftrrtf/FtrrtfRollbarBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ftrrtf/FtrrtfRollbarBundle/build-status/master)

Symfony2 Bundle for rollbar.com

## Installation

### Requirements

This package needs: `PHP >= 5.5.9`

### Install the bundle via Composer

#### Method 1: Auto-Update of your composer.json

    php composer.phar require ftrrtf/rollbar-bundle

#### Method 2: Manually updating your composer.json

    "require" :  {
        // ...
        "ftrrtf/rollbar-bundle": "^1.0",
    }

### Register the bundle in your application

Update your *AppKernel.php*:

``` php
<?php
public function registerBundles()
{
    if ($this->getEnvironment() == 'prod') {
        $bundles[] = new \Ftrrtf\RollbarBundle\FtrrtfRollbarBundle();
    }
}
```


## Configuration


```yaml
# app/config/config_prod.yml
ftrrtf_rollbar:
    notifier:
        server:
            transport:
                type: curl
                access_token: %rollbar_server_access_token%           
            # or async
            #    type: agent
            #    agent_log_location: %kernel.logs_dir%/%kernel.environment%.rollbar
        client:
            access_token: %rollbar_client_access_token%
            # optional 
            source_map_enabled: true
            code_version: "some version string, such as a version number or git sha"
            guess_uncaught_frames: true
            rollbarjs_version: "rollbar_js_version" # i.e. v1.7, if empty then the newest available version (v1.x) will be used
            
            # if you want to overwrite checkIgnore function (client js), you have to deliver
            # service id which implements Ftrrtf\RollbarBundle\Provider\CheckIgnoreFunctionProviderInterface
            # check_ignore_function_provider: my_service_name
            
            # if you want to overwrite transform function (client js), you have to deliver
            # service id which implements Ftrrtf\RollbarBundle\Provider\TransformPayloadFunctionProviderInterface
            # transform_payload_function_provider: my_service_name
            
            # there is also predefined transformer, which removes user ip and person from the payload
            # transform_payload_function_provider: ftrrtf_rollbar.transform_payload_function_provider.anonymize
            
            # optional - if not passed, window.location.origin will be only allowed host
            # allowed_js_hosts:
            #   - http://myhost.mydomain.com
            #   - http://myhost2.mydomain.com
    environment:
        environment: "%rollbar_environment%[%kernel.environment%]"
        anonymize: true # default to false. If set to true, then neither user_ip nor personal data will be reported
```

## TODO

 * More tests
 * More configuration options for rollbar-notifier
