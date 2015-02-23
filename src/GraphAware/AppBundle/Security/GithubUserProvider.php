<?php

namespace GraphAware\AppBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use GraphAware\AppBundle\Service\GithubImporter,
    GraphAware\AppBundle\Service\GithubClient,
    GraphAware\AppBundle\Service\GithubNeo4j;

class GithubUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{

    protected $githubNeo4j;

    protected $githubImporter;

    protected $githubClient;

    public function __construct(GithubNeo4j $githubNeo4j, GithubImporter $githubImporter, GithubClient $githubClient)
    {
        $this->githubNeo4j = $githubNeo4j;
        $this->githubImporter = $githubImporter;
        $this->githubClient = $githubClient;
    }

    /**
     * Authenticate a user by Oauth Service Owner
     *
     * @param UserResponseInterface $response
     * @return bool|mixed|null|User
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userId = $response->getResponse()['id'];
        $userLogin = $response->getResponse()['login'];
        $token = $response->getAccessToken();

        $user = new User();
        $user->setGithubId($userId);
        $user->setLogin($userLogin);
        $user->setToken($token);

        if (null === $this->githubNeo4j->findUserById($user->getGithubId())) {
            $events = $this->githubClient->getEvents($user->getLogin());
            $this->githubImporter->import($events);

            $this->githubNeo4j->setUserAsActive($user->getGithubId());
            $user->setIsActiveUser();

        }

        return $user;
    }

    /**
     * @inherit
     */
    public function loadUserByUsername($username)
    {
        return null;
    }

    /**
     * @inherit
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * @inherit
     */
    public function supportsClass($class)
    {

        return $class === 'BaconFinder\AppBundle\Model\User';
    }

}