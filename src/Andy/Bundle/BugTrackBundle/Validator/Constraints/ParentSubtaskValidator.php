<?php

namespace Andy\Bundle\BugTrackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

class ParentSubtaskValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if (!empty($issue->getParentIssue()) && $issue->getType()->getName() !== Issue::TYPE_SUBTASK) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
