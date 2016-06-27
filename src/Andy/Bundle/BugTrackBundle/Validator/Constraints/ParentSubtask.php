<?php

namespace Andy\Bundle\BugTrackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ParentSubtask extends Constraint
{
    public $message = 'Type Subtask should provided with Parent issue';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
