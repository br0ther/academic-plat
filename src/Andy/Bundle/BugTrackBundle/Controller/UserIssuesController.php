<?php

namespace Andy\Bundle\BugTrackBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

/**
 * @Route("/issue")
 */
class UserIssuesController extends Controller
{
    /**
     * @Route("/user/{userId}", name="user_issues", requirements={"userId"="\d+"})
     * @AclAncestor("issue_view")
     * @Template
     * @param int $userId
     * @return array
     */
    public function issuesAction($userId)
    {
        return [
            'userId' => $userId,
            'entity' => $this->getUser()
        ];
    }
}
