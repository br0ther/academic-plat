<?php

namespace Andy\Bundle\BugTrackBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class IssueRepository
 * @package Andy\Bundle\BugTrackBundle\Entity\Repository
 */
class IssueRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getIssuesByStatus()
    {
        return $this->createQueryBuilder('issue')
            ->select('count(issue.id) as issues_count, status.label')
            ->leftJoin('issue.workflowStep', 'status')
            ->groupBy('status.label, status.stepOrder')
            ->orderBy('status.stepOrder')
            ->getQuery()
            ->getArrayResult();
    }
}
