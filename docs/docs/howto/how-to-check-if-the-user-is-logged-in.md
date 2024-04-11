---
sidebar_position: 8
---
# How to check if the user is logged in
## Task
Check if the user is logged in (ie: user identity widget)

## Solution
There are three ways.

### View
LmcUser provides a View Helper ([lmcUserIdentity](https://github.com/LM-Commons/LmcUser/blob/master/src/LmcUser/View/Helper/LmcUserIdentity.php)) which you can use from any view script in your application.

```php
<!-- Test if the User is connected -->
<?php if(!$this->lmcUserIdentity()): ?>
    <!-- display the login form -->
    <?php echo $this->lmcUserLoginWidget(['redirect'=>'application']); ?>
<?php else: ?>
    <!-- display the 'display name' of the user -->
    <?php echo $this->lmcUserIdentity()->getDisplayname(); ?>
<?php endif?>
```

You can also get user's fields (if the user is logged in), like email:

```php
<?php echo $this->lmcUserIdentity()->getEmail(); ?>
```

### Controller

LmcUser provides a Controller Plugin ([lmcUserAuthentication](https://github.com/LM-Commons/LmcUser/blob/master/src/LmcUser/Controller/Plugin/LmcUserAuthentication.php)) which you can use from any controller in your application. You can check if the user is connected and get his data:

```php
<?php
if ($this->lmcUserAuthentication()->hasIdentity()) {
    //get the email of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getEmail();
    //get the user_id of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getId();
    //get the username of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getUsername();
    //get the display name of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getDisplayname();
}
?>
```

You can get the `lmcuser_auth_service` in a controller by doing:
```php
$authService = $this->lmcUserIdentity()->getAuthService();
```
### Service Manager

```php
<?php
$sm = $app->getServiceManager();
$auth = $sm->get('lmcuser_auth_service');
if ($auth->hasIdentity()) {
    echo $auth->getIdentity()->getEmail();
}
?>
```
