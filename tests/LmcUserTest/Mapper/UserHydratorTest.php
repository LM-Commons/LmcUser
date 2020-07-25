<?php

namespace LmcUserTest\Mapper;

use LmcUser\Entity\UserInterface;

use LmcUser\Entity\UserInterface as UserEntityInterface;
use LmcUser\Mapper\Exception\InvalidArgumentException;
use LmcUser\Mapper\UserHydrator as Hydrator;
use LmcUserTest\Authentication\Adapter\TestAsset\InvalidUserClass;
use PHPUnit\Framework\TestCase;

class UserHydratorTest extends TestCase
{
    protected $hydrator;

    public function setUp():void
    {
        $hydrator = new Hydrator;
        $this->hydrator = $hydrator;
    }

    /**
     * @covers LmcUser\Mapper\UserHydrator::extract
     */
    public function testExtractWithInvalidUserObject()
    {
        $this->expectException(InvalidArgumentException::class);
        $user = $this->createMock(InvalidUserClass::class);

        $this->hydrator->extract($user);
    }

    /**
     * @covers       LmcUser\Mapper\UserHydrator::extract
     * @covers       LmcUser\Mapper\UserHydrator::mapField
     * @dataProvider dataProviderTestExtractWithValidUserObject
     * @see          https://github.com/ZF-Commons/LmcUser/pull/421
     */
    public function testExtractWithValidUserObject($object, $expectArray)
    {
        $result = $this->hydrator->extract($object);
        $this->assertEquals($expectArray, $result);
    }

    /**
     * @covers LmcUser\Mapper\UserHydrator::hydrate
     */
    public function testHydrateWithInvalidUserObject()
    {
        $this->expectException(InvalidArgumentException::class);
        $user = new \StdClass;
        $this->hydrator->hydrate(array(), $user);
    }

    /**
     * @covers LmcUser\Mapper\UserHydrator::hydrate
     * @covers LmcUser\Mapper\UserHydrator::mapField
     */
    public function testHydrateWithValidUserObject()
    {
        $user = new \LmcUser\Entity\User;

        $expectArray = array(
            'username' => 'lmcuser',
            'email' => 'Zfc User',
            'display_name' => 'LmcUser',
            'password' => 'LmcUserPassword',
            'state' => 1,
            'user_id' => 1
        );

        $result = $this->hydrator->hydrate($expectArray, $user);

        $this->assertEquals($expectArray['username'], $result->getUsername());
        $this->assertEquals($expectArray['email'], $result->getEmail());
        $this->assertEquals($expectArray['display_name'], $result->getDisplayName());
        $this->assertEquals($expectArray['password'], $result->getPassword());
        $this->assertEquals($expectArray['state'], $result->getState());
        $this->assertEquals($expectArray['user_id'], $result->getId());
    }

    public function dataProviderTestExtractWithValidUserObject()
    {
        $createUserObject = function ($data) {
            $user = new \LmcUser\Entity\User;
            foreach ($data as $key => $value) {
                if ($key == 'user_id') {
                    $key='id';
                }
                $methode = 'set' . str_replace(" ", "", ucwords(str_replace("_", " ", $key)));
                call_user_func(array($user,$methode), $value);
            }
            return $user;
        };
        $return = array();
        $expectArray = array();

        $buffer = array(
            'username' => 'lmcuser',
            'email' => 'Zfc User',
            'display_name' => 'LmcUser',
            'password' => 'LmcUserPassword',
            'state' => 1,
            'user_id' => 1
        );

        $return[]=array($createUserObject($buffer), $buffer);

        /**
         * @see https://github.com/ZF-Commons/LmcUser/pull/421
         */
        $buffer = array(
            'username' => 'lmcuser',
            'email' => 'Zfc User',
            'display_name' => 'LmcUser',
            'password' => 'LmcUserPassword',
            'state' => 1
        );

        $return[]=array($createUserObject($buffer), $buffer);

        return $return;
    }
}
