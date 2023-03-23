<?php

namespace LmcUserTest\Authentication\Adapter\TestAsset;

use LmcUser\Authentication\Adapter\AbstractAdapter;
use LmcUser\Authentication\Adapter\AdapterChainEvent;

class AbstractAdapterExtension extends AbstractAdapter
{
    public function authenticate(AdapterChainEvent $e)
    {
    }
}
