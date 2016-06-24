<?php
namespace Andy\Bundle\BugTrackBundle\Tests\Unit\Entity;

use Andy\Bundle\BugTrackBundle\Entity\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    public function testEntity()
    {
        $obj = new Priority();
        $obj->setName('Critical');
        $obj->setPriority(1);
        $this->assertEquals('Critical', $obj->getName());
        $this->assertEquals(1, $obj->getPriority());
    }
    public function testToString()
    {
        $obj = new Priority();
        $this->assertEmpty((string)$obj);
        $obj->setName('Critical');
        $this->assertEquals($obj->__toString(), 'Critical');
    }
}
