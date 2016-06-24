<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Functional;

use Oro\Bundle\ApiBundle\Request\DataType;
use Oro\Bundle\ApiBundle\Request\RequestType;
use Oro\Bundle\ApiBundle\Tests\Functional\ApiTestCase;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

/**
 * @dbIsolation
 */
class IssueApiTest extends ApiTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());

        $this->loadFixtures([
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestUserData',
            '\Andy\Bundle\BugTrackBundle\Tests\Functional\DataFixtures\TestIssueData',
        ]);
        
        parent::setUp();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getRequestType()
    {
        return new RequestType([RequestType::REST]);
    }

    public function testIssueApiGetList()
    {
        $entityAlias = $this->valueNormalizer->normalizeValue(
            Issue::class,
            DataType::ENTITY_TYPE,
            $this->getRequestType()
        );
        
        $this->client->request(
            'GET',
            $this->getUrl('oro_rest_api_cget', ['entity' => $entityAlias, 'limit' => 1])
        );
        $response = $this->client->getResponse();
        $this->assertApiResponseStatusCodeEquals($response, 200, $entityAlias, 'get list');
    }

    public function testIssueApiGetSingle()
    {
        $entityAlias = $this->valueNormalizer->normalizeValue(
            Issue::class,
            DataType::ENTITY_TYPE,
            $this->getRequestType()
        );

        /** @var Issue $issue */
        $issue = $this->getReference('TEST-0001');

        $this->client->request(
            'GET',
            $this->getUrl('oro_rest_api_cget', ['entity' => $entityAlias, 'id' => $issue->getId()])
        );
        $response = $this->client->getResponse();
        $this->assertApiResponseStatusCodeEquals($response, 200, $entityAlias, 'get list');
    }
}
