<?php

namespace LmcUser\Authentication\Adapter;

use Laminas\Authentication\Storage\StorageInterface;

/**
 * Interface ChainableAdapter
 */
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
