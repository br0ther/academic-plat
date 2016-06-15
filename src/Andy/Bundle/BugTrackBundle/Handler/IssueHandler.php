<?php

namespace Andy\Bundle\BugTrackBundle\Handler;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class IssueHandler
 */
class IssueHandler
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param TokenStorage  $tokenStorage
     */
    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->token = $tokenStorage->getToken();
    }

    /**
     * Get current user
     *
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if ($this->token) {
            return $this->token->getUser();
        }

        return null;
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

    /**
     * @param Issue $issue
     */
    public function addIssueCollaborators(Issue $issue)
    {
        $collaborators = [];
        $collaborators[] = $issue->getReporter();
        $collaborators[] = $issue->getAssignee();

        foreach ($collaborators as $collaborator) {
            $issue->addCollaborator($collaborator);
        }

        $this->em->flush();
    }

    /**
     * @param Note $note
     */
    public function addNoteCollaborators(Note $note)
    {
        $issue = $note->getTarget();

        $issue->addCollaborator($this->getUser());

        $this->em->flush();
    }

    /**
     * @param Note $note
     */
    public function setIssueUpdatedAtWithNote(Note $note)
    {
        /** @var Issue $issue */
        $issue = $note->getTarget();

        $issue->setUpdatedAt(new \DateTime('now'));

        $this->em->flush();
    }
}
