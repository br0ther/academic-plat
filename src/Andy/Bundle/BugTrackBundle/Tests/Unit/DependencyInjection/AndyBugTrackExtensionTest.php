<?php

namespace Andy\Bundle\BugTrackBundle\Tests\Unit\DependencyInjection;

use Andy\Bundle\BugTrackBundle\DependencyInjection\AndyBugTrackExtension;

class OroAddressExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new AndyBugTrackExtension();
        $configs = array();
        $isCalled = false;
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->any())
            ->method('setParameter')
            ->will(
                $this->returnCallback(
                    function ($name, $value) use (&$isCalled) {
                        if ($name == 'issue.entity.class' && $value == 'Andy\Bundle\BugTrackBundle\Entity\Issue') {
                            $isCalled = true;
                        }
                    }
                )
            );

        $extension->load($configs, $container);

        $this->assertTrue($isCalled);
    }
}
