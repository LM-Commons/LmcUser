---
sidebar_position: 11
---
# How to secure sessions against session hijacking attacks

## Task

Configure the Session Manager to help mitigate session hijacking attacks.

## Solution

If you haven't already done so, add the [session manager factory](https://docs.laminas.dev/laminas-session/config/) to your application via a module config or `config/autoload` file.

In the same file (or another file if you prefer), add the `session_manager` key and insert the session validators you wish to load.  In this case we'll use both `RemoteAddr` and `HttpUserAgent`:

```php
return [
    'service_manager' => [
        'factories' => [
            'Laminas\Session\ManagerInterface' => 'Laminas\Session\Service\SessionManagerFactory',
        ],
    ],
    'session_manager' => [
        'validators' => [
            'Laminas\Session\Validator\RemoteAddr',
            'Laminas\Session\Validator\HttpUserAgent',
        ]
    ],
];
```

Alternatively, you could use an external module such as [`HtSession`](https://github.com/hrevert/HtSession) instead of a manual configuration.

> NOTE: This does not really secure your session against hijacking attacks unless it's 1994. Please use HTTPS, secure cookies, HTTP only cookies, CSRF protection, credential re-entry and session regeneration to make sure your sessions are secure.
