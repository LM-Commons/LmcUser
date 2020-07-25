<?php

namespace LmcUser\Mapper;

interface UserInterface
{
    /**
     * @param  $email
     * @return \LmcUser\Entity\UserInterface
     */
    public function findByEmail($email);

    /**
     * @param  string $username
     * @return \LmcUser\Entity\UserInterface
     */
    public function findByUsername($username);

    /**
     * @param  string|int $id
     * @return \LmcUser\Entity\UserInterface
     */
    public function findById($id);

    /**
     * @param \LmcUser\Entity\UserInterface $user
     */
    public function insert(\LmcUser\Entity\UserInterface $user);

    /**
     * @param \LmcUser\Entity\UserInterface $user
     */
    public function update(\LmcUser\Entity\UserInterface $user);
}
