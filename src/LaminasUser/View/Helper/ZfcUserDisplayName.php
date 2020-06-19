<?php

namespace LaminasUser\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use LaminasUser\Entity\UserInterface as User;

class LaminasUserDisplayName extends AbstractHelper
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * __invoke
     *
     * @access public
     * @param \LaminasUser\Entity\UserInterface $user
     * @throws \LaminasUser\Exception\DomainException
     * @return String
     */
    public function __invoke(User $user = null)
    {
        if (null === $user) {
            if ($this->getAuthService()->hasIdentity()) {
                $user = $this->getAuthService()->getIdentity();
                if (!$user instanceof User) {
                    throw new \LaminasUser\Exception\DomainException(
                        '$user is not an instance of User',
                        500
                    );
                }
            } else {
                return false;
            }
        }

        $displayName = $user->getDisplayName();
        if (null === $displayName) {
            $displayName = $user->getUsername();
        }
        // User will always have an email, so we do not have to throw error
        if (null === $displayName) {
            $displayName = $user->getEmail();
            $displayName = substr($displayName, 0, strpos($displayName, '@'));
        }

        return $displayName;
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
     * @return \LaminasUser\View\Helper\LaminasUserDisplayName
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
}
