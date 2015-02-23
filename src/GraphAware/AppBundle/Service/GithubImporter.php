<?php

namespace GraphAware\AppBundle\Service;

use Ikwattro\GithubEvent\EventHandler;
use Ikwattro\Github2Cypher\Github2CypherConverter;
use Ikwattro\GithubEvent\Exception\EventNotHandledException;
use Neoxygen\NeoClient\Client;

class GithubImporter
{
    protected $eventHandler;

    protected $eventConverter;

    protected $client;

    public function __construct(Client $client)
    {
        $this->eventHandler = EventHandler::create()
            ->build();
        $this->eventConverter = new Github2CypherConverter();
        $this->client = $client;
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

        return $this->client->commitPreparedTransaction($transaction);
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