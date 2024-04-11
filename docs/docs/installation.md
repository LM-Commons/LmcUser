---
sidebar_position: 2
---
# Installation

## Main Setup


### With composer

1. Add this project in your composer.json:

    ```json
    "require": {  
        "lm-commons/lmc-user": "^3.1"  
    }  
    ```

2. Now tell composer to download LmcUser by running the command:

    ```bash
    $ php composer.phar update
    ```

### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'LmcUser',
        ),
        // ...
    );
    ```


### Post-Install: Laminas\Db

1. If you do not already have a valid Laminas\Db\Adapter\Adapter in your service
   manager configuration, put the following in `./config/autoload/database.local.php`:

```php
<?php
return array(
    'db' => array(
        'driver'    => 'PdoMysql',
        'hostname'  => 'changeme',
        'database'  => 'changeme',
        'username'  => 'changeme',
        'password'  => 'changeme',
    ),
    'service_manager' => array(
        'factories' => array(
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);

```

Navigate to http://yourproject/user and you should land on a login page.

## Password Security


**DO NOT CHANGE THE PASSWORD HASH SETTINGS FROM THEIR DEFAULTS** unless A) you
have done sufficient research and fully understand exactly what you are
changing, **AND** B) you have a **very** specific reason to deviate from the
default settings.

If you are planning on changing the default password hash settings, please read
the following:

- [PHP Manual: crypt() function](http://php.net/manual/en/function.crypt.php)
- [Securely Storing Passwords in PHP by Adrian Schneider](http://www.syndicatetheory.com/labs/securely-storing-passwords-in-php)

The password hash settings may be changed at any time without invalidating existing
user accounts. Existing user passwords will be re-hashed automatically on their next
successful login.

**WARNING:** Changing the default password hash settings can cause serious
problems such as making your hashed passwords more vulnerable to brute force
attacks or making hashing so expensive that login and registration is
unacceptably slow for users and produces a large burden on your server(s). The
default settings provided are a very reasonable balance between the two,
suitable for computing power in 2013.


Migration from ZfcUser
----------------------

If using Zend DB update table name to lmc_user

Replace all namespace references to ZfcUser to LmcUser

Update references to the lowercase key zfcuser to lmcuser

