<?php

namespace LaminasUserTest\Mapper;

use LaminasUser\Mapper\UserHydrator as Hydrator;

class UserHydratorTest extends \PHPUnit_Framework_TestCase
{
    protected $hydrator;

    public function setUp()
    {
        $hydrator = new Hydrator;
        $this->hydrator = $hydrator;
    }

    /**
     * @covers LaminasUser\Mapper\UserHydrator::extract
     * @expectedException LaminasUser\Mapper\Exception\InvalidArgumentException
     */
    public function testExtractWithInvalidUserObject()
    {
        $user = new \StdClass;
        $this->hydrator->extract($user);
    }

    /**
     * @covers LaminasUser\Mapper\UserHydrator::extract
     * @covers LaminasUser\Mapper\UserHydrator::mapField
     * @dataProvider dataProviderTestExtractWithValidUserObject
     * @see https://github.com/ZF-Commons/LaminasUser/pull/421
     */
    public function testExtractWithValidUserObject($object, $expectArray)
    {
        $result = $this->hydrator->extract($object);
        $this->assertEquals($expectArray, $result);
    }

    /**
     * @covers LaminasUser\Mapper\UserHydrator::hydrate
     * @expectedException LaminasUser\Mapper\Exception\InvalidArgumentException
     */
    public function testHydrateWithInvalidUserObject()
    {
        $user = new \StdClass;
        $this->hydrator->hydrate(array(), $user);
    }

    /**
     * @covers LaminasUser\Mapper\UserHydrator::hydrate
     * @covers LaminasUser\Mapper\UserHydrator::mapField
     */
    public function testHydrateWithValidUserObject()
    {
        $user = new \LaminasUser\Entity\User;

        $expectArray = array(
            'username' => 'laminasuser',
            'email' => 'Zfc User',
            'display_name' => 'LaminasUser',
            'password' => 'LaminasUserPassword',
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
            $user = new \LaminasUser\Entity\User;
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
            'username' => 'laminasuser',
            'email' => 'Zfc User',
            'display_name' => 'LaminasUser',
            'password' => 'LaminasUserPassword',
            'state' => 1,
            'user_id' => 1
        );

        $return[]=array($createUserObject($buffer), $buffer);

        /**
         * @see https://github.com/ZF-Commons/LaminasUser/pull/421
         */
        $buffer = array(
            'username' => 'laminasuser',
            'email' => 'Zfc User',
            'display_name' => 'LaminasUser',
            'password' => 'LaminasUserPassword',
            'state' => 1
        );

        $return[]=array($createUserObject($buffer), $buffer);

        return $return;
    }
}
