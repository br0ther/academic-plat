<?php

namespace Andy\Bundle\BugTrackBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadRolesData;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

abstract class IssuesDataLoader extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $demoIssueData = [
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
     * @var array
     */
    protected $demoUserData = [
        [
            'name'     => 'demo_admin',
            'email'    => 'demo_admin@test.com',
            'password' => '123',
            'first'    => 'Andy',
            'last'     => 'Last',
            'role'     => LoadRolesData::ROLE_ADMINISTRATOR
        ],
        [
            'name'     => 'demo_manager',
            'email'    => 'demo_manager@test.com',
            'password' => '123',
            'first'    => 'Rob',
            'last'     => 'Magellan',
            'role'     => LoadRolesData::ROLE_MANAGER
        ],
        [
            'name'     => 'demo_user',
            'email'    => 'demo_user@test.com',
            'password' => '123',
            'first'    => 'Joe',
            'last'     => 'Smith',
            'role'     => LoadRolesData::ROLE_USER
        ],
    ];

    /**
     * @var array
     */
    protected $testUserData = [
        [
            'name'     => 'test_admin',
            'email'    => 'test_admin@test.com',
            'password' => '123',
            'first'    => 'Brian',
            'last'     => 'Adams',
            'role'     => LoadRolesData::ROLE_ADMINISTRATOR
        ],
        [
            'name'     => 'test_user',
            'email'    => 'test_user@test.com',
            'password' => '123',
            'first'    => 'Alan',
            'last'     => 'Clark',
            'role'     => LoadRolesData::ROLE_USER
        ],
    ];

    /**
     * @var array
     */
    protected $testIssueData = [
        [
            'code'     => 'TEST-0001',
            'summary'  => 'Test story 1',
            'type'     => 'Story',
            'priority' => 'Trivial',
            'notes'    => ['Test note One', 'Test note Two']
        ],
        [
            'code'     => 'TEST-0002',
            'summary'  => 'Test bug 1',
            'type'     => 'Bug',
            'priority' => 'Trivial'
        ],
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function loadIssues(ObjectManager $manager, $issuesData) {
        $issueRepo = $manager->getRepository('AndyBugTrackBundle:Issue');
        $priorityRepo = $manager->getRepository('AndyBugTrackBundle:Priority');
        $users = $manager->getRepository('OroUserBundle:User')->findBy(['enabled' => 1]);
        $users_count = count($users);
        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $issueTypeRepo = $manager->getRepository(ExtendHelper::buildEnumValueClassName('issue_type'));

        foreach ($issuesData as $data) {
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
            $this->setReference($data['code'], $issue);
            
            $manager->flush();
        }
    }

    public function loadUsers(ObjectManager $manager, array $usersData) {
        $userManager = $this->container->get('oro_user.manager');
        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $businessUnit = $manager
            ->getRepository('OroOrganizationBundle:BusinessUnit')
            ->findOneBy(['name' => LoadOrganizationAndBusinessUnitData::MAIN_BUSINESS_UNIT]);

        foreach ($usersData as $data) {
            $user = $manager->getRepository('OroUserBundle:User')->findOneByUsername($data['name']);

            if (empty($user)) {
                /** @var User $user */
                $user = $userManager->createUser();
                $user
                    ->setUsername($data['name'])
                    ->setEmail($data['email'])
                    ->setEnabled(true)
                    ->setPlainPassword($data['password'])
                    ->setOrganization($organization)
                    ->addOrganization($organization)
                    ->addBusinessUnit($businessUnit)
                    ->setFirstName($data['first'])
                    ->setLastName($data['last'])
                    ->addRole(
                        $manager->getRepository('OroUserBundle:Role')
                            ->findOneBy(['role' => $data['role']]))
                    ->setOwner($businessUnit);

                $userManager->updateUser($user);
            }

            $this->setReference($user->getUsername(), $user);
        }
    }
}
