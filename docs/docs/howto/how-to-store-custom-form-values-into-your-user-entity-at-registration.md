---
sidebar_position: 4
---
# How to store custom form values into your user entity at registration

Ref, using ZF 2.1.4 at the time this was written

A bit of a follow up to:
https://github.com/LM-Commons/LmcUser/wiki/How-to-embed-the-login-form-on-another-page

In your bootstrap event (hopefully in a custom Module for your user entities and roles and so forth), add this block to your onBootstrap code:



```php
<?php
public function onBootstrap( MVCEvent $e )
{
    $eventManager = $e->getApplication()->getEventManager();
    $em           = $eventManager->getSharedManager();

    // ...
 
    $lmcServiceEvents = $e->getApplication()->getServiceManager()->get('lmcuser_user_service')->getEventManager();

    // To validate new field
    $em->attach('LmcUser\Form\RegisterFilter','init', function($e) {
        $filter = $e->getTarget();
        $filter->add([
            'name'       => 'favorite_icecream',
            'required'   => true,
            'allowEmpty' => false,
            'filters'    => [['name' => 'StringTrim']],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                ],
            ],
        ]);
    });

    // Store the field
    $lmcServiceEvents->attach('register', function($e) {
        $form = $e->getParam('form');
        $user = $e->getParam('user');
            
        /* @var $user \FooUser\Entity\User */
        $user->setUsername( $form->get('username')->getValue() );
        $user->setFavoriteIceCream( $form->get('favorite_icecream')->getValue() );
    });
}
```
