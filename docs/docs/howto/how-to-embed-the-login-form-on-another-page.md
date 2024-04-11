---
sidebar_position: 2
---

# How to embed the login form on another page

## Task
Embed the login form on another page (ie: homepage login widget)

## Solution
LmcUser provides a View Helper ([LmcUserLoginWidget](https://github.com/LM-Commons/LmcUser/blob/master/src/LmcUser/View/Helper/LmcUserLoginWidget.php)) which you can use from any view script in your application.  Just add the following call to the location in your markup where you want the form to be rendered:

```php
<?php echo $this->lmcUserLoginWidget(); ?>
```

## Note
The view helper can also __return__ the login form:

```php
<?php $form = $this->lmcUserLoginWidget(['render' => false]); ?>
```

This will return an object of type [Login](https://github.com/LM-Commons/LmcUser/blob/master/src/LmcUser/Form/Login.php) that can be used to generate a custom login form.
