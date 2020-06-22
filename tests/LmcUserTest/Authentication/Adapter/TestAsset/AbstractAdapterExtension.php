<?php

namespace LmcUserTest\Authentication\Adapter\TestAsset;

use Laminas\EventManager\EventInterface;
use LmcUser\Authentication\Adapter\AbstractAdapter;

class AbstractAdapterExtension extends AbstractAdapter
{
    public function authenticate(EventInterface $e)
    {
    }
}
