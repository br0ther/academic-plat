<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\Demo\ORM;

use Andy\Bundle\BugTrackBundle\DataFixtures\ORM\IssuesDataLoader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueData extends IssuesDataLoader implements OrderedFixtureInterface
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
        $this->loadIssues($manager, $this->demoIssueData);
    }
}
