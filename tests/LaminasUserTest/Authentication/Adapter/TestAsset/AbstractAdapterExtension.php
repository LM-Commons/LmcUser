<?php

namespace LaminasUserTest\Authentication\Adapter\TestAsset;

use Laminas\EventManager\EventInterface;
use LaminasUser\Authentication\Adapter\AbstractAdapter;

class AbstractAdapterExtension extends AbstractAdapter
{
    public function authenticate(EventInterface $e)
    {
    }
}
