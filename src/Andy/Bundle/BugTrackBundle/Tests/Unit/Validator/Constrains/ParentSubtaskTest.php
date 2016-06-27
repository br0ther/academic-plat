<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Unit\Validator\Constrains;

use Andy\Bundle\BugTrackBundle\Validator\Constraints\ParentSubtask;

class ParentSubtaskTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTargets()
    {
        $constraint = new ParentSubtask();
        $this->assertEquals(ParentSubtask::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
