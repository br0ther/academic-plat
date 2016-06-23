<?php
namespace Andy\Bundle\BugTrackBundle\Tests\Unit\Entity;

use Andy\Bundle\BugTrackBundle\Entity\Resolution;

class ResolutionTest extends \PHPUnit_Framework_TestCase
{
    public function testEntity()
    {
        $obj = new Resolution();
        $obj->setName('Duplicate');
        $this->assertEquals('Duplicate', $obj->getName());
    }

    public function testToString()
    {
        $obj = new Resolution();
        $this->assertEmpty((string)$obj);
        $obj->setName('Duplicate');
        $this->assertEquals($obj->__toString(), 'Duplicate');
    }

}
