<?php

namespace LmcUserTest\Entity;

use LmcUser\Entity\User as Entity;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $user;

    public function setUp():void
    {
        $user = new Entity;
        $this->user = $user;
    }

    /**
     * @covers LmcUser\Entity\User::setId
     * @covers LmcUser\Entity\User::getId
     */
    public function testSetGetId()
    {
        $this->user->setId(1);
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * @covers LmcUser\Entity\User::setUsername
     * @covers LmcUser\Entity\User::getUsername
     */
    public function testSetGetUsername()
    {
        $this->user->setUsername('lmcUser');
        $this->assertEquals('lmcUser', $this->user->getUsername());
    }

    /**
     * @covers LmcUser\Entity\User::setDisplayName
     * @covers LmcUser\Entity\User::getDisplayName
     */
    public function testSetGetDisplayName()
    {
        $this->user->setDisplayName('Zfc User');
        $this->assertEquals('Zfc User', $this->user->getDisplayName());
    }

    /**
     * @covers LmcUser\Entity\User::setEmail
     * @covers LmcUser\Entity\User::getEmail
     */
    public function testSetGetEmail()
    {
        $this->user->setEmail('lmcUser@lmcUser.com');
        $this->assertEquals('lmcUser@lmcUser.com', $this->user->getEmail());
    }

    /**
     * @covers LmcUser\Entity\User::setPassword
     * @covers LmcUser\Entity\User::getPassword
     */
    public function testSetGetPassword()
    {
        $this->user->setPassword('lmcUser');
        $this->assertEquals('lmcUser', $this->user->getPassword());
    }

    /**
     * @covers LmcUser\Entity\User::setState
     * @covers LmcUser\Entity\User::getState
     */
    public function testSetGetState()
    {
        $this->user->setState(1);
        $this->assertEquals(1, $this->user->getState());
    }
}
