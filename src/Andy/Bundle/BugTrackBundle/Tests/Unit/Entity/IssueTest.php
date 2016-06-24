<?php
namespace Andy\Bundle\BugTrackBundle\Tests\Unit\Entity;

use Andy\Bundle\BugTrackBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testIdGetter()
    {
        $obj = new Issue();

        $this->setId($obj, 1);
        $this->assertEquals(1, $obj->getId());
    }

    public function testCreatedAtGetter()
    {
        $date = new \DateTime('now');

        $obj = new Issue();

        $this->setCreatedAt($obj, $date);
        $this->assertEquals($date, $obj->getCreatedAt());
    }

    /**
     * @dataProvider provider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Issue();
        call_user_func_array([$obj, 'set' . ucfirst($property)], [$value]);
        $this->assertEquals($value, call_user_func_array([$obj, 'get' . ucfirst($property)], []));
    }

    public function testToString()
    {
        $obj = new Issue();
        $this->assertEmpty((string)$obj);
        $obj->setCode('BT-1234');
        $this->assertEquals($obj->__toString(), 'BT-1234');
    }

    public function provider()
    {
        return [
            ['code', 'BT-00001'],
            ['summary', 'Test task'],
            ['description', 'Test Description'],
            ['priority', $this->getMock('Andy\Bundle\BugTrackBundle\Entity\Priority')],
            ['resolution', $this->getMock('Andy\Bundle\BugTrackBundle\Entity\Resolution')],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
            ['organization', $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization')],
            ['reporter', $this->getMock('Oro\Bundle\UserBundle\Entity\User')],
            ['assignee', $this->getMock('Oro\Bundle\UserBundle\Entity\User')]
        ];
    }

    /**
     * @param mixed $obj
     * @param mixed $val
     */
    protected function setId($obj, $val)
    {
        $class = new \ReflectionClass($obj);
        $prop = $class->getProperty('id');
        $prop->setAccessible(true);

        $prop->setValue($obj, $val);
    }

    /**
     * @param mixed $obj
     * @param mixed $val
     */
    protected function setCreatedAt($obj, $val)
    {
        $class = new \ReflectionClass($obj);
        $prop = $class->getProperty('createdAt');
        $prop->setAccessible(true);

        $prop->setValue($obj, $val);
    }
}
