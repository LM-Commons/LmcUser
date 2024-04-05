<?php

namespace LmcUser\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChain as AuthAdapter;

class LmcUserAuthentication extends AbstractPlugin
{
    /**
     * @var AuthAdapter
     */
    protected $authAdapter;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Proxy convenience method
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * Proxy convenience method
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Get authAdapter.
     *
     * @return AuthAdapter
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * Set authAdapter.
     *
     * @param authAdapter $authAdapter
     *
     * @return LmcUserAuthentication
     */
    public function setAuthAdapter(AuthAdapter $authAdapter)
    {
        $this->authAdapter = $authAdapter;
        return $this;
    }

    /**
     * Get authService.
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Set authService.
     *
     * @param AuthenticationService $authService
     *
     * @return LmcUserAuthentication
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
}
