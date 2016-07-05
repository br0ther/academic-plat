<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures;

use Andy\Bundle\BugTrackBundle\DataFixtures\ORM\IssuesDataLoader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadRolesData;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestUserData extends IssuesDataLoader implements OrderedFixtureInterface
{
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
        $this->loadUsers($manager, $this->testUserData);
    }
}
