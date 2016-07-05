<?php

namespace Andy\Bundle\BugTrackBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

abstract class IssuesDataLoader extends AbstractFixture implements ContainerAwareInterface
{
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

    public function loadIssues(ObjectManager $manager, $issuesData)
    {
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

    public function loadUsers(ObjectManager $manager, array $usersData)
    {
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
                            ->findOneBy(['role' => $data['role']])
                    )
                    ->setOwner($businessUnit);

                $userManager->updateUser($user);
            }

            $this->setReference($user->getUsername(), $user);
        }
    }
}
