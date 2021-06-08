<?php

namespace App\Controller\Core;

use App\Entity\Adress;
use App\Form\AdressType;
use App\Repository\Core\AdressRepository ;
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
 * entity_twig_var_plural :  adresses   *
 * entity_class_name :  Adress   * 
 */

/**
 * @Route("/adress")
 */
class AdressController extends AbstractController
{
// InBORe Controller generation

    /**
     * @Route("/", name="adress_index", methods={"GET"})
     */
     
public function index(AdressRepository $adressRepository): Response
{
    return $this->render('Core/adress/index.html.twig', [
        'list_SQL_fetchedVariables' => $adressRepository->findSearch("id ASC", "", null, null, 1),
    ]);
}

 
  /**
   * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType) 
   *
   * @Route("/search/{q}", name="adress_search", requirements={"q"=".+"} )
   */
  public function searchAction($q, AdressRepository $adressRepository) {
    
    $results = $adressRepository->findSearchAction($q);

    return $this->json($results);
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="adress_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, AdressRepository $adressRepository) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "adress.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;    
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
    $searchPhrase = $request->get('searchPattern');
    }   
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $entities_toshow =  $adressRepository->findSearch($orderBy, $searchPhrase, $request->get('idFk'), $request->get('nameFk') );
    } else {
      $entities_toshow =  $adressRepository->findSearch($orderBy, $searchPhrase);
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
     * @Route("/new", name="adress_new", methods={"GET","POST"})
     */
    public function new(Request $request, AdressRepository $adressRepository): Response
    {
        $adress = new Adress();
        $em = $this->getDoctrine()->getManager();
        
        // check if the relational Entity  is given
        if ($relationalEntity_id = $request->get('idFk') && $request->get('nameFk')) { 
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = ( substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository('App:'.$nameRelEntity)->find($relationalEntity_id);
            $method =  'set'.$nameRelEntity.'Fk';
            $adress->$method($relationalEntity);
        }
        
        $form = $this->createForm(AdressType::class, $adress, ['action_type' => Action::create(),]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->persist($adress);
          try {
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/adress/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $adressRepository->findSearch("id ASC", "", null, null, 1),
            ]);
            // return $this->render('Core/adress/index.html.twig', ['exception_message' => explode("\n", $exception_message)[0],]);
          }
          return $this->redirectToRoute('adress_edit', [
            'id'    => $adress->getId(),
            'valid' => 1,
            'idFk'  => $request->get('idFk'),
          ]);
        }
        // Initial form render or form invalid
        return $this->render('Core/adress/edit.html.twig', [
          'adress'  => $adress,
          'edit_form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="adress_show", methods={"GET"})
     */
    public function show(Adress $adress): Response
    {
    
        $deleteForm = $this->createDeleteForm($adress);
        $showForm   = $this->createForm('App\Form\AdressType', $adress, [
          'action_type' => Action::show(),
        ]);

        return $this->render('Core/adress/edit.html.twig', [
          'adress'    => $adress,
          'edit_form'   => $showForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    
    }

    /**
     * @Route("/{id}/edit", name="adress_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Adress $adress, AdressRepository $adressRepository): Response
    {
    
        /** //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $adress->getUserCre() != $user->getId()) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        */

        // set Array Collection for N-N relationship and the link-embeded form if needed
        $edit_form_parameters = $adressRepository->editActionBeforeFormIsSubmitted($request, $adress);

        // editAction
        $deleteForm = $this->createDeleteForm($adress);
        $editForm   = $this->createForm('App\Form\AdressType', $adress, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          // delete ArrayCollection
          $adressRepository->editActionAfterFormIsSubmitted($request, $adress);

          // flush
          $this->getDoctrine()->getManager()->persist($adress);
          try {
            $this->getDoctrine()->getManager()->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/adress/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $adressRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
          return $this->render('Core/adress/edit.html.twig', array(
            'adress'  => $adress,
            'edit_form' => $editForm->createView(),
            'valid'     => 1,
            'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
          ));
        }

        return $this->render('Core/adress/edit.html.twig', array(
          'adress'    => $adress,
          'edit_form'   => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
          'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ));
    
    }

    /**
     * @Route("/{id}", name="adress_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Adress $adress,  AdressRepository $adressRepository): Response
    {
    
        $form = $this->createDeleteForm($adress);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
          $em = $this->getDoctrine()->getManager();
          try {
            $em->remove($adress);
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes( html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8') );
            return $this->render('Core/adress/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $adressRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
        }

    return $this->redirectToRoute('adress_index');
    
    }
    
   /**
   * Creates a form to delete a adress entity.
   *
   * @param Adress $adress The adress entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Adress $adress) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('adress_delete', array('id' => $adress->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
  
}
