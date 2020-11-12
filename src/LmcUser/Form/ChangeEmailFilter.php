<?php

namespace LmcUser\Form;

use Laminas\InputFilter\InputFilter;
use LmcUser\Options\AuthenticationOptionsInterface;

class ChangeEmailFilter extends InputFilter
{
    protected $emailValidator;

    public function __construct(AuthenticationOptionsInterface $options, $emailValidator)
    {
        $this->emailValidator = $emailValidator;

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
                'name'       => 'newIdentity',
                'required'   => true,
                'validators' => [
                    [
                    'name' => 'EmailAddress'
                    ],
                    $this->emailValidator
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'newIdentityVerify',
                'required'   => true,
                'validators' => [
                    [
                    'name' => 'identical',
                    'options' => [
                        'token' => 'newIdentity'
                    ]
                    ],
                ],
            ]
        );
    }

    public function getEmailValidator()
    {
        return $this->emailValidator;
    }

    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;
        return $this;
    }
}
