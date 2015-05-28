<?php

namespace GraphAware\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        $neogh = $this->get('ga.github_neo');

        return array(
            'active_users' => (int) $neogh->getActiveUsers(),
            'total_events' => (int) $neogh->getEventsCount(),
            'total_repos' => (int) $neogh->getRepositoriesCount(),
            'stats' => null !== $this->getUser() ? $neogh->getUserStats($this->getUser()->getGithubId()) : null,
            'repo_contrib' => null !== $this->getUser() ? $neogh->getRepositoriesIContributed($this->getUser()->getGithubId()) : null
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/me", name="myneohub")
     * @Template()
     */
    public function myNeoHubAction()
    {
        $user = $this->getUser()->getLogin();
        $neo = $this->get('ga.github_neo');
        $u = $neo->findUserByLogin($user);

        return array(
            'user' => $u
        );
    }

    /**
     * @Template()
     */
    public function userEventsCountDetailedAction()
    {
        $neo = $this->get('ga.github_neo');
        $user = $this->getUser()->getLogin();

        $events = $neo->getUserEventsCountDetailed($user);
        $keys = array_keys($events[0]);
        $c = $neo->getUserEventsCount($user);

        return array(
            'keys' => $keys,
            'events' => $events,
            'user' => $user,
            'count' => $c
        );
    }

    /**
     * @Template()
     */
    public function userEventsDayDiffAction()
    {
        $neo = $this->get('ga.github_neo');
        $user = $this->getUser()->getLogin();
        $c = $neo->getUserEventsDayDiff($user);

        return array(
            'user' => $user,
            'count' => $c
        );
    }

    /**
     * @Template()
     */
    public function userLastRepoInteractedAction()
    {
        $neo = $this->get('ga.github_neo');
        $user = $this->getUser()->getLogin();
        $repos = $neo->getUserLastRepoInteraction($user);

        return array(
            'repos' => $repos,
            'user' => $user
        );
    }

    /**
     * @Template()
     */
    public function userRepoLanguagesAction()
    {
        $neo = $this->get('ga.github_neo');
        $user = $this->getUser()->getLogin();
        $languages = $neo->getUserRepoLanguages($user);
        $cl = count($languages);

        return array(
            'languages' => $languages,
            'user' => $user,
            'cl' => $cl
        );
    }



    /**
     * @Route("/createIndexes")
     */
    public function createIndexesAction()
    {
        $importer = $this->get('ga.github_importer');
        $importer->createIndexes();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/github-users-count", name="users_count")
     * @Method("GET")
     */
    public function usersCountAction()
    {
        $neoService = $this->get('ga.github_neo');
        $response = new JsonResponse();
        $usersCount = $neoService->getUsersCount();
        $response->setData(['count' => $usersCount]);

        return $response;
    }

    /**
     * @Route("/github-repos-count", name="repos_count")
     * @Method("GET")
     */
    public function reposCountAction()
    {
        $neoService = $this->get('ga.github_neo');
        $response = new JsonResponse();
        $reposCount = $neoService->getRepositoriesCount();
        $response->setData(['count' => $reposCount]);

        return $response;
    }

    /**
     * @Route("/github-events-count", name="events_count")
     * @Method("GET")
     */
    public function eventsCountAction()
    {
        $neoService = $this->get('ga.github_neo');
        $response = new JsonResponse();
        $eventsCount = $neoService->getEventsCount();
        $response->setData(['count' => $eventsCount]);

        return $response;
    }

    /**
     * @Route("/github-rels-count", name="rels_count")
     * @Method("GET")
     */
    public function relsCountAction()
    {
        $neoService = $this->get('ga.github_neo');
        $response = new JsonResponse();
        $relsCount = $neoService->getRelsCount();
        $response->setData(['count' => $relsCount]);

        return $response;
    }
}
