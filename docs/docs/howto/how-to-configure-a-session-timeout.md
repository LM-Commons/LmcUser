---
sidebar_position: 10
---
# How to configure a session timeout
## Task
Automatically terminate a user's session after a specific period of inactivity.

## Solution

1. Add [session factories and configuration](https://docs.laminas.dev/laminas-session/config/) to your application via a module config or `config/autoload` file:

    ```php
    return [
        'service_manager' => [
            'factories' => [
                // Configures the default SessionManager instance
                'Laminas\Session\ManagerInterface' => 'Laminas\Session\Service\SessionManagerFactory',
                
                // Provides session configuration to SessionManagerFactory
                'Laminas\Session\Config\ConfigInterface' => 'Laminas\Session\Service\SessionConfigFactory',
            ],
        ],
        'session_manager' => [
            // SessionManager config: validators, etc
        ],
        'session_config' => [
            // Set the session and cookie expiries to 15 minutes
            'cache_expire' => 900,
            'cookie_lifetime' => 900,
        ],
    ];
    ```

2. In `Application\Module::onBootstrap`, pull an instance of the SessionManager.  This will inject the properly-configured SessionManager instance as the default for all new session containers.

    ```php
    public function onBootstrap(MvcEvent $e)
    {
        $manager = $e->getApplication()->getServiceManager()->get('Laminas\Session\ManagerInterface');
    }
    ```

Alternatively, you could use an external module such as [`HtSession`](https://github.com/hrevert/HtSession) instead of a manual configuration.
