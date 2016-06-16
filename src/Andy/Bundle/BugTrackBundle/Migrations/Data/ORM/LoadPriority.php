<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Andy\Bundle\BugTrackBundle\Entity\Priority;

class LoadPriority extends AbstractFixture
{
    protected $priorities = [
        [
            'name'  => 'Trivial',
            'priority' => 10
        ],
        [
            'name'  => 'Major',
            'priority' => 20
        ],
        [
            'name'  => 'Critical',
            'priority' => 30
        ],
        [
            'name'  => 'Blocker',
            'priority' => 40
        ]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->priorities as $priorityData) {
            $priorities = $manager->getRepository('AndyBugTrackBundle:Priority')
                ->findBy([
                    'name' => $priorityData['name']
                ]);
            if (!count($priorities)) {
                $priority = new Priority();
                $priority->setName($priorityData['name']);
                $priority->setPriority($priorityData['priority']);
                $manager->persist($priority);
            }
        }
        $manager->flush();
    }
}
