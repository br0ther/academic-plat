<?php
namespace ORO\Bundle\IssueBundle\Tests\Unit\Form\Type;

use Andy\Bundle\BugTrackBundle\Form\Type\IssueFormType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Form\Test\FormInterface;

class IssueFormTypeTest extends FormIntegrationTestCase
{
    /**
     * @var IssueFormType
     */
    protected $type;

    /**
     * @var string
     */
    protected $expectedName;

    /**
     * Setup test env
     */
    protected function setUp()
    {
        parent::setUp();
        $this->type = new IssueFormType(['Bug', 'Story', 'Subtask']);
        $this->expectedName = 'tracker_issue';
    }
    
    public function testGetName()
    {
        $this->assertEquals($this->expectedName, $this->type->getName());
    }

    /**
     * @dataProvider issueDataProvider
     * @param $formData
     */
    public function testSubmitValidData($formData)
    {
        $form = $this->factory->create('form', $this->type);

        $this->assertFormOptions($form);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertTrue($form->isSynchronized());
    }

    /**
     * @return array
     */
    public function issueDataProvider()
    {
        return [
            [
                'formData' => [
                    'code'          => 'CODE-01',
                    'summary'       => 'SUMMARY',
                    'type'          => 'Story',
                    'description'   => 'DESCRIPTION',
                    'priority'      => 'Critical',
                    'assignee'      => 1,
                    'reporter'      => 1,
                    'relatedIssues' => [1, 2],
                    'resolution'    => 'Fixed',
                ]
            ]
        ];
    }

    public function testGetParent()
    {
        $this->assertEquals('form', $this->type->getParent());
    }

    /**
     * @param $form FormInterface
     */
    protected function assertFormOptions($form)
    {
        $formConfig = $form->getConfig();
        $this->assertEquals('Andy\Bundle\BugTrackBundle\Form\Type\IssueFormType', $formConfig->getOption('data_class'));
    }

    protected function getExtensions()
    {
        return array(
            new PreloadedExtension(
                [
                    'translatable_entity' => $this->getMock('Symfony\Component\Translation\TranslatorInterface')
                ],
                []
            ),
        );
    }
}
