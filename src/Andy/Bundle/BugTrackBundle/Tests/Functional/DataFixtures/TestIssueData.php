<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures;

use Andy\Bundle\BugTrackBundle\DataFixtures\ORM\IssuesDataLoader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TestIssueData extends IssuesDataLoader implements OrderedFixtureInterface
{
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
