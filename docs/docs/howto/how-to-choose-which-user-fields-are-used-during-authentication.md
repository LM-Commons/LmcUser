---
sidebar_position: 6
---
# How to choose which user fields are used during authentication

## Task
How to specify which fields a user can use as their 'identity' when logging in.

## Solution

The configuration directive `auth_identity_fields` is used to control the fields used to look up user identities stored in LmcUser.  You can configure this directive (via your `config/autoload/lmcuser.global.php` override file) to one of four possible modes:

1. Authenticate via email address only:
```php
'auth_identity_fields' => ['email'],
```
2. Authenticate via username only:
```php
'auth_identity_fields' => ['username'],
```

3. Authenticate via both methods, with username field checked first:
```php
'auth_identity_fields' => ['username', 'email'],
```

4. Authenticate via both methods, with email address field checked first:
```php
'auth_identity_fields' => ['email', 'username'],
```
