<?php

namespace LmcUser\Mapper;

use Laminas\Hydrator\HydratorInterface;
use LmcUser\Entity\UserInterface as UserEntityInterface;
use LmcUser\Mapper\Exception\InvalidArgumentException;

/**
 * Class UserHydrator
 */
class UserHydrator implements HydratorInterface
{
    /** @var HydratorInterface */
    private $hydrator;

    /**
     * UserHydrator constructor.
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Extract values from an object
     *
     * @param  UserEntityInterface $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object): array
    {
        if (!$object instanceof UserEntityInterface) {
            throw new InvalidArgumentException('$object must be an instance of LmcUser\Entity\UserInterface');
        }

        $data = $this->hydrator->extract($object);
        if ($data['id'] !== null) {
            $data = $this->mapField('id', 'user_id', $data);
        } else {
            unset($data['id']);
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array               $data
     * @param  UserEntityInterface $object
     * @return UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of LmcUser\Entity\UserInterface');
        }

        $data = $this->mapField('user_id', 'id', $data);

        return $this->hydrator->hydrate($data, $object);
    }

    /**
     * @param string $keyFrom
     * @param string $keyTo
     * @param  array  $array
     * @return array
     */
    protected function mapField(string $keyFrom, string $keyTo, array $array): array
    {
        if (isset($array[$keyFrom])) {
            $array[$keyTo] = $array[$keyFrom];
            unset($array[$keyFrom]);
        }

        return $array;
    }
}
