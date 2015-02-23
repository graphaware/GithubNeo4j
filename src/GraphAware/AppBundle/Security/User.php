<?php

namespace GraphAware\AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface, EquatableInterface
{
    protected $roles;

    protected $login;

    protected $token;

    protected $githubId;

    protected $password;

    protected $salt;

    protected $isActiveUser;

    public function __construct()
    {
        $this->isActiveUser = false;
    }

    /**
     * Returns the username
     *
     * @return mixed|null|string username
     */
    public function getUsername()
    {
        return $this->login;
    }
    /**
     * @return mixed
     */
    public function getRoles()
    {
        return array(
            'ROLE_USER'
        );
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function eraseCredentials()
    {

    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @param mixed $githubId
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;
    }

    /**
     * @return mixed
     */
    public function getIsActiveUser()
    {
        return $this->isActiveUser;
    }

    /**
     * @param mixed $isActiveUser
     */
    public function setIsActiveUser()
    {
        $this->isActiveUser = true;
    }
}