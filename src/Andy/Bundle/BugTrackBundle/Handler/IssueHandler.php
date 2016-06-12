<?php

namespace Andy\Bundle\BugTrackBundle\Handler;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

/**
 * Class IssueHandler
 */
class IssueHandler extends BaseHandler
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param bool $isSubtask
     * @return mixed
     */
    public function getIssueTypes($isSubtask = false)
    {
        $className = ExtendHelper::buildEnumValueClassName('issue_type');

        $qb = $this->em
            ->createQueryBuilder()
            ->from($className, 't')
            ->select('t');
        
        if ($isSubtask) {
            $qb->where('t.id = :subtask');
        } else {
            $qb->where('t.id <> :subtask');
        }

        $qb->setParameter('subtask', lcfirst(Issue::TYPE_SUBTASK));

        return $qb->getQuery()->getResult();
    }

}
