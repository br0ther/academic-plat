<?php
namespace Andy\Bundle\BugTrackBundle\Bundle\Tests\Functional\Entity\Repository;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolation
 */
class IssueRepositoryTest extends WebTestCase
{
    public function testGetIssuesByStatus()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
        $this->loadFixtures(
            [
                '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestUserData',
                '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestIssueData',
            ]
        );
        /** @var EntityManager $em */
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $statuses = $em
            ->getRepository('AndyBugTrackBundle:Issue')->getIssuesByStatus();

        $this->assertNotEmpty($statuses);
    }
}
