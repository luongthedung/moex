<?php

namespace Moex\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Moex\CoreBundle\Entity\MeDrivers;
use Moex\CoreBundle\Form\MeDriversType;
use Moex\CoreBundle\Form\DriverFilterType;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * MeDrivers controller.
 *
 * @Route("/driver")
 */
class MeDriversController extends Controller
{
    /**
     * Lists all MeDrivers entities.
     *
     * @Route("/", name="driver")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $filter = $this->getRequest()->getSession()->remove('driver.filter');
        $filter = $this->getRequest()->getSession()->get('driver.filter', new \Moex\CoreBundle\Entity\DriverFilter());
		
		$translator = $this->get('translator');
        $filterForm = $this->createForm(new DriverFilterType($translator), $filter);
        $filterForm->bindRequest($this->getRequest());
        $this->getRequest()->getSession()->set('driver.filter', $filter);
        
        $query = $em->getRepository('MoexCoreBundle:MeDrivers')->findByFilterQuery($filter);
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($this->container->getParameter('moex.pagesize.default'));
        $paginator->setCurrentPage($this->get('request')->query->get('page', 1), false, true);

        return array('filterForm' => $filterForm->createView(), 'paginator' => $paginator);
    }

    /**
     * Filter Form.
     *
     * @Route("/filter", name="driver_filter")
     * @Template()
     */
    public function filterAction()
    {
        $filter = $this->getRequest()->getSession()->get('driver.filter', new MeDrivers());

		$translator = $this->get('translator');
        $filterForm = $this->createForm(new DriverFilterType($translator), $filter);
        $filterForm->bindRequest($this->getRequest());
        $this->getRequest()->getSession()->set('driver.filter', $filter);

        return array('filterForm' => $filterForm->createView());
    }

    /**
     * Finds and displays a MeDrivers entity.
     *
     * @Route("/{id}/show", name="driver_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MoexCoreBundle:MeDrivers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeDrivers entity.');
        }
        return array(
            'entity'      => $entity,
            );
    }

    /**
     * Displays a form to create a new MeDrivers entity.
     *
     * @Route("/new", name="driver_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MeDrivers();
		$translator = $this->get('translator');
        $form   = $this->createForm(new MeDriversType($translator), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new MeDrivers entity.
     *
     * @Route("/create", name="driver_create")
     * @Method("post")
     * @Template("MoexCoreBundle:MeDrivers:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new MeDrivers();
        $request = $this->getRequest();
		$translator = $this->get('translator');
        $form    = $this->createForm(new MeDriversType($translator), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
        	$created_at = new \DateTime();
        	$updated_at = new \DateTime();
			$entity->setCreatedAt($created_at);
			$entity->setUpdatedAt($updated_at);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('driver_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing MeDrivers entity.
     *
     * @Route("/{id}/edit", name="driver_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MoexCoreBundle:MeDrivers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeDrivers entity.');
        }
		$translator = $this->get('translator');
        $editForm = $this->createForm(new MeDriversType($translator), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing MeDrivers entity.
     *
     * @Route("/{id}/update", name="driver_update")
     * @Method("post")
     * @Template("MoexCoreBundle:MeDrivers:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MoexCoreBundle:MeDrivers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeDrivers entity.');
        }
	
		$translator = $this->get('translator');
        $editForm   = $this->createForm(new MeDriversType($translator), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
        	$updated_at = new \DateTime();
			$entity->setUpdatedAt($updated_at);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('driver_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a MeDrivers entity.
     *
     * @Route("/{id}/delete", name="driver_delete")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('MoexCoreBundle:MeDrivers')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find MeDrivers entity.');
		}

		$em->remove($entity);
		$em->flush();

        return $this->redirect($this->generateUrl('driver'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
