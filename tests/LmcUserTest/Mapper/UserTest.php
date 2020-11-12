<?php

namespace LmcUserTest\Mapper;

use LmcUser\Mapper\User as Mapper;
use LmcUser\Entity\User as Entity;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Adapter\Adapter;
use LmcUser\Mapper\UserHydrator;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     *
     *
     * @var \LmcUser\Mapper\User
     */
    protected $mapper;

    /**
     *
     *
     * @var \Laminas\Db\Adapter\Adapter
     */
    protected $mockedDbAdapter;

    /**
     *
     *
     * @var \Laminas\Db\Adapter\Adapter
     */
    protected $realAdapter = [];

    /**
     *
     *
     * @var \Laminas\Db\Sql\Select
     */
    protected $mockedSelect;

    /**
     *
     *
     * @var \Laminas\Db\ResultSet\HydratingResultSet
     */
    protected $mockedResultSet;

    /**
     *
     *
     * @var \Laminas\Db\Sql\Sql
     */
    protected $mockedDbSql;

    /**
     *
     *
     * @var \Laminas\Db\Adapter\Driver\DriverInterface
     */
    protected $mockedDbAdapterDriver;

    /**
     *
     *
     * @var \Laminas\Db\Adapter\Platform\PlatformInterface
     */
    protected $mockedDbAdapterPlatform;

    public function setUp():void
    {
        $mapper = new Mapper;
        $mapper->setEntityPrototype(new Entity());
        $mapper->setHydrator(new UserHydrator());
        $this->mapper = $mapper;


        $this->setUpMockedAdapter();

        $this->mockedSelect = $this->createMock('\Laminas\Db\Sql\Select', ['where']);

        $this->mockedResultSet = $this->createMock('\Laminas\Db\ResultSet\HydratingResultSet');

        $this->setUpAdapter('mysql');
        //         $this->setUpAdapter('pgsql');
        $this->setUpAdapter('sqlite');
    }

    /**
     *
     */
    public function setUpAdapter($driver)
    {
        $upCase = strtoupper($driver);
        if (!defined(sprintf('DB_%s_DSN', $upCase))
            || !defined(sprintf('DB_%s_USERNAME', $upCase))
            || !defined(sprintf('DB_%s_PASSWORD', $upCase))
            || !defined(sprintf('DB_%s_SCHEMA', $upCase))
        ) {
             return false;
        }

        try {
            $connection = [
                'driver'=>sprintf('Pdo_%s', ucfirst($driver)),
                'dsn'=>constant(sprintf('DB_%s_DSN', $upCase))
            ];
            if (constant(sprintf('DB_%s_USERNAME', $upCase)) !== "") {
                $connection['username'] = constant(sprintf('DB_%s_USERNAME', $upCase));
                $connection['password'] = constant(sprintf('DB_%s_PASSWORD', $upCase));
            }
            $adapter = new Adapter($connection);

            $this->setUpSqlDatabase($adapter, constant(sprintf('DB_%s_SCHEMA', $upCase)));

            $this->realAdapter[$driver] = $adapter;
        } catch (\Exception $e) {
            $this->realAdapter[$driver] = false;
        }
    }

    public function setUpSqlDatabase($adapter, $schemaPath)
    {
        $queryStack= ['DROP TABLE IF EXISTS user'];
        $queryStack = array_merge($queryStack, explode(';', file_get_contents($schemaPath)));
        $queryStack = array_merge($queryStack, explode(';', file_get_contents(__DIR__ . '/_files/user.sql')));

        foreach ($queryStack as $query) {
            if (!preg_match('/\S+/', $query)) {
                continue;
            }
            $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        }
    }

    /**
     *
     */
    public function setUpMockedAdapter()
    {
        $this->mockedDbAdapterDriver = $this->createMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $this->mockedDbAdapterPlatform = $this->createMock('Laminas\Db\Adapter\Platform\PlatformInterface', []);
        $this->mockedDbAdapterStatement= $this->createMock('Laminas\Db\Adapter\Driver\StatementInterface', []);

        $this->mockedDbAdapterPlatform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('null'));

        $this->mockedDbAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setConstructorArgs(
                [
                                          $this->mockedDbAdapterDriver,
                                          $this->mockedDbAdapterPlatform
                ]
            )
            ->getMock(['getPlatform']);

        $this->mockedDbAdapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($this->mockedDbAdapterPlatform));

        $this->mockedDbSql = $this->getMockBuilder('Laminas\Db\Sql\Sql')
            ->setConstructorArgs([$this->mockedDbAdapter])
            ->setMethods(['prepareStatementForSqlObject'])
            ->getMock();
        $this->mockedDbSql->expects($this->any())
            ->method('prepareStatementForSqlObject')
            ->will($this->returnValue($this->mockedDbAdapterStatement));

        $this->mockedDbSqlPlatform = $this->getMockBuilder('\Laminas\Db\Sql\Platform\Platform')
            ->setConstructorArgs([$this->mockedDbAdapter])
            ->getMock();
    }

    /**
     *
     * @param  arra $eventListenerArray
     * @return array
     */
    public function setUpMockMapperInsert($mapperMethods)
    {
        $mockBuilder=$this->getMockBuilder(get_class($this->mapper));
        $mockBuilder->onlyMethods($mapperMethods);
        $this->mapper = $mockBuilder->getMock();

        foreach ($mapperMethods as $method) {
            switch ($method) {
                case 'getSelect':
                    $this->mapper->expects($this->once())
                    ->method('getSelect')
                    ->will($this->returnValue($this->mockedSelect));
                    break;
                case 'initialize':
                    $this->mapper->expects($this->once())
                    ->method('initialize')
                    ->will($this->returnValue(true));
                    break;
            }
        }
    }

    /**
     *
     * @param  arra $eventListenerArray
     * @return array
     */
    public function &setUpMockedMapper($eventListenerArray, array $mapperMethods = [])
    {
        $returnMockedParams = [];

        $mapperMethods = count($mapperMethods)
            ? array_merge($mapperMethods, ['getSelect', 'select'])
            : ['getSelect', 'select'];

        $this->setUpMockMapperInsert($mapperMethods);

        $this->mapper->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->mockedResultSet));

        $mockedSelect = $this->mockedSelect;
        $this->mockedSelect->expects($this->once())
            ->method('where')
            ->will(
                $this->returnCallback(
                    function () use (&$returnMockedParams, $mockedSelect) {
                        $returnMockedParams['whereArgs'] = func_get_args();
                        return $mockedSelect;
                    }
                )
            );

        foreach ($eventListenerArray as $eventKey => $eventListener) {
            $this->mapper->getEventManager()->attach($eventKey, $eventListener);
        }

        $this->mapper->setDbAdapter($this->mockedDbAdapter);
        $this->mapper->setEntityPrototype(new Entity());

        return $returnMockedParams;
    }

    /**
     * @dataProvider providerTestFindBy
     * @param        string $method
     * @param        array  $args
     * @param        array  $expectedParams
     */
    public function testFindBy($method, $args, $expectedParams, $eventListener, $entityEqual)
    {
        $mockedParams =& $this->setUpMockedMapper($eventListener);

        $this->mockedResultSet->expects($this->once())
            ->method('current')
            ->will($this->returnValue($entityEqual));

        $return = call_user_func_array([$this->mapper, $method], $args);

        foreach ($expectedParams as $paramKey => $paramValue) {
            $this->assertArrayHasKey($paramKey, $mockedParams);
            $this->assertEquals($paramValue, $mockedParams[$paramKey]);
        }
        $this->assertEquals($entityEqual, $return);
    }

    /**
     * @todo         Integration test for UserMapper
     * @dataProvider providerTestFindBy
     */
    public function testIntegrationFindBy($methode, $args, $expectedParams, $eventListener, $entityEqual)
    {
        /* @var $entityEqual Entity */
        /* @var $dbAdapter Adapter */
        foreach ($this->realAdapter as $dbAdapter) {
            if ($dbAdapter == false) {
                continue;
            }

            $this->mapper->setDbAdapter($dbAdapter);
            $return = call_user_func_array([$this->mapper, $methode], $args);

            $this->assertIsObject($return);
            $this->assertInstanceOf('LmcUser\Entity\User', $return);
            $this->assertEquals($entityEqual, $return);
        }

        if (!isset($return)) {
            $this->markTestSkipped("Without real database we dont can test findByEmail / findByUsername / findById");
        }
    }

    public function testGetTableName()
    {
        $this->assertEquals('user', $this->mapper->getTableName());
    }

    public function testSetTableName()
    {
        $this->mapper->setTableName('LmcUser');
        $this->assertEquals('LmcUser', $this->mapper->getTableName());
    }

    public function testInsertUpdateDelete()
    {
        $baseEntity = new Entity();
        $baseEntity->setEmail('lmc-user-foo@zend-framework.org');
        $baseEntity->setUsername('lmc-user-foo');
        $baseEntity->setPassword('lmc-user-foo');

        /* @var $entityEqual Entity */
        /* @var $dbAdapter Adapter */
        foreach ($this->realAdapter as $diver => $dbAdapter) {
            if ($dbAdapter === false) {
                continue;
            }
            $this->mapper->setDbAdapter($dbAdapter);

            // insert
            $entity = clone $baseEntity;

            $result = $this->mapper->insert($entity);

            $this->assertNotNull($entity->getId());
            $this->assertGreaterThanOrEqual(1, $entity->getId());

            $entityEqual = $this->mapper->findById($entity->getId());
            $this->assertEquals($entity, $entityEqual);

            // update
            $entity->setUsername($entity->getUsername() . '-' . $diver);
            $entity->setEmail($entity->getUsername() . '@github.com');

            $result = $this->mapper->update($entity);

            $entityEqual = $this->mapper->findById($entity->getId());
            $this->assertNotEquals($baseEntity->getUsername(), $entityEqual->getUsername());
            $this->assertNotEquals($baseEntity->getEmail(), $entityEqual->getEmail());

            $this->assertEquals($entity->getUsername(), $entityEqual->getUsername());
            $this->assertEquals($entity->getEmail(), $entityEqual->getEmail());

            /**
             *
             * @todo delete is currently protected

            // delete
            $result = $this->mapper->delete($entity->getId());

            $this->assertNotEquals($baseEntity->getEmail(), $entityEqual->getEmail());
            $this->assertEquals($entity->getEmail(), $entityEqual->getEmail());
             */
        }

        if (!isset($result)) {
            $this->markTestSkipped("Without real database we dont can test insert, update and delete");
        }
    }

    public function providerTestFindBy()
    {
        $user = new Entity();
        $user->setEmail('lmc-user@github.com');
        $user->setUsername('lmc-user');
        $user->setDisplayName('Zfc-User');
        $user->setId('1');
        $user->setState(1);
        $user->setPassword('lmc-user');

        return [
            [
                'findByEmail',
                [$user->getEmail()],
                [
                    'whereArgs'=> [
                        ['email' =>$user->getEmail()],
                        'AND'
                    ]
                ],
                [],
                $user
            ],
            [
                'findByUsername',
                [$user->getUsername()],
                [
                    'whereArgs'=> [
                        ['username' =>$user->getUsername()],
                        'AND'
                    ]
                ],
                [],
                $user
            ],
            [
                'findById',
                [$user->getId()],
                [
                    'whereArgs'=> [
                        ['user_id' =>$user->getId()],
                        'AND'
                    ]
                ],
                [],
                $user
            ],
        ];
    }
}
