<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures;

use Andy\Bundle\BugTrackBundle\DataFixtures\ORM\IssuesDataLoader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TestIssueData extends IssuesDataLoader implements OrderedFixtureInterface
{
    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadIssues($manager, $this->testIssueData);
    }
}
