FtrrtfRollbarBundle
===================

Symfony2 Bundle for rollbar.com

## Installation

### Install the bundle via Composer

#### Method 1: Auto-Update of your composer.json

    php composer.phar require ftrrtf/rollbar-bundle

#### Method 2: Manually updating your composer.json

    "require" :  {
        // ...
        "ftrrtf/rollbar-bundle": "v0.5",
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
            # optional - if not passed, window.location.origin will be only allowed host
            # allowed_js_hosts:
            #   - http://myhost.mydomain.com
            #   - http://myhost2.mydomain.com
    environment:
        environment: "%rollbar_environment%[%kernel.environment%]"
```

## TODO

 * More tests
 * More configuration options for rollbar-notifier
