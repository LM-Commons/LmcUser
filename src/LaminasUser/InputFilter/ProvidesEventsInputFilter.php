<?php
namespace LaminasUser\InputFilter;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\InputFilter\InputFilter;

class ProvidesEventsInputFilter extends InputFilter
{
    use EventManagerAwareTrait;
}
