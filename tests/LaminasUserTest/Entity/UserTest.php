<?php

namespace LaminasUserTest\Entity;

use LaminasUser\Entity\User as Entity;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $user;

    public function setUp()
    {
        $user = new Entity;
        $this->user = $user;
    }

    /**
     * @covers LaminasUser\Entity\User::setId
     * @covers LaminasUser\Entity\User::getId
     */
    public function testSetGetId()
    {
        $this->user->setId(1);
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * @covers LaminasUser\Entity\User::setUsername
     * @covers LaminasUser\Entity\User::getUsername
     */
    public function testSetGetUsername()
    {
        $this->user->setUsername('laminasUser');
        $this->assertEquals('laminasUser', $this->user->getUsername());
    }

    /**
     * @covers LaminasUser\Entity\User::setDisplayName
     * @covers LaminasUser\Entity\User::getDisplayName
     */
    public function testSetGetDisplayName()
    {
        $this->user->setDisplayName('Zfc User');
        $this->assertEquals('Zfc User', $this->user->getDisplayName());
    }

    /**
     * @covers LaminasUser\Entity\User::setEmail
     * @covers LaminasUser\Entity\User::getEmail
     */
    public function testSetGetEmail()
    {
        $this->user->setEmail('laminasUser@laminasUser.com');
        $this->assertEquals('laminasUser@laminasUser.com', $this->user->getEmail());
    }

    /**
     * @covers LaminasUser\Entity\User::setPassword
     * @covers LaminasUser\Entity\User::getPassword
     */
    public function testSetGetPassword()
    {
        $this->user->setPassword('laminasUser');
        $this->assertEquals('laminasUser', $this->user->getPassword());
    }

    /**
     * @covers LaminasUser\Entity\User::setState
     * @covers LaminasUser\Entity\User::getState
     */
    public function testSetGetState()
    {
        $this->user->setState(1);
        $this->assertEquals(1, $this->user->getState());
    }
}
