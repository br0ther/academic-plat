<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadRolesData;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * User names used for data migration
     */   
    const USER_ADMIN_NAME = 'test_user_admin';
    const USER_ONE_NAME = 'test_user_one';
    const USER_TWO_NAME = 'test_user_two1';
    
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
        $userManager = $this->container->get('oro_user.manager');

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $businessUnit = $manager
            ->getRepository('OroOrganizationBundle:BusinessUnit')
            ->findOneBy(['name' => LoadOrganizationAndBusinessUnitData::MAIN_BUSINESS_UNIT]);

        $adminRole = $manager->getRepository('OroUserBundle:Role')
            ->findOneBy(['role' => LoadRolesData::ROLE_ADMINISTRATOR]);
        $userAdmin = $manager->getRepository('OroUserBundle:User')->findOneByUsername(self::USER_ADMIN_NAME);
        $userOne = $manager->getRepository('OroUserBundle:User')->findOneByUsername(self::USER_ONE_NAME);
        $userTwo = $manager->getRepository('OroUserBundle:User')->findOneByUsername(self::USER_TWO_NAME);

        if (empty($userAdmin)) {
            /** @var User $userAdmin */
            $userAdmin = $userManager->createUser();
            $userAdmin
                ->setUsername(self::USER_ADMIN_NAME)
                ->setEmail(self::USER_ADMIN_NAME . '@test.com')
                ->setEnabled(true)
                ->setPlainPassword('123')
                ->addRole($adminRole)
                ->setOrganization($organization)
                ->addOrganization($organization)
                ->addBusinessUnit($businessUnit)
                ->setOwner($businessUnit)
                ->setFirstName('Test')
                ->setLastName('Admin');

            $userManager->updateUser($userAdmin);
        }

        if (empty($userOne)) {
            /** @var User $userOne */
            $userOne = $userManager->createUser();
            $userOne
                ->setUsername(self::USER_ONE_NAME)
                ->setEmail(self::USER_ONE_NAME . '@test.com')
                ->setEnabled(true)
                ->setPlainPassword('123')
                ->setOrganization($organization)
                ->addOrganization($organization)
                ->addBusinessUnit($businessUnit)
                ->setFirstName('User')
                ->setLastName('One');

            $userManager->updateUser($userOne);
        }

        if (empty($userTwo)) {
            /** @var User $userTwo */
            $userTwo = $userManager->createUser();
            $userTwo
                ->setUsername(self::USER_TWO_NAME)
                ->setEmail(self::USER_TWO_NAME . '@test.com')
                ->setEnabled(true)
                ->setPlainPassword('123')
                ->setOrganization($organization)
                ->addOrganization($organization)
                ->addBusinessUnit($businessUnit)
                ->setFirstName('User')
                ->setLastName('Two');

            $userManager->updateUser($userTwo);
        }

        $this->setReference(self::USER_ADMIN_NAME, $userAdmin);
        $this->setReference(self::USER_ONE_NAME, $userOne);
        $this->setReference(self::USER_TWO_NAME, $userTwo);

        $manager->flush();
    }
}
