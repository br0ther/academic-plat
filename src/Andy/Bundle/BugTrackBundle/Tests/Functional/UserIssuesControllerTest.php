<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * @dbIsolation
 */
class UserIssuesControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader('test_admin', '123'));
        
        $this->loadFixtures([
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestUserData',
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestIssueData',
        ]);
    }

    public function testIssues()
    {
        /** @var User $admin */
        $admin = $this->getReference('test_admin');

        $this->client->request(
            'GET',
            $this->getUrl('user_issues', array('userId' => $admin->getId()))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $content = $result->getContent();
        $this->assertContains('User Issues', $content);
    }
}
