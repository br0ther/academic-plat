<?php
namespace Andy\Bundle\BugTrackBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class IssueFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    const SAMPLE_ISSUE_CODE = 'BT-0000';

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Andy\Bundle\BugTrackBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData(self::SAMPLE_ISSUE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * @param string $key
     * @param Issue $entity
     */
    public function fillEntityData($key, $entity)
    {
        if ($key == self::SAMPLE_ISSUE_CODE) {
            $userRepository = $this->templateManager->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
            $user = $userRepository->getEntity('John Doo');
            $organizationRepository = $this->templateManager
                ->getEntityRepository('Oro\Bundle\OrganizationBundle\Entity\Organization');
            $priorityRepository     = $this->templateManager
                ->getEntityRepository('\Andy\Bundle\BugTrackBundle\Entity\Priority');
            $issueTypeRepo = $this->templateManager->getEntityRepository(ExtendHelper::buildEnumValueClassName('issue_type'));
            $entity->setCode(self::SAMPLE_ISSUE_CODE);
            $entity->setSummary('Sample task');
            $entity->setDescription('Sample description');
            $entity->setType($issueTypeRepo->getEntity('Story'));
            $entity->setPriority($priorityRepository->getEntity('Critical'));
            $entity->setReporter($user);
            $entity->setAssignee($user);
            $entity->setOrganization($organizationRepository->getEntity('default'));
            $entity->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
            $entity->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

            return;
        }
        parent::fillEntityData($key, $entity);
    }
}
