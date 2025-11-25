<?php

declare(strict_types=1);

namespace LmcUser\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use Lmc\User\Common\Mapper\UserMapperInterface;
use LmcUser\Entity\UserInterface;
use LmcUser\EventManager\EventProvider;
use LmcUser\Options\UserServiceOptionsInterface;
use Psr\Container\ContainerInterface;

class User extends EventProvider
{
    protected UserMapperInterface $userMapper;

    protected AuthenticationService $authService;

    protected Form $loginForm;

    protected Form $registerForm;

    protected Form $changePasswordForm;

    protected ContainerInterface $serviceManager;

    protected UserServiceOptionsInterface $options;

    protected ClassMethodsHydrator $formHydrator;

    public function __construct(
        UserMapperInterface $userMapper,
        AuthenticationService $authService,
        Form $loginForm,
        Form $registerForm,
        Form $changePasswordForm,
        UserServiceOptionsInterface $options,
        ClassMethodsHydrator $hydrator,
    ) {
        $this->userMapper         = $userMapper;
        $this->authService        = $authService;
        $this->loginForm          = $loginForm;
        $this->registerForm       = $registerForm;
        $this->changePasswordForm = $changePasswordForm;
        $this->options            = $options;
        $this->formHydrator       = $hydrator;
    }

    /**
     * createFromForm
     *
     * @throws Exception\InvalidArgumentException
     */
    public function register(array $data): bool|UserInterface
    {
        $class = $this->getOptions()->getUserEntityClass();
        $user  = new $class();
        $form  = $this->getRegisterForm();
        $form->setHydrator($this->getFormHydrator());
        $form->bind($user);
        $form->setData($data);
        if (! $form->isValid()) {
            return false;
        }

        $user = $form->getData();
        /** @var UserInterface $user */

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        if ($this->getOptions()->getEnableUsername()) {
            $user->setUsername($data['username']);
        }
        if ($this->getOptions()->getEnableDisplayName()) {
            $user->setDisplayName($data['display_name']);
        }

        // If user state is enabled, set the default state value
        if ($this->getOptions()->getEnableUserState()) {
            $user->setState($this->getOptions()->getDefaultUserState());
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $user, 'form' => $form]);
        $this->getUserMapper()->insert($user);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $user, 'form' => $form]);
        return $user;
    }

    /**
     * change the current users password
     */
    public function changePassword(array $data): bool
    {
        $currentUser = $this->getAuthService()->getIdentity();

        $oldPass = $data['credential'];
        $newPass = $data['newCredential'];

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($oldPass, $currentUser->getPassword())) {
            return false;
        }

        $pass = $bcrypt->create($newPass);
        $currentUser->setPassword($pass);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser, 'data' => $data]);
        $this->getUserMapper()->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser, 'data' => $data]);

        return true;
    }

    public function changeEmail(array $data): bool
    {
        $currentUser = $this->getAuthService()->getIdentity();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($data['credential'], $currentUser->getPassword())) {
            return false;
        }

        $currentUser->setEmail($data['newIdentity']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser, 'data' => $data]);
        $this->getUserMapper()->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser, 'data' => $data]);

        return true;
    }

    /**
     * getUserMapper
     */
    public function getUserMapper(): UserMapperInterface
    {
        return $this->userMapper;
    }

    public function setUserMapper(UserMapperInterface $userMapper): static
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * getAuthService
     */
    public function getAuthService(): AuthenticationService
    {
        return $this->authService;
    }

    /**
     * setAuthenticationService
     */
    public function setAuthService(AuthenticationService $authService): static
    {
        $this->authService = $authService;
        return $this;
    }

    public function getRegisterForm(): Form
    {
        return $this->registerForm;
    }

    public function setRegisterForm(Form $registerForm): static
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    public function getChangePasswordForm(): Form
    {
        return $this->changePasswordForm;
    }

    public function setChangePasswordForm(Form $changePasswordForm): static
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    public function getOptions(): UserServiceOptionsInterface
    {
        return $this->options;
    }

    /**
     * set service options
     */
    public function setOptions(UserServiceOptionsInterface $options): void
    {
        $this->options = $options;
    }

    /**
     * Retrieve service manager instance
     */
    public function getServiceManager(): ContainerInterface
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ContainerInterface $serviceManager): static
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Return the Form Hydrator
     */
    public function getFormHydrator(): HydratorInterface
    {
        return $this->formHydrator;
    }

    /**
     * Set the Form Hydrator to use
     *
     * @return User
     */
    public function setFormHydrator(Hydrator\HydratorInterface $formHydrator)
    {
        $this->formHydrator = $formHydrator;
        return $this;
    }
}
