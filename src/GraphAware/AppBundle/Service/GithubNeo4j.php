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

    public function getUsersCount()
    {
        $q = 'MATCH (user:User) RETURN count(user) as count';
        $result = $this->client->sendCypherQuery($q)->getResult();

        return $result->get('count');
    }

    public function getRelsCount()
    {
        $q = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() RETURN count(r) as c';
        $result = $this->client->sendCypherQuery($q)->getResult();

        return $result->get('c');
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

    public function getUserStats($userId)
    {
        $q = 'MATCH (user:User) WHERE user.id = {user_id}
        MATCH (user)-[:LAST_EVENT|PREVIOUS_EVENT*]->(event)
        RETURN event.type as eventType, count(*) as c';
        $p = [
            'user_id' => (int) $userId
        ];

        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->getTableFormat();
    }

    public function getRepositoriesIContributed($userId)
    {
        $q = 'MATCH (user:User) WHERE user.id = {user_id}
        MATCH (user)-[:LAST_EVENT|PREVIOUS_EVENT*]->(event)
        MATCH (event)-[:MERGED_PR|OPENED_PR|PUSHED]->(step)
        MATCH (step)-[*1..4]->(repo:Repository)
        RETURN DISTINCT(repo.name) as repos';
        $p = ['user_id' => $userId];
        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->get('repos');
    }

    public function getHowIKnowOthers($userId)
    {
        $q = 'MATCH (user:User) WHERE user.id = {user_id}
        MATCH (others:User) WHERE others <> user
        WITH user, collect(others) as o
        UNWIND o as other
        MATCH p=shortestPath((user)-[*]-(other))
        RETURN p';
        $p = ['user_id' => $userId];
        return $this->client->sendCypherQuery($q, $p)->getBody();
        $result = $this->client->sendCypherQuery($q, $p)->getResult();

        return $result->get('p');
    }

    public function getUserId($username)
    {
        $user = trim($username);
        $q = 'MATCH (user:ActiveUser {login: {user}}) RETURN user.id as uid';

        return $this->client->sendCypherQuery($q, ['user' => $user])->getResult()->get('uid');
    }

    public function getUserEventsCountDetailed($user)
    {
        $q = 'MATCH (user:User {login: {user}})
        MATCH (user)-[:LAST_EVENT|:PREVIOUS_EVENT*]->(event)
        RETURN event.type as type, count(*) as count';

        return $this->client->sendCypherQuery($q, ['user' => $user])->getResult()->getTableFormat();
    }

    public function getUserEventsCount($user)
    {
        $q = 'MATCH (user:User {login: {user}})
        MATCH (user)-[:LAST_EVENT|:PREVIOUS_EVENT*]->(event)
        RETURN count(event) as c';

        return $this->client->sendCypherQuery($q, ['user' => $user])->getResult()->get('c');
    }

    public function getUserEventsDayDiff($user)
    {
        $q = 'MATCH (n:User {login: {user}})
MATCH (n)-[:LAST_EVENT]-(lastEvent)-[:PREVIOUS_EVENT*0..]->(event)
WITH collect(event) as events
WITH events[0] as last, events[size(events)-1] as first
RETURN (last.time - first.time) / 86400000 as count';

        return $this->client->sendCypherQuery($q, ['user' => $user])->getResult()->get('count');
    }
}
