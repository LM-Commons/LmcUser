<?php

namespace LmcUser\Form;

use Laminas\Form\Element;
use LmcUser\Options\AuthenticationOptionsInterface;

class Otp extends ProvidesEventsForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name, AuthenticationOptionsInterface $options)
    {
        $this->setAuthenticationOptions($options);

        parent::__construct($name);

        $this->add(
            array(
                'name' => 'code',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'type' => 'text'
                ),
            )
        );
        if ($this->getAuthenticationOptions()->getUseLoginFormCsrf()) {
            $this->add([
                'type' => '\Laminas\Form\Element\Csrf',
                'name' => 'security',
                'options' => [
                    'csrf_options' => [
                        'timeout' => $this->getAuthenticationOptions()->getLoginFormTimeout()
                    ]
                ]
            ]);
        }
        
        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Sign In')
            ->setAttributes(
                array(
                    'type'  => 'submit',
                )
            );

        $this->add(
            $submitElement,
            array(
                'priority' => -100,
            )
        );
    }

    /**
     * Set Authentication-related Options
     *
     * @param  AuthenticationOptionsInterface $authOptions
     * @return Login
     */
    public function setAuthenticationOptions(AuthenticationOptionsInterface $authOptions)
    {
        $this->authOptions = $authOptions;

        return $this;
    }

    /**
     * Get Authentication-related Options
     *
     * @return AuthenticationOptionsInterface
     */
    public function getAuthenticationOptions()
    {
        return $this->authOptions;
    }
}
