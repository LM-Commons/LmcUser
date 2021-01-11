<?php

namespace LmcUser\Options;

interface AuthenticationOptionsInterface extends PasswordOptionsInterface
{

    /**
     * set login form timeout in seconds
     *
     * @param int $loginFormTimeout
     */
    public function setLoginFormTimeout($loginFormTimeout);

    /**
     * set login form timeout in seconds
     *
     * @param int $loginFormTimeout
     */
    public function getLoginFormTimeout();

    /**
     * set auth identity fields
     *
     * @param  array $authIdentityFields
     * @return ModuleOptions
     */
    public function setAuthIdentityFields($authIdentityFields);

    /**
     * get auth identity fields
     *
     * @return array
     */
    public function getAuthIdentityFields();

    /**
     * set use a captcha in login form
     *
     * @param bool $useLoginFormCaptcha
     * @return ModuleOptions
     */
    public function setUseLoginFormCaptcha($useLoginFormCaptcha);

    /**
     * get use a captcha in login form
     *
     * @return bool
     */
    public function getUseLoginFormCaptcha();

    /**
     * set use a csrf in login form
     *
     * @param  bool $useLoginFormCsrf
     * @return ModuleOptions
     */
    public function setUseLoginFormCsrf($useLoginFormCsrf);

    /**
     * get use a csrf in login form
     *
     * @return bool
     */
    public function getUseLoginFormCsrf();

    /**
     * set form CAPTCHA options
     *
     * @param  array $formCaptchaOptions
     * @return ModuleOptions
     */
    public function setFormCaptchaOptions($formCaptchaOptions);

    /**
     * get form CAPTCHA options
     *
     * @return array
     */
    public function getFormCaptchaOptions();
}
