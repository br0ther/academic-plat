<?php

namespace Andy\Bundle\BugTrackBundle\Controller;

use Andy\Bundle\BugTrackBundle\Entity\Issue;
use Andy\Bundle\BugTrackBundle\Form\Type\IssueFormType;
use Doctrine\Common\Persistence\ObjectRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\NavigationBundle\Annotation\TitleTemplate;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/issue")
 */
class IssueController extends Controller
{
    /**
     * @Route("/", name="issue_index")
     * @Template
     * @Acl(
     *     id="issue_view",
     *     type="entity",
     *     class="AndyBugTrackBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction()
    {

        return [
            'gridName'     => 'issues-grid',
            'entity_class' => $this->container->getParameter('issue.entity.class'),
        ];
    }

    /**
     * @Route("/create", name="issue_create")
     * @Template("AndyBugTrackBundle:Issue:update.html.twig")
     * @Acl(
     *     id="issue_create",
     *     type="entity",
     *     class="AndyBugTrackBundle:Issue",
     *     permission="CREATE"
     * )
     * 
     * @param Request $request
     * 
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        /** @var Issue $issue */
        $issue = new Issue();
        $issue->setReporter($this->getUser());
        $isSubtask = false;

        if ($parentId = $request->query->getInt('parent')) {
            
            /** @var ObjectRepository $issueTypesRepository */
            $issueTypesRepository = $this->getDoctrine()->getManager()
                ->getRepository(ExtendHelper::buildEnumValueClassName('issue_type'));

            $parent = $this->getDoctrine()->getRepository('AndyBugTrackBundle:Issue')
                ->findOneBy([
                    'id'   => $parentId,
                    'type' => $issueTypesRepository->findOneBy(['name' => Issue::TYPE_STORY])
                ]);

            if ($parent) {
                $issue->setParentIssue($parent)->setType(
                    $issueTypesRepository->findOneBy(['name' => Issue::TYPE_SUBTASK])
                );
                $isSubtask = true;
            }
        }

        return $this->update($issue, $isSubtask);
    }

    /**
     * @Route("/update/{id}", name="issue_update", requirements={"id":"\d+"}, defaults={"id":0})
     * @Template()
     * @Acl(
     *     id="issue_update",
     *     type="entity",
     *     class="AndyBugTrackBundle:Issue",
     *     permission="EDIT"
     * )
     *
     * @param Issue $issue
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Issue $issue)
    {
        return $this->update($issue);
    }

    /**
     * @param Issue $issue
     * @param bool  $isSubtask
     * 
     * @return array|RedirectResponse
     */
    public function update(Issue $issue, $isSubtask = false)
    {
        $issueTypes = $this->get('andy_bug_track.handler.issue_handler')->getIssueTypes($isSubtask);

//        return $this->get('oro_form.model.update_handler')->update(
//            $issue,
//            $this->createForm(new IssueFormType($issueTypes), $issue),
//            $this->get('translator')->trans('oro.tracking.trackingwebsite.saved_message')
//        );

        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $issue,
            $this->createForm(new IssueFormType($issueTypes), $issue),
            function (Issue $entity) {
                return [
                    'route' => 'issue_update',
                    'parameters' => ['id' => $entity->getId()]
                ];
            },
            function (Issue $entity) {
                return [
                    'route' => 'issue_view',
                    'parameters' => ['id' => $entity->getId()]
                ];
            },
            $this->get('translator')->trans('andy.bugtrack.issue.saved_message')
        );
    }

    /**
     * @Route("/{id}", name="issue_view", requirements={"id"="\d+"})
     * @Template
     * @TitleTemplate("oro.issue.menu.issue_view")
     * @AclAncestor("issue_view")
     * 
     * @param Issue $issue
     *
     * @return array
     */
    public function viewAction(Issue $issue)
    {
        $issueTypes = $this->get('andy_bug_track.handler.issue_handler')->getIssueTypes();

        return [
            'entity' => $issue,
            'user' => $this->getUser(),
            'issue_types' => $issueTypes
        ];
    }

    /**
     * @Route("/delete/{id}", name="issue_delete", requirements={"id":"\d+"})
     * @Acl(
     *     id="issue_delete",
     *     type="entity",
     *     class="AndyBugTrackBundle:Issue",
     *     permission="DELETE"
     * )
     * 
     * @param Issue $issue
     * 
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Issue $issue)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($issue);
        $entityManager->flush();
        
        return $this->redirectToRoute('issue_index');
    }

    /**
     * @Route("/deletegrid/{id}", name="issue_grid_delete", requirements={"id":"\d+"})
     * @Method({"DELETE"})
     * @Acl(
     *     id="issue_delete",
     *     type="entity",
     *     class="AndyBugTrackBundle:Issue",
     *     permission="DELETE"
     * )
     *
     * @param Issue $issue
     *
     * @return JsonResponse
     */
    public function deleteFromGridAction(Issue $issue)
    {
        $id = $issue->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($issue);
        $entityManager->flush();

        return new JsonResponse(
            [
                'id' => $id,
                'success' => true
            ]
        );
    }

    /**
     * @Route("/widget/updated_at/{id}", name="issue_widget_updated_at", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("issue_view")
     * 
     * @param Issue $issue
     * 
     * @return array
     */
    public function updatedAtAction(Issue $issue)
    {
        return [
            'updatedAt' => $issue->getUpdatedAt(),
        ];
    }

}
