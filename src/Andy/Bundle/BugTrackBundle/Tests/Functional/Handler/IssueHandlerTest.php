<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional\Handler;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * @dbIsolation
 */
class IssueHandlerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());

        $this->loadFixtures([
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestUserData',
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestIssueData',
        ]);
    }

    public function testGetIssueTypesForSubtask()
    {
        /** @var array $issueTypes */
        $issueTypes = $this->getContainer()->get('andy_bug_track.handler.issue_handler')->getIssueTypes(true);

        $this->assertEquals(1, count($issueTypes));
        $issueTypeClass = ExtendHelper::buildEnumValueClassName('issue_type');
        $this->assertTrue($issueTypes[0] instanceof $issueTypeClass);
        $this->assertEquals($issueTypes[0]->getName(), Issue::TYPE_SUBTASK);
    }

    public function testGetIssueTypesForNonSubtask()
    {
        /** @var array $issueTypes */
        $issueTypes = $this->getContainer()->get('andy_bug_track.handler.issue_handler')->getIssueTypes(false);

        $this->assertEquals(3, count($issueTypes));
    }

    public function testAddIssueCollaborators()
    {
        /** @var User $user1 */
        $user1 = $this->getReference('test_admin');
        
        /** @var User $user2 */
        $user2 = $this->getReference('test_user');
        
        /** @var Issue $issue */
        $issue = new Issue();
        $issue->setReporter($user1);
        $issue->setAssignee($user2);
        $this->assertEmpty($issue->getCollaborators());

        $this->getContainer()->get('andy_bug_track.handler.issue_handler')->addIssueCollaborators($issue);
        $this->assertEquals(2, count($issue->getCollaborators()));
    }

    public function testAddNoteCollaborators()
    {
        /** @var User $user1 */
        $user1 = $this->getReference('test_admin');

        /** @var Issue $issue */
        $issue = new Issue();
        
        /** @var Note $note */
        $note = new Note();
        $note->setTarget($issue);
        $note->setOwner($user1);

        $this->getContainer()->get('andy_bug_track.handler.issue_handler')->addNoteCollaborators($note);

        $this->assertEquals(1, count($issue->getCollaborators()));
        $this->assertEquals($issue->getCollaborators()[0]->getId(), $user1->getId());
    }

    public function testSetIssueUpdatedAtWithNote()
    {
        /** @var Issue $issue */
        $issue = new Issue();

        /** @var Note $note */
        $note = new Note();
        $note->setTarget($issue);
        $this->assertEmpty($issue->getUpdatedAt());
        
        $this->getContainer()->get('andy_bug_track.handler.issue_handler')->setIssueUpdatedAtWithNote($note);
        $this->assertEquals(new \DateTime('now'), $issue->getUpdatedAt());
        
    }
}
