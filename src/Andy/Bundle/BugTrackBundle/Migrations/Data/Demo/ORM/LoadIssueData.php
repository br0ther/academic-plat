<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var array
     */
    protected $data = [
        [
            'code'     => 'BT-0001',
            'summary'  => 'Story 1',
            'type'     => 'Story',
            'priority' => 'Trivial',
            'notes'    => ['Note One', 'Note Two']
        ],
        [
            'code'     => 'BT-0002',
            'summary'  => 'Bug 1',
            'type'     => 'Bug',
            'priority' => 'Critical',
        ],
        [
            'code'     => 'BT-0003',
            'summary'  => 'Bug 2',
            'priority' => 'Major',
            'type'     => 'Bug',
            'related'  => ['BT-0001', 'BT-0002']
        ],
        [
            'code'     => 'BT-0004',
            'summary'  => 'Task 1',
            'priority' => 'Critical',
            'type'     => 'Task',
        ],
        [
            'code'     => 'BT-0005',
            'summary'  => 'Task 2',
            'priority' => 'Critical',
            'type'     => 'Task'
        ],
        [
            'code'     => 'BT-0006',
            'summary'  => 'Task 3',
            'priority' => 'Critical',
            'type'     => 'Task'
        ],
        [
            'code'     => 'BT-0007',
            'summary'  => 'Sub-task(BT-0001)',
            'type'     => 'Subtask',
            'priority' => 'Critical',
            'parent'   => 'BT-0001',
            'related'  => ['BT-0005']
        ],
        [
            'code'     => 'BT-0008',
            'summary'  => 'Sub-task(BT-0001)',
            'type'     => 'Subtask',
            'priority' => 'Trivial',
            'parent'   => 'BT-0001'
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return ['Andy\Bundle\BugTrackBundle\Migrations\Data\Demo\ORM\LoadUserData'];
    }
    
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueRepo = $manager->getRepository('AndyBugTrackBundle:Issue');
        $priorityRepo = $manager->getRepository('AndyBugTrackBundle:Priority');
        $users = $manager->getRepository('OroUserBundle:User')->findBy(['enabled' => 1]);
        $users_count = count($users);
        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $issueTypeRepo = $manager->getRepository(ExtendHelper::buildEnumValueClassName('issue_type'));

        foreach ($this->data as $data) {
            $commentator = $users[rand(0, $users_count - 1)];
            $assignee = $users[rand(0, $users_count - 1)];
            $reporter = $users[rand(0, $users_count - 1)];
            $issue = new Issue();
            $issue->setCode($data['code'])
                ->setSummary($data['summary'])
                ->setDescription($data['summary'])
                ->setPriority($priorityRepo->findOneBy(['name' => $data['priority']]))
                ->setReporter($reporter)
                ->setAssignee($assignee)
                ->setOrganization($organization)
                ->setType($issueTypeRepo->findOneById($data['type']));

            if (!empty($data['parent'])) {
                $parent = $issueRepo->findOneBy(['code' => $data['parent']]);
                $issue->setParentIssue($parent);
            }

            if (!empty($data['related'])) {
                foreach ($data['related'] as $related) {
                    $relatedIssue = $issueRepo->findOneBy(['code' => $related]);
                    $issue->addRelatedIssue($relatedIssue);
                }
            }

            $manager->persist($issue);

            if (!empty($data['notes'])) {
                foreach ($data['notes'] as $noteMessage) {
                    $note = new Note();
                    $note->setMessage($noteMessage)
                        ->setOrganization($organization)
                        ->setOwner($commentator)
                        ->setTarget($issue);
                    $manager->persist($note);
                }
            }

            $manager->flush();
        }
    }
}
