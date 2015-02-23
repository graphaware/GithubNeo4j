<?php

namespace GraphAware\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $neogh = $this->get('ga.github_neo');

        return array(
            'active_users' => (int) $neogh->getActiveUsers(),
            'total_events' => (int) $neogh->getEventsCount(),
            'total_repos' => (int) $neogh->getRepositoriesCount()
        );
    }
}
