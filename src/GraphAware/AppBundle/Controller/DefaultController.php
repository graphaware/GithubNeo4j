<?php

namespace GraphAware\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        $neogh = $this->get('ga.github_neo');
        //echo '<pre>';

        //print_r($neogh->getHowIKnowOthers($this->getUser()->getGithubId()));
        //exit();

        return array(
            'active_users' => (int) $neogh->getActiveUsers(),
            'total_events' => (int) $neogh->getEventsCount(),
            'total_repos' => (int) $neogh->getRepositoriesCount(),
            'stats' => null !== $this->getUser() ? $neogh->getUserStats($this->getUser()->getGithubId()) : null,
            'repo_contrib' => null !== $this->getUser() ? $neogh->getRepositoriesIContributed($this->getUser()->getGithubId()) : null
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
}
