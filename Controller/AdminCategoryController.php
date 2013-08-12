<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller managing the categories`
 *
 * @Route("/admin/category")
 */
class AdminCategoryController extends Controller
{
    /**
     * Show lists of Persons
     *
     * @Method("GET")
     * @Route("/index.html", name="admin_category_index")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     * 
     * @return array
     */
    public function indexAction()
    {
        $csrf               = $this->container->get('form.csrf_provider');

        $keys = array(
            'id',
            'category.admin.category.name.text',
        );

        return array(
            'keys'      => $keys,
            'csrf'      => $csrf
        );
    }

    /**
     * Show lists of Events
     *
     * @Method("GET")
     * @Route("/list.json", name="admin_categorys_json")
     * @Secure(roles="ROLE_ADMIN")
     * 
     * @return Response
     */
    public function ajaxListAction()
    {
        $manager       = $this->getManager();
        $repository    = $manager->getRepository();
        $rawDocuments  = $repository->findAll();

        $documents = array('aaData' => array());

        foreach ($rawDocuments as $document) {
            $documents['aaData'][] = array(
                $document->getId(),
                $document->getName(),
                null
            );
        }

        return new Response(json_encode($documents));
    }

    /**
     * Displays a form to create a new Person document.
     *
     * @Method({"GET", "POST"})
     * @Route("/new", name="admin_category_new")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()\
     * 
     * @return array
     */
    public function newAction()
    {
        $documentManager    = $this->getManager();
        $document           = $documentManager->createInstance();

        $formHandler    = $this->get('black_category.category.form.handler');
        $process        = $formHandler->process($document);

        if ($process) {
            $documentManager->persist($document);
            $documentManager->flush();

            return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $document->getId())));
        }

        return array(
            'document'  => $document,
            'form'      => $formHandler->getForm()->createView()
        );
    }

    /**
     * Displays a form to edit an existing Category document.
     *
     * @param string $id The document ID
     *
     * @Method({"GET", "POST"})
     * @Route("/{id}/edit", name="admin_category_edit")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction($id)
    {
        $documentManager    = $this->getManager();
        $repository         = $documentManager->getRepository();

        $document = $repository->findOneById($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Category document.');
        }

        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if (true === $document->isPrivate() && $document->getAuthor() != $this->getUser()) {
                throw new AccessDeniedException();
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        $formHandler    = $this->get('black_category.category.form.handler');
        $process        = $formHandler->process($document);

        if ($process) {
            $documentManager->flush();

            return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $id)));
        }

        return array(
            'document'      => $document,
            'form'          => $formHandler->getForm()->createView(),
            'delete_form'   => $deleteForm->createView()
        );
    }

    /**
     * Deletes a Category document.
     *
     * @param string    $id
     * @param null      $token
     *
     * @Method({"POST", "GET"})
     * @Route("/{id}/delete/{token}", name="admin_category_delete")
     * @Secure(roles="ROLE_ADMIN")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id, $token = null)
    {
        $form       = $this->createDeleteForm($id);
        $request    = $this->getRequest();

        $form->bind($request);

        if (null !== $token) {
            $token = $this->get('form.csrf_provider')->isCsrfTokenValid('delete', $token);
        }

        if ($form->isValid() || true === $token) {

            $dm         = $this->getManager();
            $repository = $dm->getRepository();
            $document   = $repository->findOneById($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Person document.');
            }

            $dm->remove($document);
            $dm->flush();

            $this->get('session')->getFlashBag()->add('success', 'success.category.admin.category.delete');

        } else {
            $this->getFlashBag->add('error', 'error.category.admin.category.not.valid');
        }

        return $this->redirect($this->generateUrl('admin_category_index'));
    }

    /**
     * Batch action for 1/n document.
     *
     * @Method({"POST"})
     * @Route("/batch", name="admin_category_batch")
     *
     * @return array
     *
     * @throws \Symfony\Component\Serializer\Exception\InvalidArgumentException If method does not exist
     */
    public function batchAction()
    {
        $request    = $this->getRequest();
        $token      = $this->get('form.csrf_provider')->isCsrfTokenValid('batch', $request->get('token'));

        if (!$ids = $request->get('ids')) {
            $this->get('session')->getFlashBag()->add('error', 'error.category.admin.category.no.item');

            return $this->redirect($this->generateUrl('admin_category_index'));
        }

        if (!$action = $request->get('batchAction')) {
            $this->get('session')->getFlashBag()->add('error', 'error.category.admin.category.no.action');

            return $this->redirect($this->generateUrl('admin_category_index'));
        }

        if (!method_exists($this, $method = $action . 'Action')) {
            throw new Exception\InvalidArgumentException(
                sprintf('You must create a "%s" method for action "%s"', $method, $action)
            );
        }

        if (false === $token) {
            $this->get('session')->getFlashBag()->add('error', 'error.category.admin.category.csrf');

            return $this->redirect($this->generateUrl('admin_category_index'));
        }

        foreach ($ids as $id) {
            $this->$method($id, $this->get('form.csrf_provider')->generateCsrfToken('delete'));
        }

        return $this->redirect($this->generateUrl('admin_category_index'));

    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();

        return $form;
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    protected function getManager()
    {
        return $this->get('black_category.manager.category');
    }
}
