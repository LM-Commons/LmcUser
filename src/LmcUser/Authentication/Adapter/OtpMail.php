<?php

namespace LmcUser\Authentication\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Result as AuthenticationResult;
use Laminas\EventManager\EventInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Session\Container as SessionContainer;
use LmcUser\Entity\UserOtpInterface;
use LmcUser\Mapper\UserInterface as UserMapperInterface;
use LmcUser\Options\ModuleOptions;
use LmcUser\Entity\User;
use Laminas\Mail;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use Laminas\Http\Response;

class OtpMail extends AbstractAdapter
{
    /**
     * @var UserMapperInterface
     */
    protected $mapper;

    /**
     * @var callable
     */
    protected $credentialPreprocessor;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * Called when user id logged out
     *
     * @param AdapterChainEvent $e
     */
    public function logout(AdapterChainEvent $e)
    {
        $this->getStorage()->clear();
    }

    /**
     * @param  AdapterChainEvent $e
     * @return bool
     */
    public function authenticate(AdapterChainEvent $e)
    {
        $storage = $this->getStorage()->read();
        if (!$this->isSatisfied()) {
            $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                ->setMessages(array('A record with the supplied identity could not be found.'));
            return;
        }

        /**
         *
         * @var UserOtpInterface|null $userObject
        */
        $userObject = $this->getMapper()->findById($storage['identity']);
        if (!$userObject) {
            $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                ->setMessages(array('A record with the supplied identity could not be found.'));
            $this->setSatisfied(false);
            return false;
        }

        if (((isset($storage['is_otp_satisfied']) && true === $storage['is_otp_satisfied'])) || false === $userObject->getUseOtp()) {
            $storage = $this->getStorage()->read();
            $e->setIdentity($storage['identity'])
                ->setCode(AuthenticationResult::SUCCESS)
                ->setMessages(array('Authentication successful.'));
            return;
        }

        $code = $e->getRequest()->getPost()->get('code');

        if (!$code) {
            $randCode = rand(100000, 999999);
            $userObject->setOtp(strval($randCode));
            $userObject->setOtpTimeout(time() + 60 * 5);

            try {
                $mail = $this->createMail($userObject, $randCode);
                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);
            } catch (\Exception $error) {
                // Handle error if needed
            }
            $this->getMapper()->update($userObject);
            $router  = $this->serviceManager->get('Router');
            $url = $router->assemble([], [
                'name' => 'lmcuser/otp'
            ]);
            $response = new Response();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            return $response;
        }

        if ($userObject->getOtp() != $code) {
            $e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
                ->setMessages(array('Supplied credential is invalid.'));
            $this->setSatisfied(false);
            return false;
        }

        if ($userObject->getOtpTimeout('unix') < time()) {
            $e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
                ->setMessages(array('Supplied credential is invalid.'));
            $this->setSatisfied(false);
            return false;
        }

        // regen the id
        $session = new SessionContainer($this->getStorage()->getNameSpace());
        $session->getManager()->regenerateId();

        // Success!
        $e->setIdentity($userObject->getId());
        // Update user's password hash if the cost parameter has changed
        $storage = $this->getStorage()->read();
        $storage['is_otp_satisfied'] = true;
        $storage['identity'] = $e->getIdentity();
        $this->getStorage()->write($storage);
        $e->setCode(AuthenticationResult::SUCCESS)
            ->setMessages(array('Authentication successful.'));
    }

    /**
     *
     * @param User $user
     * @param string $code
     * @return Mail\Message
     */
    private function createMail($user, $code)
    {
        $mail_message = $code;
        $mail = new Mail\Message();
        $mail->setEncoding("UTF-8");
        $mail->setFrom('test@example.com');
        $mail->addTo($user->getEmail());
        $mail->setSubject('OTP');
        $html = new MimePart($mail_message);
        $html->type = 'text/html';
        $body = new MimeMessage();
        $body->setParts([$html]);
        $mail->setBody($body);
        return $mail;
    }

    /**
     * getMapper
     *
     * @return UserMapperInterface
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('lmcuser_user_mapper');
        }

        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @param  UserMapperInterface $mapper
     * @return Db
     */
    public function setMapper(UserMapperInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * Get credentialPreprocessor.
     *
     * @return callable
     */
    public function getCredentialPreprocessor()
    {
        return $this->credentialPreprocessor;
    }

    /**
     * Set credentialPreprocessor.
     *
     * @param  callable $credentialPreprocessor
     * @return $this
     */
    public function setCredentialPreprocessor($credentialPreprocessor)
    {
        $this->credentialPreprocessor = $credentialPreprocessor;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ContainerInterface $serviceManager
     */
    public function setServiceManager(ContainerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param ModuleOptions $options
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
    }

    /**
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if ($this->options === null) {
            $this->setOptions($this->getServiceManager()->get('lmcuser_module_options'));
        }

        return $this->options;
    }
}
