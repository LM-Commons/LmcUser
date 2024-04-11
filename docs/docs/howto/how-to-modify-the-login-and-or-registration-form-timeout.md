---
sidebar_position: 7
---
# How to modify the login and/or registration form timeout

## Task
The login/registration form timeout is too small and needs to be extended.

## Solution
To simply extend the login or registration form timeout, create a local configuration file inside the project (ex: `/config/autoload/lmcuser.local.php`) and add the necessary configuration options. For example, to allow the login form to live for 30 minutes before expiring :

```php
return [
    'lmc_user' => [
        'login_form_timeout' => 1800,
    ],
];
```

### Note
The registration form's option is `user_form_timeout`.

For very long TTL, instead of setting a very high timeout value, it is also possible to refresh the CSRF field value via Ajax. To do so, create a controller and a route to it. For example, we may want to map `/user/login/keep-alive` to this controller action :

```php
<?php
namespace FooModule\Controller

use Laminas\Mvc\Controller\ActionController,
    Laminas\View\Model\ViewModel;

class BarController extends ActionController
{
    public function keepAliveAction()
    {
        $loginForm = $this->getServiceLocator()->get('lmcuser_login_form');
        return new JsonModel([
            'timestamp' => strtotime('now'),
            'hash' => $loginForm->get('csrf')->getValidator()->getHash(true),
        ]);
    }
}
```

Then, in your module config, add the `JsonStrategy`

```php
    'view_manager' => [
        // ... other configs
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
```

and, finally, in your view script (assuming jQuery is used)

```js
function pingLoginForm() {
    setTimeout(function() {
        $.getJSON("<?php echo $this->url('keep-alive-route-name'); ?>", function(data) {
            if (data.timestamp) {
                $(':hidden[name="csrf"]').val(data.hash);
            }
        });
        pingLoginForm();
    }, 302000); // 5 minutes + 2 seconds
}
pingLoginForm();
```

### Note
The Javascript timer timeout value may be set via the lmc_user config by setting the view model the proper value from the controller (Laminas MVC v2 only)

```php
<?php
$timeout = $this->getServiceLocator()->get('lmcuser_module_options')->getLoginFormTimeout();
return new ViewModel([
     // ...
    'loginFormTimeout' => $timeout,
]);
```
