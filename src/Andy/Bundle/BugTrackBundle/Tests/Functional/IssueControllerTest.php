<?php


namespace Andy\Bundle\BugTrackBundle\Tests\Functional;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

/**
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader('test_admin', '123'));
        
        $this->loadFixtures([
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestUserData',
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestIssueData',
        ]);
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->getUrl('issue_index')
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains('Issues', $content);
        $this->assertContains('TEST-0001', $content);

    }

    public function testView()
    {
        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0001');

        $this->client->request(
            'GET',
            $this->getUrl('issue_view', array('id' => $issue->getId()))
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains('Code', $content);
        $this->assertContains('Assigned To', $content);
        $this->assertContains('Related Issues', $content);
    }

    public function testCreate()
    {
        $this->client->request(
            'GET',
            $this->getUrl('issue_create')
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains(Issue::TYPE_BUG, $content);

    }

    public function testUpdate()
    {
        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0001');

        $this->client->request(
            'GET',
            $this->getUrl('issue_update', array('id' => $issue->getId()))
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains("TEST-0001", $content);
    }

    public function testCreateSubtaskFromStory()
    {
        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0001');
        
        $this->client->request(
            'GET',
            sprintf('%s?parent=%d', $this->getUrl('issue_create'), $issue->getId())
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains(Issue::TYPE_SUBTASK, $content);

    }

    public function testCreateSubtaskFromBug()
    {
        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0002');

        $this->client->request(
            'GET',
            sprintf('%s?parent=%d', $this->getUrl('issue_create'), $issue->getId())
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains(Issue::TYPE_BUG, $content);
        $this->assertContains('regular task created!', $content);

    }

    public function testDelete()
    {
        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0002');

        $this->client->request(
            'GET',
            $this->getUrl('issue_delete', array('id' => $issue->getId()))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 302);
    }

    public function testDeleteFromGrid()
    {
        $issueId = $this->getReference('TEST-0001')->getId();

        $this->client->request(
            'DELETE',
            $this->getUrl('issue_grid_delete', array('id' => $issueId))
        );

        $jsonResponce = $this->getJsonResponseContent($this->client->getResponse(), 200);
        $this->assertEquals($issueId, $jsonResponce['id']);
    }
}
