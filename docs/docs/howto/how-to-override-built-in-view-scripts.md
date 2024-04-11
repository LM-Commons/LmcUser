---
sidebar_position: 1
---
# How to override built in view scripts
## Task
Override the built-in view scripts for pages such as registration and sign-in with your own custom view scripts

## Solution

1. In your module, under the `view` directory, create the folder tree `lmc-user/user`
2. Create the necessary override view scripts, depending on which page(s) you want to change:
    * User Login page: `lmc-user/user/login.phtml`
    * User Registration page: `lmc-user/user/register.phtml`
    * Default post-login landing page: `lmc-user/user/index.phtml`
3. Put this into your `module.config.php` file

```php
'view_manager' => [
        'template_path_stack' => [
            'zfc-user' => __DIR__ . '/../view',
        ],
    ],
```

Refer to each [built-in view script](https://github.com/LM-Commons/LmcUser/tree/master/view/lmc-user/user) to see how the form is configured and rendered.

NOTE: Your module must be loaded after LmcUser or the overriding will not work.  To do this, place your module after LmcUser in the `modules` key of your application configuration (`config/application.config.php`).
