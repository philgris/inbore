<?php

namespace App\Controller\Core;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\Core\ContactRepository ;
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
 * entity_twig_var_plural :  contacts   *
 * entity_class_name :  Contact   * 
 */

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
// InBORe Controller generation

    /**
     * @Route("/", name="contact_index", methods={"GET"})
     */
     
public function index(ContactRepository $contactRepository): Response
{
    return $this->render('Core/contact/index.html.twig', [
        'list_SQL_fetchedVariables' => $contactRepository->findSearch("id ASC", "", null, null, 1),
    ]);
}

 
  /**
   * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType) 
   *
   * @Route("/search/{q}", name="contact_search", requirements={"q"=".+"} )
   */
  public function searchAction($q, ContactRepository $contactRepository) {
    
    $results = $contactRepository->findSearchAction($q);

    return $this->json($results);
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="contact_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, ContactRepository $contactRepository) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "contact.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;    
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
    $searchPhrase = $request->get('searchPattern');
    }   
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $entities_toshow =  $contactRepository->findSearch($orderBy, $searchPhrase, $request->get('idFk'), $request->get('nameFk') );
    } else {
      $entities_toshow =  $contactRepository->findSearch($orderBy, $searchPhrase);
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
     * @Route("/new", name="contact_new", methods={"GET","POST"})
     */
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $em = $this->getDoctrine()->getManager();
        
        // check if the relational Entity  is given
        if ($relationalEntity_id = $request->get('idFk') && $request->get('nameFk')) { 
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = ( substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository('App:'.$nameRelEntity)->find($relationalEntity_id);
            $method =  'set'.$nameRelEntity.'Fk';
            $contact->$method($relationalEntity);
        }
        
        $form = $this->createForm(ContactType::class, $contact, ['action_type' => Action::create(),]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->persist($contact);
          try {
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/contact/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $contactRepository->findSearch("id ASC", "", null, null, 1),
            ]);
            // return $this->render('Core/contact/index.html.twig', ['exception_message' => explode("\n", $exception_message)[0],]);
          }
          return $this->redirectToRoute('contact_edit', [
            'id'    => $contact->getId(),
            'valid' => 1,
            'idFk'  => $request->get('idFk'),
          ]);
        }
        // Initial form render or form invalid
        return $this->render('Core/contact/edit.html.twig', [
          'contact'  => $contact,
          'edit_form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="contact_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
    
        $deleteForm = $this->createDeleteForm($contact);
        $showForm   = $this->createForm('App\Form\ContactType', $contact, [
          'action_type' => Action::show(),
        ]);

        return $this->render('Core/contact/edit.html.twig', [
          'contact'    => $contact,
          'edit_form'   => $showForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    
    }

    /**
     * @Route("/{id}/edit", name="contact_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
    
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $contact->getUserCre() != $user->getId()) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }


        // set Array Collection for N-N relationship and the link-embeded form if needed
        $edit_form_parameters = $contactRepository->editActionBeforeFormIsSubmitted($request, $contact);

        // editAction
        $deleteForm = $this->createDeleteForm($contact);
        $editForm   = $this->createForm('App\Form\ContactType', $contact, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          // delete ArrayCollection
          $contactRepository->editActionAfterFormIsSubmitted($request, $contact);

          // flush
          $this->getDoctrine()->getManager()->persist($contact);
          try {
            $this->getDoctrine()->getManager()->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/contact/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $contactRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
          return $this->render('Core/contact/edit.html.twig', array(
            'contact'  => $contact,
            'edit_form' => $editForm->createView(),
            'valid'     => 1,
            'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
          ));
        }

        return $this->render('Core/contact/edit.html.twig', array(
          'contact'    => $contact,
          'edit_form'   => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
          'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ));
    
    }

    /**
     * @Route("/{id}", name="contact_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Contact $contact,  ContactRepository $contactRepository): Response
    {
    
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
          $em = $this->getDoctrine()->getManager();
          try {
            $em->remove($contact);
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes( html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8') );
            return $this->render('Core/contact/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $contactRepository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
        }

    return $this->redirectToRoute('contact_index');
    
    }
    
   /**
   * Creates a form to delete a contact entity.
   *
   * @param Contact $contact The contact entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Contact $contact) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
  
}
