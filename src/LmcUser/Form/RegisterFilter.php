<?php

namespace LmcUser\Form;

use LmcUser\InputFilter\ProvidesEventsInputFilter;
use LmcUser\Options\RegistrationOptionsInterface;

class RegisterFilter extends ProvidesEventsInputFilter
{
    protected $emailValidator;
    protected $usernameValidator;

    /**
     * @var RegistrationOptionsInterface
     */
    protected $options;

    public function __construct($emailValidator, $usernameValidator, RegistrationOptionsInterface $options)
    {
        $this->setOptions($options);
        $this->emailValidator = $emailValidator;
        $this->usernameValidator = $usernameValidator;

        if ($this->getOptions()->getEnableUsername()) {
            $this->add(
                [
                    'name'       => 'username',
                    'required'   => true,
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                            'min' => 3,
                            'max' => 255,
                            ],
                        ],
                        $this->usernameValidator,
                    ],
                ]
            );
        }

        $this->add(
            [
                'name'       => 'email',
                'required'   => true,
                'validators' => [
                    [
                    'name' => 'EmailAddress'
                    ],
                    $this->emailValidator
                ],
            ]
        );

        if ($this->getOptions()->getEnableDisplayName()) {
            $this->add(
                [
                    'name'       => 'display_name',
                    'required'   => true,
                    'filters'    => [['name' => 'StringTrim']],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                            'min' => 3,
                            'max' => 128,
                            ],
                        ],
                    ],
                ]
            );
        }

        $this->add(
            [
                'name'       => 'password',
                'required'   => true,
                'filters'    => [['name' => 'StringTrim']],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                        'min' => 6,
                        ],
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'passwordVerify',
                'required'   => true,
                'filters'    => [['name' => 'StringTrim']],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                        'min' => 6,
                        ],
                    ],
                    [
                        'name'    => 'Identical',
                        'options' => [
                        'token' => 'password',
                        ],
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

    public function getUsernameValidator()
    {
        return $this->usernameValidator;
    }

    public function setUsernameValidator($usernameValidator)
    {
        $this->usernameValidator = $usernameValidator;
        return $this;
    }

    /**
     * set options
     *
     * @param RegistrationOptionsInterface $options
     */
    public function setOptions(RegistrationOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return RegistrationOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }
}
