<?php

namespace LmcUser\Form;

use LmcUser\InputFilter\ProvidesEventsInputFilter;
use LmcUser\Options\AuthenticationOptionsInterface;

class OtpFilter extends ProvidesEventsInputFilter
{
    public function __construct(AuthenticationOptionsInterface $options)
    {
        $identityParams = array(
            'name'       => 'code',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            )
        );
    }
}
