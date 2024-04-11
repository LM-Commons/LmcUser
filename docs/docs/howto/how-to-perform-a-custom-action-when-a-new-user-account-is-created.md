---
sidebar_position: 5
---
# How to perform a custom action when a new user account is created

## Task
Perform a custom action when a new user account is created.

## Solution
The user service (`lmcuser_user_service`) provided by LmcUser triggers an event (`register`) immediately before persisting the user account.

If you have access to the service locator which LmcUser has previously loaded it's services into, you can simply pull the user service and attach an event to it's internal event manager.

```php
<?php
$lmcServiceEvents = $locator->get('lmcuser_user_service')->getEventManager();
$lmcServiceEvents->attach('register', function($e) {
    $user = $e->getParam('user');  // User account object
    $form = $e->getParam('form');  // Form object
    // Perform your custom action here
});
```

If you can't get access to the user service instance directly, you can use the StaticEventManager to attach an event directly via the class name:

```php
<?php
$em = \Laminas\EventManager\StaticEventManager::getInstance();
$em->attach('LmcUser\Service\User', 'register', function($e) {
    $user = $e->getParam('user');  // User account object
    $form = $e->getParam('form');  // Form object
    // Perform your custom action here
});
```

## Retrieving the User Id
If you need to retrieve the `user_id`, just attach to `register.post` and the user entity should have it.

## Example

File: module/Application/Module.php
```php
<?php
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $em = \Laminas\EventManager\StaticEventManager::getInstance();
        $em->attach('LmcUser\Service\User', 'register', function($e) {
            $user = $e->getParam('user');  // User account object
            $form = $e->getParam('form');  // Form object
            // Perform your custom action here
        });
    }
}
```
