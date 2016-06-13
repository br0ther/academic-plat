<?php

namespace Andy\Bundle\BugTrackBundle\Form\Type;

use Andy\Bundle\BugTrackBundle\Entity\Priority;
use Andy\Bundle\BugTrackBundle\Entity\Resolution;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Andy\Bundle\BugTrackBundle\Entity\Issue;

class IssueFormType extends AbstractType
{
    private $types;

    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Issue::class,
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tracker_issue';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'code',
                'text',
                [
                    'required' => true,
                    'label' => 'andy.bugtrack.issue.code.label'
                ]
            )
            ->add(
                'summary',
                'text',
                [
                    'required' => true,
                    'label' => 'andy.bugtrack.issue.summary.label'
                ]
            )
            ->add(
                'type',
                'entity',
                [
                    'label' => 'andy.bugtrack.issue.type.label',
                    'class' => ExtendHelper::buildEnumValueClassName('issue_type'),
                    'choices' => $this->types,
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => false,
                    'label' => 'andy.bugtrack.issue.description.label'
                ]
            )
            ->add(
                'priority',
                'translatable_entity',
                [
                    'required' => true,
                    'label' => 'andy.bugtrack.issue.priority.label',
                    'class' => Priority::class
                ]
            )
            ->add(
                'assignee',
                'oro_user_select',
                [
                    'required' => true,
                    'label' => 'andy.bugtrack.issue.assignee.label'
                ]
            )
            ->add(
                'reporter',
                'oro_user_select',
                [
                    'required' => true,
                    'label' => 'andy.bugtrack.issue.reporter.label'
                ]
            )
            ->add(
                'relatedIssues',
                'translatable_entity',
                [
                    'required' => false,
                    'multiple' => true,
                    'label' => 'andy.bugtrack.issue.related.label',
                    'class' => Issue::class
                ]
            )
            ->add(
                'resolution',
                'translatable_entity',
                [
                    'required' => false,
                    'label' => 'andy.bugtrack.issue.resolution.label',
                    'class' => Resolution::class
                ]
            );
    }
}
