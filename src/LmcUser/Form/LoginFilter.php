<?php

namespace LmcUser\Form;

use LmcUser\InputFilter\ProvidesEventsInputFilter;
use LmcUser\Options\AuthenticationOptionsInterface;

class LoginFilter extends ProvidesEventsInputFilter
{
    public function __construct(AuthenticationOptionsInterface $options)
    {
        $identityParams = [
            'name'       => 'identity',
            'required'   => true,
            'validators' => []
        ];

        $identityFields = $options->getAuthIdentityFields();
        if ($identityFields == ['email']) {
            $validators = ['name' => 'EmailAddress'];
            array_push($identityParams['validators'], $validators);
        }

        $this->add($identityParams);

        $this->add(
            [
                'name'       => 'credential',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                        'min' => 6,
                        ],
                    ],
                ],
                'filters'   => [
                    ['name' => 'StringTrim'],
                ],
            ]
        );
    }
}
