<?php

namespace GraphAware\AppBundle\Service;

use Neoxygen\NeoClient\Client;

class GithubNeo4j
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function findUserById($id)
    {
        $q = 'MATCH (user:ActiveUser) WHERE user.id = {user_id} RETURN user';
        $p = ['user_id' => (int) $id];

        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->get('user');
    }

    public function findUserByLogin($login)
    {
        $q = 'MATCH (user:ActiveUser) WHERE user.login = {user_login} RETURN user';
        $p = ['user_login' => (string) $login];

        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->get('user');
    }

    public function setUserAsActive($id)
    {
        $q = 'MATCH (user:User) WHERE user.id = {user_id}
        SET user :ActiveUser
        RETURN user';
        $p = ['user_id' => (int) $id];

        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->get('user');
    }

    public function getActiveUsers()
    {
        $q = 'MATCH (user:ActiveUser) RETURN count(user) as count';

        $result = $this->client->sendCypherQuery($q)->getResult();

        return $result->get('count');
    }

    public function getEventsCount()
    {
        $q = 'MATCH (event:GithubEvent) RETURN count(event) as count';
        $result = $this->client->sendCypherQuery($q)->getResult();

        return $result->get('count');
    }

    public function getRepositoriesCount()
    {
        $q = 'MATCH (repo:Repository) RETURN count(repo) as count';
        $result = $this->client->sendCypherQuery($q)->getResult();

        return $result->get('count');
    }
}