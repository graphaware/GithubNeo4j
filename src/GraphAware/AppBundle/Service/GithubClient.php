<?php

namespace GraphAware\AppBundle\Service;

use GuzzleHttp\Client;

class GithubClient
{
    const BASE_URL = 'https://api.github.com';

    protected $httpClient;

    protected $clientId;

    protected $clientSecret;

    protected $totalEvents;

    public function __construct($clientId, $clientSecret, $totalEvents)
    {
        $this->clientId = (string) $clientId;
        $this->clientSecret = (string) $clientSecret;
        $this->totalEvents = round($totalEvents / 100);

        $this->httpClient = new Client(array('base_url' => self::BASE_URL));
    }

    public function getEvents($user)
    {
        if (null === $user) {
            throw new \InvalidArgumentException('The user cannot be null');
        }

        $events = [];
        for ($i = 1; $i < $this->totalEvents; $i++) {
            $ev = $this->doCall($user, $i);
            foreach ($ev as $e) {
                $events[] = $e;
            }
        }

        $eve = array_reverse($events);
        //echo json_encode($eve, JSON_PRETTY_PRINT);
        //exit();

        return $eve;
    }

    public function getRepoLanguages($repo, $owner)
    {
        $response = $this->httpClient->get(
            self::BASE_URL.'/repos/' . $owner . '/' . $repo . '/languages?client_id=' . $this->clientId . '&client_secret=' . $this->clientSecret
        );
        $body = json_decode((string) $response->getBody());

        return $body;
    }

    private function doCall($user, $page = 1)
    {
        $response = $this->httpClient->get(
            self::BASE_URL . '/users/' . $user . '/events/public?client_id=' . $this->clientId . '&client_secret=' . $this->clientSecret . '&per_page=100&page=' . $page);

        $body = json_decode((string) $response->getBody(), true);

        return $body;
    }


}