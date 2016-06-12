<?php
namespace Andy\Bundle\BugTrackBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Andy\Bundle\BugTrackBundle\Entity\Resolution;

class LoadResolution extends AbstractFixture
{
    /**
     * @var array
     */
    protected $resolutions = ['Cannot Reproduce', 'Duplicate', 'Obsolete', 'Fixed'];


    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->resolutions as $resolutionName) {

            $resolutions = $manager->getRepository('AndyBugTrackBundle:Resolution')
                ->findBy([
                    'name' => $resolutionName
                ]);

            if (!count($resolutions)) {
                $entity = new Resolution();
                $entity->setName($resolutionName);
                $manager->persist($entity);
            }
        }
        $manager->flush();
    }
}
