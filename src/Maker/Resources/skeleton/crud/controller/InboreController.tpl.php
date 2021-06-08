<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
use App\Repository\Core\<?= $entity_class_name?>Repository ;
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunction;

/**
 * INFO VAR 
 * repository_full_class_name : <?php if (isset($repository_full_class_name)): ?> <?= $repository_full_class_name ?>  <?php endif ?>
 *
 * repository_class_name : <?php if (isset($repository_class_name)): ?> <?= $repository_class_name ?> <?php endif ?>
 *
 * repository_var : <?php if (isset($repository_var)): ?> <?= $repository_var ?>  <?php endif ?>
 *
 * entity_twig_var_plural : <?php if (isset($entity_twig_var_plural)): ?> <?= $entity_twig_var_plural ?>  <?php endif ?>
 *
 * entity_class_name : <?php if (isset($entity_class_name)): ?> <?= $entity_class_name ?>  <?php endif ?>
 * 
 */

<?php if ($use_attributes) { ?>
#[Route('<?= $route_path ?>')]
<?php } else { ?>
/**
 * @Route("<?= $route_path ?>")
 */
<?php } ?>
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
// InBORe Controller generation

<?php if ($use_attributes) { ?>
    #[Route('/', name: '<?= $route_name ?>_index', methods: ['GET'])]
<?php } else { ?>
    /**
     * @Route("/", name="<?= $route_name ?>_index", methods={"GET"})
     */
<?php } ?>
     
public function index(<?= $entity_class_name?>Repository $<?=  strtolower($entity_class_name ) ?>Repository): Response
{
    return $this->render('Core/<?= $templates_path ?>/index.html.twig', [
        'list_SQL_fetchedVariables' => $<?=  strtolower($entity_class_name ) ?>Repository->findSearch("id ASC", "", null, null, 1),
    ]);
}

 
  /**
   * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType) 
   *
   * @Route("/search/{q}", name="<?= strtolower($entity_class_name) ?>_search", requirements={"q"=".+"} )
   */
  public function searchAction($q, <?=  $entity_class_name  ?>Repository $<?=  strtolower($entity_class_name ) ?>Repository) {
    
    $results = $<?=  strtolower($entity_class_name ) ?>Repository->findSearchAction($q);

    return $this->json($results);
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="<?= strtolower($entity_class_name) ?>_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, <?=  $entity_class_name  ?>Repository $<?=  strtolower($entity_class_name ) ?>Repository) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "<?= strtolower($entity_class_name) ?>.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;    
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
    $searchPhrase = $request->get('searchPattern');
    }   
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $entities_toshow =  $<?=  strtolower($entity_class_name ) ?>Repository->findSearch($orderBy, $searchPhrase, $request->get('idFk'), $request->get('nameFk') );
    } else {
      $entities_toshow =  $<?=  strtolower($entity_class_name ) ?>Repository->findSearch($orderBy, $searchPhrase);
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
   * Creates a new <?= $entity_var_singular ?> entity for modal windows
   *
   * @Route("/newmodal", name="<?= $entity_var_singular ?>_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction($choice_label = null, Request $request) {
    $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
    $form    = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= $entity_var_singular ?>, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form"  => $this->render('modal-form.html.twig', [
            'choice_label' => $request['choice_label'],
            'entityname' => '<?= $entity_var_singular ?>',
            'form'       => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $em = $this->getDoctrine()->getManager();
        $em->persist($<?= $entity_var_singular ?>);
        try {
          $flush       = $em->flush();
          $select_id   = $<?= $entity_var_singular ?>->getId();
          $method = 'get'.ucfirst($request->get('choice_label'));
          $select_name = $<?= $entity_var_singular ?>->$method();
          return new JsonResponse([
            'select_id'   => $select_id,
            'select_name' => $select_name,
            'entityname'  => '<?= $entity_var_singular ?>',
          ]);
        } catch (\Doctrine\DBAL\DBALException $e) {
          return new JsonResponse([
            'exception'         => true,
            'exception_message' => $e->getMessage(),
            'entityname'        => '<?= $entity_var_singular ?>',
          ]);
        }
      }
    } 
    
    return $this->render('modal.html.twig', array(
      'choice_label' => $choice_label,
      'entityname' => '<?= $entity_var_singular ?>',
      'form'       => $form->createView(),
    ));
  }
  
<?php if ($use_attributes) { ?>
    #[Route('/new', name: '<?= $route_name ?>_new', methods: ['GET', 'POST'])]
<?php } else { ?>
    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     */
<?php } ?>
    public function new(Request $request, <?= $entity_class_name?>Repository $<?=  strtolower($entity_class_name ) ?>Repository): Response
    {
        $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        $em = $this->getDoctrine()->getManager();
        
        // check if the relational Entity  is given
        if ($relationalEntity_id = $request->get('idFk') && $request->get('nameFk')) { 
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = ( substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository('App:'.$nameRelEntity)->find($relationalEntity_id);
            $method =  'set'.$nameRelEntity.'Fk';
            $<?= $entity_var_singular ?>->$method($relationalEntity);
        }
        
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, ['action_type' => Action::create(),]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em->persist($<?= $entity_var_singular ?>);
          try {
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/<?= $templates_path ?>/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $<?=  strtolower($entity_class_name ) ?>Repository->findSearch("id ASC", "", null, null, 1),
            ]);
            // return $this->render('Core/<?= $entity_var_singular ?>/index.html.twig', ['exception_message' => explode("\n", $exception_message)[0],]);
          }
          return $this->redirectToRoute('<?= $entity_var_singular ?>_edit', [
            'id'    => $<?= $entity_var_singular ?>->getId(),
            'valid' => 1,
            'idFk'  => $request->get('idFk'),
          ]);
        }
        // Initial form render or form invalid
        return $this->render('Core/<?= $entity_var_singular ?>/edit.html.twig', [
          '<?= $entity_var_singular ?>'  => $<?= $entity_var_singular ?>,
          'edit_form' => $form->createView(),
        ]);

    }
        

<?php if ($use_attributes) { ?>
    #[Route('/{<?= $entity_identifier ?>}', name: '<?= $route_name ?>_show', methods: ['GET'])]
<?php } else { ?>
    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_show", methods={"GET"})
     */
<?php } ?>
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
    
        $deleteForm = $this->createDeleteForm($<?= $entity_var_singular ?>);
        $showForm   = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= $entity_var_singular ?>, [
          'action_type' => Action::show(),
        ]);

        return $this->render('Core/<?= $entity_var_singular ?>/edit.html.twig', [
          '<?= $entity_var_singular ?>'    => $<?= $entity_var_singular ?>,
          'edit_form'   => $showForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    
    }

<?php if ($use_attributes) { ?>
    #[Route('/{<?= $entity_identifier ?>}/edit', name: '<?= $route_name ?>_edit', methods: ['GET', 'POST'])]
<?php } else { ?>
    /**
     * @Route("/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"})
     */
<?php } ?>
    public function edit(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>, <?= $entity_class_name?>Repository $<?=  strtolower($entity_class_name ) ?>Repository): Response
    {
    
        /** //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $<?= $entity_var_singular ?>->getUserCre() != $user->getId()) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        */

        // set Array Collection for N-N relationship and the link-embeded form if needed
        $edit_form_parameters = $<?=  strtolower($entity_class_name ) ?>Repository->editActionBeforeFormIsSubmitted($request, $<?= $entity_var_singular ?>);

        // editAction
        $deleteForm = $this->createDeleteForm($<?= $entity_var_singular ?>);
        $editForm   = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= $entity_var_singular ?>, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          // delete ArrayCollection
          $<?=  strtolower($entity_class_name ) ?>Repository->editActionAfterFormIsSubmitted($request, $<?= $entity_var_singular ?>);

          // flush
          $this->getDoctrine()->getManager()->persist($<?= $entity_var_singular ?>);
          try {
            $this->getDoctrine()->getManager()->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
            return $this->render('Core/<?= $templates_path ?>/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $<?=  strtolower($entity_class_name ) ?>Repository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
          return $this->render('Core/<?= $entity_var_singular ?>/edit.html.twig', array(
            '<?= $entity_var_singular ?>'  => $<?= $entity_var_singular ?>,
            'edit_form' => $editForm->createView(),
            'valid'     => 1,
            'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
          ));
        }

        return $this->render('Core/<?= $entity_var_singular ?>/edit.html.twig', array(
          '<?= $entity_var_singular ?>'    => $<?= $entity_var_singular ?>,
          'edit_form'   => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
          'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ));
    
    }

<?php if ($use_attributes) { ?>
    #[Route('/{<?= $entity_identifier ?>}', name: '<?= $route_name ?>_delete', methods: ['DELETE'])]
<?php } else { ?>
    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_delete", methods={"DELETE"})
     */
<?php } ?>
    public function delete(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>,  <?= $entity_class_name?>Repository $<?=  strtolower($entity_class_name ) ?>Repository): Response
    {
    
        $form = $this->createDeleteForm($<?= $entity_var_singular ?>);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
          $em = $this->getDoctrine()->getManager();
          try {
            $em->remove($<?= $entity_var_singular ?>);
            $em->flush();
          } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = addslashes( html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8') );
            return $this->render('Core/<?= $templates_path ?>/index.html.twig', [
                'exception_message' => explode("\n", $exception_message)[0],
                'list_SQL_fetchedVariables' => $<?=  strtolower($entity_class_name ) ?>Repository->findSearch("id ASC", "", null, null, 1),
            ]);
          }
        }

    return $this->redirectToRoute('<?= $entity_var_singular ?>_index');
    
    }
    
   /**
   * Creates a form to delete a <?= $entity_var_singular ?> entity.
   *
   * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?> The <?= $entity_var_singular ?> entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(<?= $entity_class_name ?> $<?= $entity_var_singular ?>) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('<?= $entity_var_singular ?>_delete', array('id' => $<?= $entity_var_singular ?>->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
  

  
}
