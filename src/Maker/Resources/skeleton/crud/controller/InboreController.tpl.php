<?= "<?php\n" ?>
<?php $route_name = (substr($route_name, 0, 4)== 'app_')? substr($route_name, 4) : $route_name ; ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
use App\Repository\Core\<?= $entity_class_name?>Repository ;
use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunction;


/**
 * @Route("<?= $route_name ?>")
 */

class <?= $class_name ?> extends AbstractController<?= "\n" ?>
{
// InBORe Controller generation

/**
 * date of update  : 28/06/2022 
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
const ENTITY_PATH   = 'App\\Entity\\';
private $doctrine;
public function __construct(ManagerRegistry $doctrine) {
    $this->doctrine = $doctrine;
   }



/**
* @Route("/", name="<?= $route_name ?>_index", methods={"GET"})
*/
     
public function index(<?= $entity_class_name?>Repository $<?=  strtolower($entity_class_name ) ?>Repository): Response
{
    return $this->render('Core/<?= $templates_path ?>/index.html.twig', [
        'list_SQL_fetchedVariables' => $<?=  strtolower($entity_class_name ) ?>Repository->findSearch(1),
    ]);
}

 
/**
 * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType) 
 *
 * @Route("/search/{q}", name="<?= $route_name ?>_search", requirements={"q"=".+"} )
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
   * @Route("/indexjson", name="<?= $route_name ?>_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, <?=  $entity_class_name  ?>Repository $<?=  strtolower($entity_class_name ) ?>Repository) {
    return new JsonResponse($<?=  strtolower($entity_class_name ) ?>Repository->findSearch());
  }  
  
  /**
   * Creates a new <?= strtolower($entity_class_name) ?> entity for modal windows
   *
   * @Route("/newmodal", name="<?= $route_name ?>_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction( Request $request, FileUploader $fileUploader, $choice_label = null ) {
    $<?= strtolower($entity_class_name) ?> = new <?= $entity_class_name ?>();
    $form    = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= strtolower($entity_class_name) ?>, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form"  => $this->render('modal-form.html.twig', [
            'choice_label' => $request['choice_label'],
            'entityname' => '<?= strtolower($entity_class_name) ?>',
            'form'       => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $em = $this->doctrine->getManager();
        $em->persist($<?= strtolower($entity_class_name) ?>);       
        try {
            $flush = $em->flush();
            // upload
            if ($fileUploader->handleFile($form, $<?= strtolower($entity_class_name) ?>, '<?= strtolower($entity_class_name) ?>')) {
                $em->persist($<?= strtolower($entity_class_name) ?>);
                $em->flush();
            }
            $select_id   = $<?= strtolower($entity_class_name) ?>->getId();
            $method = 'get' . ucfirst($request->get('choice_label'));
            $select_name = $<?= strtolower($entity_class_name) ?>->$method();
            return new JsonResponse([
                'select_id' => $select_id,
                'select_name' => $select_name,
                'entityname' => '<?= strtolower($entity_class_name) ?>',
            ]);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return new JsonResponse([
                'exception' => true,
                'exception_message' => $e->getMessage(),
                'entityname' => '<?= strtolower($entity_class_name) ?>',
            ]);
        }                       
      }
    } 
    
    return $this->render('modal.html.twig', array(
      'choice_label' => $choice_label,
      'entityname' => '<?= strtolower($entity_class_name) ?>',
      'form'       => $form->createView(),
    ));
  }
  

    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader, GenericFunction $genericFunctionService): Response
    {
        $<?= strtolower($entity_class_name) ?> = new <?= $entity_class_name ?>();
        $em = $this->doctrine->getManager();
        
        // check if the relational Entity  is given
        if ($request->get('idFk') && $request->get('nameFk')) { 
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = ( substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository(self::ENTITY_PATH.$nameRelEntity)->find($request->get('idFk'));
            $nameRelEntityFk = $genericFunctionService->GetFkName($nameRelEntity);
            $method =  'set'.$nameRelEntityFk;
            $<?= strtolower($entity_class_name) ?>->$method($relationalEntity);
        }
        
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= strtolower($entity_class_name) ?>, ['action_type' => Action::create->value,]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($<?= strtolower($entity_class_name) ?>);
                try {
                    $em->flush();
                    if ($fileUploader->handleFiles($form, $<?= strtolower($entity_class_name) ?>)) {
                        $em->persist($<?= strtolower($entity_class_name) ?>);
                        $em->flush();
                    }
                } catch (\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('<?= $route_name ?>_index');
                }
                $this->addFlash('success', '<?= strtolower($entity_class_name) ?>_created');
                return $this->redirectToRoute('<?= $route_name ?>_edit', [
                    'id' => $<?= strtolower($entity_class_name) ?> ->getId(),
                    'valid' => 1,
                    'nameFk' => $request->get('nameFk'),
                    'idFk' => $request->get('idFk'),
                    'urlArg' => $request->get('urlArg'),
                    'backTo' => $request->get('backTo'),
                    'backToNameFk' => $request->get('backToNameFk'),
                    'backToIdFk' => $request->get('backToIdFk'),
                ]);
            } else {
                $this->addFlash('danger', $form->getErrors(true));
            }
        }
        
        // Initial form render or form invalid
        return $this->render('Core/<?= $route_name ?>/edit.html.twig', [
          '<?= strtolower($entity_class_name) ?>'  => $<?= strtolower($entity_class_name) ?>,
          'edit_form' => $form->createView(),
        ]);

    }
        

    /**
     * @Route("/{id}", name="<?= $route_name ?>_show", methods={"GET"})
     */
    public function show(<?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?>): Response
    {
    
        $deleteForm = $this->createDeleteForm($<?= strtolower($entity_class_name) ?>);
        $showForm   = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= strtolower($entity_class_name) ?>, [
          'action_type' => Action::show->value,
        ]);

        return $this->render('Core/<?= $route_name ?>/edit.html.twig', [
          '<?= strtolower($entity_class_name) ?>'    => $<?= strtolower($entity_class_name) ?>,
          'edit_form'   => $showForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    
    }


    /**
     * @Route("/{id}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, <?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?>, FileUploader $fileUploader): Response
    {
    
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $<?= strtolower($entity_class_name) ?>->getUserCre() != $user->getId()) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        // check if a relational Entity is given as urlArg parameter like nameRelEntity:idRelEntity
        // If  SET Fk value if idRelEntity > 0 or DELETE if idRelEntity = 0 
        if ($request->get('urlArg') && count(explode( ':', $request->get('urlArg'))) == 2) { 
            try {            
                // set the RelationalEntityFk for the new Entity
                $nameRelEntity = explode( ':', $request->get('urlArg'))[0];
                $idRelEntity = explode( ':', $request->get('urlArg'))[1];
                // test if idRelEntity is an integer
                if (is_numeric($idRelEntity) && ctype_digit($idRelEntity) ) { 
                    $repo = $em->getRepository(self::ENTITY_PATH.$nameRelEntity);
                    $relationalEntity = $repo->find($idRelEntity);
                    $nameRelEntityFk = $genericFunctionService->GetFkName($nameRelEntity);
                    // GET the Relational Entity to DELETE if needed
                    $method =  'get'.$nameRelEntityFk;
                    $relationalEntityToDelete = $<?= strtolower($entity_class_name) ?>->$method(); 
                    // SET the new id value for the Fk of the Relational Entity (=NULL if $idRelEntity=0)
                    $method =  'set'.$nameRelEntityFk;
                    $<?= strtolower($entity_class_name) ?>->$method($relationalEntity);
                    $em->persist($<?= strtolower($entity_class_name) ?>);
                    if($idRelEntity == 0 && $relationalEntityToDelete !== null) {
                        // DELETE procedure
                        $em->remove($relationalEntityToDelete);
                        $this->addFlash('success', $nameRelEntity.'_deleted');   
                    } 
                    // flush changes in the database
                    $em->flush();                    
                }
            } catch (Doctrine\Common\Persistence\Mapping\MappingException $e) {
                // NOPE! Not a mapped model
            }
        }        

        // set Edit form parameters
        $edit_form_parameters = array('form_parameters' => ['action_type' => Action::edit->value]);

        // editAction
        $deleteForm = $this->createDeleteForm($<?= strtolower($entity_class_name) ?>);
        $editForm   = $this->createForm('App\Form\<?= $entity_class_name ?>Type', $<?= strtolower($entity_class_name) ?>, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                // upload
                try {
                    $fileUploader->handleFiles($editForm, $<?= strtolower($entity_class_name) ?>);
                } catch (\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('<?= $route_name ?>_index');
                }

                // flush
                $this->doctrine->getManager()->persist($<?= strtolower($entity_class_name) ?>);
                try {
                    $this->doctrine->getManager()->flush();
                } catch (\Doctrine\DBAL\DBALException $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('<?= $route_name ?>_index');
                }

                //flash message
                $this->addFlash('success', '<?= strtolower($entity_class_name) ?>_updated');
                return $this->redirectToRoute('<?= $route_name ?>_edit', [
                    'id' => $<?= strtolower($entity_class_name) ?>->getId(),
                    'valid'     => 1,
                    'nameFk'    => $request->get('nameFk'),
                    'idFk'      => $request->get('idFk'),
                    'urlArg' => $request->get('urlArg'),
                    'backTo' => $request->get('backTo'),
                    'backToNameFk' => $request->get('backToNameFk'),
                    'backToIdFk' => $request->get('backToIdFk'),
                ]);
            } else {
                $this->addFlash('danger', $editForm->getErrors(true));
            }
        }        
        
        return $this->render('Core/<?= $route_name ?>/edit.html.twig', array(
          '<?= strtolower($entity_class_name) ?>'    => $<?= strtolower($entity_class_name) ?>,
          'edit_form'   => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ));
    
    }


    /**
     * @Route("/{id}", name="<?= $route_name ?>_delete", methods={"DELETE"})
     */
    public function delete(Request $request, <?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?>, FileUploader $fileUploader): Response
    {
    
        $form = $this->createDeleteForm($<?= strtolower($entity_class_name) ?>);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->doctrine->getManager();
            try {
                $fileUploader->handleFiles($form, $<?= strtolower($entity_class_name) ?>);
                $em->remove($<?= strtolower($entity_class_name) ?>);
                $em->flush();
                $this->addFlash('success', '<?= strtolower($entity_class_name) ?>_deleted');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
                $this->addFlash('danger', explode("\n", $exception_message)[0]);
            }
        }
        
    return $this->redirectToRoute('<?= $route_name ?>_index', [
                    'nameFk'    => $request->get('nameFk'),
                    'idFk'      => $request->get('idFk'),
                ]);
    
    }
    
   /**
   * Creates a form to delete a <?= strtolower($entity_class_name) ?> entity.
   *
   * @param <?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?> The <?= strtolower($entity_class_name) ?> entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(<?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?>) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('<?= $route_name ?>_delete', array('id' => $<?= strtolower($entity_class_name) ?>->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
  
    /**
     * @Route("/{id}/{field}/file", name="<?= $route_name ?>_file", methods={"GET"}, requirements={"field":".*"})
     */
    public function showFile(Request $request, <?= $entity_class_name ?> $<?= strtolower($entity_class_name) ?>, FileUploader $fileUploader, $field): Response
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if ($user->getRole() == 'ROLE_COLLABORATION' && $<?= strtolower($entity_class_name) ?>->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        try {
            $response = new BinaryFileResponse($fileUploader->getFile($<?= strtolower($entity_class_name) ?>, $field));
            $response->headers->set('Content-Type', $fileUploader->getMime($<?= strtolower($entity_class_name) ?>, $field));
            return $response;
        } catch (\Exception $e) {
            $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
            $this->addFlash('danger', explode("\n", $exception_message)[0]);
            return $this->redirectToRoute('<?= $route_name ?>_index');
        }

    }

  
}
