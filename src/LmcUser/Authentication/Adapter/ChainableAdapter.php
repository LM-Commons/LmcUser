<?php

namespace LmcUser\Authentication\Adapter;

use Laminas\Authentication\Storage\StorageInterface;
use Laminas\EventManager\EventInterface;

interface ChainableAdapter
{
    /**
     * @param  AdapterChainEvent $e
     * @return bool
     */
    public function authenticate(AdapterChainEvent $e);

    /**
     * @return StorageInterface
     */
    public function getStorage();
}
