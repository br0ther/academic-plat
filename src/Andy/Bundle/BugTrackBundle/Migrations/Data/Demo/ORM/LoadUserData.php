<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\Demo\ORM;

use Andy\Bundle\BugTrackBundle\DataFixtures\ORM\IssuesDataLoader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadRolesData;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends IssuesDataLoader implements OrderedFixtureInterface
{
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
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
    
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

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager, $this->demoUserData);
    }
}
