<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Data\ORM;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadIssueType extends AbstractFixture
{
    /** @var $data array */
    protected $data = [
        Issue::TYPE_BUG => false,
        Issue::TYPE_SUBTASK => false,
        Issue::TYPE_TASK => true,
        Issue::TYPE_STORY => false,
    ];
    
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $className = ExtendHelper::buildEnumValueClassName('issue_type');

        /** @var EnumValueRepository $enumRepo */
        $enumRepo = $manager->getRepository($className);
        $priority = 1;
        foreach ($this->data as $name => $isDefault) {
            $enumOption = $enumRepo->createEnumValue($name, $priority++, $isDefault);
            $manager->persist($enumOption);
        }
        $manager->flush();
    }
}
