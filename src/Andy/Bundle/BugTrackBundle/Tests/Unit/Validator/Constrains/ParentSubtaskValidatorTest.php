<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Unit\Validator\Constrains;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Andy\Bundle\BugTrackBundle\Validator\Constraints\ParentSubtask;
use Andy\Bundle\BugTrackBundle\Validator\Constraints\ParentSubtaskValidator;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ParentSubtaskValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Issue
     */
    protected $entity;

    /**
     * @var ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var ParentSubtaskValidator
     */
    protected $validator;

    /**
     * @var ParentSubtask
     */
    protected $constraint;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entity = new Issue();
        $this->contextMock = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->constraint = new ParentSubtask();
        $this->validator = new ParentSubtaskValidator();
        $this->validator->initialize($this->contextMock);
    }

    /**
     * Case when trying issue have a parent but havent type subtask
     */
    public function testValidateStoryFail()
    {
        $issue =$this->getMockBuilder(Issue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $issueType = $this->getMockBuilder(ExtendHelper::buildEnumValueClassName('issue_type'))
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();
        
        $issueType->method('getName')
            ->willReturn(Issue::TYPE_BUG);

        $issue->method('getType')->willReturn($issueType);

        $parentIssue = $this->getMockBuilder(Issue::class)
            ->disableOriginalConstructor()
            ->getMock();

        $issue->setParentIssue($parentIssue);

        $violationMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $violationMock->expects($this->once())
            ->method('addViolation');
        
        $this->contextMock->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationMock);

        $this->validator->validate($issue, $this->constraint);
    }

    /**
     * Case when trying issue have a parent but havent type subtask
     */
    public function testValidateSuccess()
    {
        $issue =$this->getMockBuilder(Issue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType'])
            ->getMock();

        $issueType = $this->getMockBuilder(ExtendHelper::buildEnumValueClassName('issue_type'))
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $issueType->method('getName')
            ->willReturn(Issue::TYPE_BUG);

        $issue->method('getType')->willReturn($issueType);
        
        $violationMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $violationMock->expects($this->never())
            ->method('addViolation');

        $this->contextMock->expects($this->never())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationMock);

        $this->validator->validate($issue, $this->constraint);
    }
}
