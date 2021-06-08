<?php

namespace App\Controller\Core;

use App\Entity\Voc;
use App\Form\VocType;
use App\Repository\Core\VocRepository ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunction;

/**
 * INFO VAR 
 * repository_full_class_name :  *
 * repository_class_name :  *
 * repository_var :  *
 * entity_twig_var_plural :  vocs   *
 * entity_class_name :  Voc   * 
 */

/**
 * @Route("/voc")
 */
class VocController extends AbstractController
{
// InBORe Controller generation

    /**
     * @Route("/", name="voc_index", methods={"GET"})
     */
     
public function index(VocRepository $vocRepository): Response
{
    return $this->render('Core/voc/index.html.twig', [
        'list_SQL_fetchedVariables' => $vocRepository->findSearch("id ASC", "", null, null, 1),
    ]);
}

 
  /**
   * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType) 
   *
   * @Route("/search/{q}", name="voc_search", requirements={"q"=".+"} )
   */
  public function searchAction($q, VocRepository $vocRepository) {
    
    $results = $vocRepository->findSearchAction($q);

    return $this->json($results);
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="voc_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, VocRepository $vocRepository) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "voc.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;    
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
    $searchPhrase = $request->get('searchPattern');
    }   
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $entities_toshow =  $vocRepository->findSearch($orderBy, $searchPhrase, $request->get('idFk'), $request->get('nameFk') );
    } else {
      $entities_toshow =  $vocRepository->findSearch($orderBy, $searchPhrase);
    }

    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $entities_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $nb, // total data array
    ]);
  }    

    /**
     * @Route("/new", name="voc_new", methods={"GET","POST"})
     */
    public function new(Request $request, VocRepository $vocRepository): Response
    {
        $voc = new Voc();
        $em = $this->getDoctrine()->getManager();
        
        // check if the relational Entity  is given
        if ($relationalEntity_id = $request->get('idFk') && $request->get('nameFk')) { 
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = ( substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository('App:'.$nameRelEntity)->find($relationalEntity_id);
            $method =  'set'.$nameRelEntity.'Fk';
            $voc->$method($relationalEntity);
        }
        
        $form = $this->createForm(VocType::class, $voc, ['action_type' => Action::create(),]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->persist($voc);
          try {
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/voc/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $vocRepository->findSearch("id ASC", "", null, null, 1),
            ]);
            // return $this->render('Core/voc/index.html.twig', ['exception_message' => explode("\n", $exception_message)[0],]);
          }
          return $this->redirectToRoute('voc_edit', [
            'id'    => $voc->getId(),
            'valid' => 1,
            'idFk'  => $request->get('idFk'),
          ]);
        }
        // Initial form render or form invalid
        return $this->render('Core/voc/edit.html.twig', [
          'voc'  => $voc,
          'edit_form' => $form->createView(),
        ]);

    }

    
   /**
   * Creates a new voc entity for modal windows
   *
   * @Route("/newmodal", name="voc_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction($choice_label = null, Request $request) {
    $voc = new Voc();
    $form    = $this->createForm('App\Form\VocType', $voc, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form"  => $this->render('modal-form.html.twig', [
            'choice_label' => $request['choice_label'],
            'entityname' => 'voc',
            'form'       => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $em = $this->getDoctrine()->getManager();
        $em->persist($voc);

        try {
          $flush       = $em->flush();
          $select_id   = $voc->getId();
          $method = 'get'.ucfirst($request->get('choice_label'));
          $select_name = $voc->$method();
          return new JsonResponse([
            'select_id'   => $select_id,
            'select_name' => $select_name,
            'entityname'  => 'voc',
          ]);
        } catch (\Doctrine\DBAL\DBALException $e) {
          return new JsonResponse([
            'exception'         => true,
            'exception_message' => $e->getMessage(),
            'entityname'        => 'voc',
          ]);
        }
      }
    }
    
    return $this->render('modal.html.twig', array(
      'choice_label' => $choice_label,
      'entityname' => 'voc',
      'form'       => $form->createView(),
    ));
  }

    /**
     * @Route("/{id}", name="voc_show", methods={"GET"})
     */
    public function show(Voc $voc): Response
    {
    
        $deleteForm = $this->createDeleteForm($voc);
        $showForm   = $this->createForm('App\Form\VocType', $voc, [
          'action_type' => Action::show(),
        ]);

        return $this->render('Core/voc/edit.html.twig', [
          'voc'    => $voc,
          'edit_form'   => $showForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    
    }

    /**
     * @Route("/{id}/edit", name="voc_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Voc $voc, VocRepository $vocRepository): Response
    {
    
        /** //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $voc->getUserCre() != $user->getId()) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        */

        // set Array Collection for N-N relationship and the link-embeded form if needed
        $edit_form_parameters = $vocRepository->editActionBeforeFormIsSubmitted($request, $voc);

        // editAction
        $deleteForm = $this->createDeleteForm($voc);
        $editForm   = $this->createForm('App\Form\VocType', $voc, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          // delete ArrayCollection
          $vocRepository->editActionAfterFormIsSubmitted($request, $voc);

          // flush
          $this->getDoctrine()->getManager()->persist($voc);
          try {
            $this->getDoctrine()->getManager()->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/voc/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $vocRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
          return $this->render('Core/voc/edit.html.twig', array(
            'voc'  => $voc,
            'edit_form' => $editForm->createView(),
            'valid'     => 1,
            'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
          ));
        }

        return $this->render('Core/voc/edit.html.twig', array(
          'voc'    => $voc,
          'edit_form'   => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
          'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ));
    
    }

    /**
     * @Route("/{id}", name="voc_delete", methods={"POST"})
     */
    public function delete(Request $request, Voc $voc,  VocRepository $vocRepository): Response
    {
    
        $form = $this->createDeleteForm($voc);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
          $em = $this->getDoctrine()->getManager();
          try {
            $em->remove($voc);
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes( html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8') );
            return $this->render('Core/voc/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $vocRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
        }

    return $this->redirectToRoute('voc_index');
    
    }
    
   /**
   * Creates a form to delete a voc entity.
   *
   * @param Voc $voc The voc entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Voc $voc) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('voc_delete', array('id' => $voc->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
  
}
