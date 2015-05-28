<?php

namespace GraphAware\AppBundle\Service;

use Ikwattro\GithubEvent\EventHandler;
use Ikwattro\Github2Cypher\Github2CypherConverter;
use Ikwattro\GithubEvent\Exception\EventNotHandledException;
use GraphAware\AppBundle\Service\GithubClient;
use Neoxygen\NeoClient\Client;

class GithubImporter
{
    protected $eventHandler;

    protected $eventConverter;

    protected $client;

    protected $github;

    public function __construct(Client $client, GithubClient $github)
    {
        $this->eventHandler = EventHandler::create()
            ->build();
        $this->eventConverter = new Github2CypherConverter();
        $this->client = $client;
        $this->github = $github;
    }

    public function import(array $events)
    {
        $transaction = $this->client->prepareTransaction();

        foreach ($events as $event) {
                try {
                    $evObject = $this->eventHandler->handleEvent($event);
                    try {
                        $statements = $this->eventConverter->convert($evObject);
                        foreach ($statements as $statement) {
                            $transaction->pushQuery($statement['query'], $statement['params']);
                        }
                    } catch (\InvalidArgumentException $e) {

                    }
                } catch (EventNotHandledException $e) {

                }
            }

        $this->client->commitPreparedTransaction($transaction);

        return $this->fetchRepoLanguages();
    }

    public function fetchRepoLanguages()
    {
        $q = 'MATCH (r:Repository)-[:OWNED_BY]->(owner) WHERE NOT HAS(r.processed) RETURN r.name as repo, owner.login as login, r.id as repoId';
        $results = $this->client->sendCypherQuery($q)->getResult()->getTableFormat();
        if (!empty($results)) {
            $tx = $this->client->prepareTransaction();
            foreach ($results as $result) {
                $languages = $this->github->getRepoLanguages($result['repo'], $result['login']);
                foreach ($languages as $l => $nbr) {
                    $q = 'MATCH (r:Repository {id: {repo_id}})
                    SET r.processed = 1
                    MERGE (l:Language {name: {l}})
                    MERGE (r)-[:WRITTEN_IN_LANGUAGE {lines: {lines}}]->(l)';
                    $p = ['repo_id' => $result['repoId'], 'l' => $l, 'lines' => $nbr];
                    $tx->pushQuery($q, $p);
                }
            }
            return $tx->commit();
        }

        return true;

    }

    public function createIndexes()
    {
        foreach ($this->eventConverter->getInitialSchemaConstraints() as $constraint) {
            $this->client->createUniqueConstraint($constraint['label'], $constraint['property'], true);
        }

        foreach ($this->eventConverter->getInitialSchemaIndexes() as $index) {
            $this->client->createIndex($index['label'], $index['property']);
        }
    }
}