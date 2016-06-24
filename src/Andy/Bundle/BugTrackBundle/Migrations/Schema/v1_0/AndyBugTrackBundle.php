<?php

namespace Andy\Bundle\BugTrackBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class AndyBugTrackBundle implements Migration, ExtendExtensionAwareInterface,
 NoteExtensionAwareInterface, ActivityExtensionAwareInterface
{
    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    /**
     * @var NoteExtension
     */
    protected $noteExtension;

    /** @var ActivityExtension */
    protected $activityExtension;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createBugTrackIssueTable($schema);
        $this->createBugTrackIssueCollaboratorsTable($schema);
        $this->createBugTrackIssueRelatedTable($schema);
        $this->createBugTrackPriorityTable($schema);
        $this->createBugTrackResolutionTable($schema);

        /** Foreign keys generation **/
        $this->addBugTrackIssueForeignKeys($schema);
        $this->addBugTrackIssueCollaboratorsForeignKeys($schema);
        $this->addBugTrackIssueRelatedForeignKeys($schema);
    }

    /**
     * Create bug_track_issue table
     *
     * @param Schema $schema
     */
    protected function createBugTrackIssueTable(Schema $schema)
    {
        $table = $schema->createTable('bug_track_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 25]);
        $table->addColumn('description', 'text', ['notnull' => false, 'length' => 255]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);

        $this->extendExtension->addEnumField(
            $schema,
            $table,
            'type',
            'issue_type',
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_CUSTOM],
                'datagrid' => [
                    'is_visible' => false,
                ],
                'form' => [
                    'is_enabled' => false,
                ],
                'view' => [
                    'is_displayable' => false,
                ],
            ]
        );

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code'], 'UNIQ_12AD233E77153098');
        $table->addUniqueIndex(['workflow_item_id'], 'UNIQ_671503B31023C4EE');
        $table->addIndex(['priority_id'], 'IDX_12AD233E497B19F9', []);
        $table->addIndex(['resolution_id'], 'IDX_12AD233E12A1C43A', []);
        $table->addIndex(['reporter_id'], 'IDX_12AD233EE1CFE6F5', []);
        $table->addIndex(['assignee_id'], 'IDX_12AD233E59EC7D60', []);
        $table->addIndex(['parent_id'], 'IDX_12AD233E727ACA70', []);
        $table->addIndex(['organization_id'], 'IDX_671503B332C8A3DE', []);
        $table->addIndex(['workflow_step_id'], 'IDX_671503B371FE882C', []);

        //Enables Notes activity for Issue entity
        $this->noteExtension->addNoteAssociation($schema, $table->getName());

        //Enables Email activity for Issue entity
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', $table->getName(), true);
    }

    /**
     * Create bug_track_issue_collaborators table
     *
     * @param Schema $schema
     */
    protected function createBugTrackIssueCollaboratorsTable(Schema $schema)
    {
        $table = $schema->createTable('bug_track_issue_collaborators');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'user_id']);
        $table->addIndex(['issue_id'], 'IDX_93B721895E7AA58C', []);
        $table->addIndex(['user_id'], 'IDX_93B72189A76ED395', []);
    }

    /**
     * Create bug_track_issue_related table
     *
     * @param Schema $schema
     */
    protected function createBugTrackIssueRelatedTable(Schema $schema)
    {
        $table = $schema->createTable('bug_track_issue_related');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('related_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'related_id']);
        $table->addIndex(['issue_id'], 'IDX_C5AF35715E7AA58C', []);
        $table->addIndex(['related_id'], 'IDX_C5AF35714162C001', []);
    }

    /**
     * Create bug_track_priority table
     *
     * @param Schema $schema
     */
    protected function createBugTrackPriorityTable(Schema $schema)
    {
        $table = $schema->createTable('bug_track_priority');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('priority', 'integer', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'UNIQ_62A6DC275E237E06');
    }

    /**
     * Create bug_track_resolution table
     *
     * @param Schema $schema
     */
    protected function createBugTrackResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('bug_track_resolution');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'UNIQ_FDD30F8A5E237E06');
    }

    /**
     * Add bug_track_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBugTrackIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bug_track_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_item'),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_resolution'),
            ['resolution_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_step'),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['assignee_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_issue'),
            ['parent_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_priority'),
            ['priority_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_enum_issue_type'),
            ['type_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add bug_track_issue_collaborators foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBugTrackIssueCollaboratorsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bug_track_issue_collaborators');
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add bug_track_issue_related foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBugTrackIssueRelatedForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bug_track_issue_related');
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_issue'),
            ['related_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bug_track_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
