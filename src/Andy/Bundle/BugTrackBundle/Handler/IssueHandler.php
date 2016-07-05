<?php

namespace Andy\Bundle\BugTrackBundle\Handler;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
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
     * @var TokenInterface
     */
    private $token;

    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * Constructor.
     *
     * @param TokenStorage   $tokenStorage
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(TokenStorage $tokenStorage, DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
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
     * @return array
     */
    public function getIssueTypes($isSubtask = false)
    {
        if ($isSubtask) {
            return [$this->getIssueTypeReference(Issue::TYPE_SUBTASK)];
        } else {
            return [
                $this->getIssueTypeReference(Issue::TYPE_BUG),
                $this->getIssueTypeReference(Issue::TYPE_TASK),
                $this->getIssueTypeReference(Issue::TYPE_STORY),
            ];
        }
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

        $this->doctrineHelper->getEntityManager(Issue::class)->flush();
    }

    /**
     * @param Note $note
     */
    public function addNoteCollaborators(Note $note)
    {
        $issue = $note->getTarget();

        if ($this->getUser()) {
            $issue->addCollaborator($this->getUser());
        } else {
            $issue->addCollaborator($note->getOwner());
        }

        $this->doctrineHelper->getEntityManager(Issue::class)->flush();
    }

    /**
     * @param Note $note
     */
    public function setIssueUpdatedAtWithNote(Note $note)
    {
        /** @var Issue $issue */
        $issue = $note->getTarget();

        $issue->setUpdatedAt(new \DateTime('now'));

        $this->doctrineHelper->getEntityManager(Issue::class)->flush();
    }

    /**
     * @param $issueTypeName
     * 
     * @return bool|\Doctrine\Common\Proxy\Proxy|null|object
     * 
     * @throws \Doctrine\ORM\ORMException
     */
    public function getIssueTypeReference($issueTypeName)
    {
        $issueTypeClass = ExtendHelper::buildEnumValueClassName('issue_type');
        $entityManager = $this->doctrineHelper->getEntityManager($issueTypeClass);

        return $entityManager->getReference(
            $issueTypeClass,
            lcfirst($issueTypeName)
        );
    }
}
