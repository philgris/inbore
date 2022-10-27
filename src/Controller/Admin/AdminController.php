<?php

namespace App\Controller\Admin;

use App\Repository\Core\AdminRepository;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunction;
use Twig\Environment;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    const CONTROLLER_PATH   = 'App\\Controller\\Core\\';
    const FORM_PATH         = 'App\\Form\\';
    const ENTITY_PATH   = 'App\\Entity\\';

    // config from admin.yaml
    private $config;
    private $repository;
    private $uploader;
    private $doctrine;

    public function __construct(
        ParameterBagInterface $config,
        AdminRepository $repository,
        FileUploader $fileUploader,
        ManagerRegistry $doctrine
    )
    {
        $this->config = $config->get('admin');
        $this->repository = $repository;
        $this->uploader = $fileUploader;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/{entity}", name="admin_index", requirements={"entity"="[a-z_]+"}, methods={"GET"})
     */

    public function index($entity, Environment $twig): Response
    {
//        override route:
//          @Route("/admin/<entity>, ..., priority=1)

        $class = $this->getSnakeToUpper($entity);

//        override index method:
//        if (method_exists(self::CONTROLLER_PATH.$class.'Controller', 'index')) {
//            return $this->forward(self::CONTROLLER_PATH.$class.'Controller::index', ['entity' => $entity]);
//        }

//        override index template:
//        $template = $twig->getLoader()->exists('Core/'.$entity.'/index.html.twig')
//            ? 'Core/'.$entity.'/index.html.twig'
//            : 'Admin/index.html.twig'
//        ;
        return $this->render('Admin/index.html.twig', [
            'list_SQL_fetchedVariables' => $this->doctrine->getRepository(self::ENTITY_PATH.$class)->dql_findSearch(1),
        ]);
    }


    /**
     * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType)
     *
     * @Route("/{entity}/search/{q}", name="admin_search", requirements={"q"=".+"} )
     */
    public function searchAction($entity, $q)
    {
        //@TODO cf: BehaviorStudyType > gps : SearchableSelectType
        $results = $this->repository->findSearchAction($entity, $q);
        return $this->json($results);
    }


    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/{entity}/indexjson", name="admin_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, $entity)
    {
        $class = $this->getSnakeToUpper($entity);
        $json = $this->doctrine->getRepository(self::ENTITY_PATH.$class)->dql_findSearch();
        return new JsonResponse($json);
    }

    /**
     * Creates a new protocol entity for modal windows
     *
     * @Route("/{entity}/newmodal", name="admin_newmodal", methods={"GET", "POST"})
     */
    public function newmodalAction($entity, Request $request, $choice_label = null)
    {
        //@TODO
        $class = $this->getSnakeToUpper($entity);
        try {
            $rc = new \ReflectionClass(self::ENTITY_PATH.$class);
            $object = $rc->newInstance();
        } catch(\Exception $e) {
            $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
            $this->addFlash('danger', explode("\n", $exception_message)[0]);
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }
        $form = class_exists(self::FORM_PATH.$class.'Type')
            ? $this->createForm(self::FORM_PATH.$class.'Type', $object, ['action_type' => Action::create->value])
            : $this->createForm(self::FORM_PATH.'Admin\\AdminType', $object, ['action_type' => Action::create->value])
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return new JsonResponse([
                    'valid' => false,
                    "form"  => $this->render('modal-form.html.twig', [
                        'choice_label'  => $choice_label,
                        'entity'        => $entity,
                        'form'          => $form->createView(),
                    ])->getContent(),
                ]);
            } else {
                $em = $this->doctrine->getManager();
                $em->persist($object);
                try {
                    $em->flush();
                    if ($this->uploader->handleFile($form, $object, $entity)) {
                        $em->persist($object);
                        $em->flush();
                    }
                    $select_id = $object->getId();
                    $method = 'get' . ucfirst($request->get('choice_label'));
                    $select_name = $object->$method();
                    return new JsonResponse([
                        'select_id'     => $select_id,
                        'select_name'   => $select_name,
                        'entityname'    => $entity
                    ]);
                } catch (\Doctrine\DBAL\DBALException $e) {
                    return new JsonResponse([
                        'exception'         => true,
                        'exception_message' => $e->getMessage(),
                        'entityname'        => $entity
                    ]);
                }
            }
        }

        return $this->render('modal.html.twig', array(
            'choice_label'  => $choice_label,
            'entityname'    => $entity,
            'entity'        => $entity,
            'form'          => $form->createView(),
        ));
    }

    /**
     * @Route("/{entity}/new", name="admin_new", methods={"GET","POST"})
     */
    public function new(Request $request, GenericFunction $genericFunctionService, $entity): Response
    {
        $class = $this->getSnakeToUpper($entity);
        try {
            $rc = new \ReflectionClass('App\\Entity\\'.$class);
            $object = $rc->newInstance();
        } catch(\Exception $e) {
            $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
            $this->addFlash('danger', explode("\n", $exception_message)[0]);
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }

        $em = $this->doctrine->getManager();

        // check if the relational Entity  is given
        if ($request->get('idFk') && $request->get('nameFk')) {
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = (substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository(self::ENTITY_PATH. $nameRelEntity)->find($request->get('idFk'));
            $nameRelEntityFk = $genericFunctionService->GetFkName($nameRelEntity);
            $method = 'set' . $nameRelEntityFk;
            $object->$method($relationalEntity);
        }

        $form = class_exists(self::FORM_PATH.$class.'Type')
            ? $this->createForm(self::FORM_PATH.$class.'Type', $object, ['action_type' => Action::create->value])
            : $this->createForm(self::FORM_PATH.'Admin\\AdminType', $object, ['action_type' => Action::create->value])
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($object);
                try {
                    $em->flush();
                    if ($this->uploader->handleFile($form, $object, $entity)) {
                        $em->persist($object);
                        $em->flush();
                    }
                } catch (\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('admin_index', ['entity' => $entity]);
                }
                return $this->redirectToRoute('admin_edit', [
                    'entity' => $entity,
                    'id' => $object->getId(),
                    'idFk' => $request->get('idFk')
                ]);
            } else {
                $this->addFlash('danger', $form->getErrors(true));
            }
        }
        // Initial form render or form invalid
        return $this->render('Admin/edit.html.twig', [
            'entity'    => $entity,
            'object'    => $object,
            'edit_form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{entity}/{id}/show", name="admin_show", methods={"GET"})
     */
    public function show($entity, $id): Response
    {
        $class = $this->getSnakeToUpper($entity);
        $object = $this->doctrine->getRepository(self::ENTITY_PATH.$class)->find($id);
        if (!$object) {
            $this->addFlash('danger', $entity.'_not_found');
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }
        $deleteForm = $this->createDeleteForm($entity, $id);
//        $showForm = $this->createForm(self::FORM_PATH.$class.'Type', $object, [ 'action_type' => Action::show->value ]);
        $showForm = class_exists(self::FORM_PATH.$class.'Type')
            ? $this->createForm(self::FORM_PATH.$class.'Type', $object, ['action_type' => Action::show->value])
            : $this->createForm(self::FORM_PATH.'Admin\\AdminType', $object, ['action_type' => Action::show->value])
        ;

        return $this->render('Admin/edit.html.twig', [
            'entity'    => $entity,
            'object'    => $object,
            'edit_form' => $showForm->createView(),
            //'delete_form' => $deleteForm->createView(),
            //'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ]);
    }

    /**
     * @Route("/{entity}/{id}/edit", name="admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $entity, $id): Response
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $class = $this->getSnakeToUpper($entity);
        $object = $this->doctrine->getRepository(self::ENTITY_PATH.$class)->find($id);
        if (!$object) {
            $this->addFlash('danger', $entity.'_not_found');
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }
        if ($user->getRole() == 'ROLE_COLLABORATION' && $object->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        // set Array Collection for N-N relationship and the link-embeded form if needed
//        $edit_form_parameters = [
//            'form_parameters' => ['action_type' => Action::edit->value,],
//            //'embeded_form' => (isset($embeded_form)) ? $embeded_form : NULL,
//        ];
        // editAction
        //$deleteForm = $this->createDeleteForm($object);
        $editForm = class_exists(self::FORM_PATH.$class.'Type')
            ? $this->createForm(self::FORM_PATH.$class.'Type', $object, ['action_type' => Action::edit->value])
            : $this->createForm(self::FORM_PATH.'Admin\\AdminType', $object, ['action_type' => Action::edit->value])
        ;
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                // upload
                try {
                    $this->uploader->handleFile($editForm, $object, $entity);
                } catch (\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('admin_index', ['entity' => $entity]);
                }

                // flush
                $this->doctrine->getManager()->persist($object);
                try {
                    $this->doctrine->getManager()->flush();
                } catch (\Doctrine\DBAL\DBALException $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('admin_index', ['entity' => $entity]);
                }

                //flash message
                $this->addFlash('success', $entity . '_updated');
                return $this->redirectToRoute('admin_edit', ['entity' => $entity, 'id' => $object->getId()]);
            } else {
                $this->addFlash('danger', $editForm->getErrors(true));
            }
        }

        return $this->render('Admin/edit.html.twig', [
            'entity'    => $entity,
            'object'    => $object,
            'edit_form' => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
            //'edit_embeded_form' => ($edit_form_parameters['embeded_form']!== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL ,
        ]);

    }

    /**
     * @Route("/{entity}/{id}/delete", name="admin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $entity, $id): Response
    {
        $class = $this->getSnakeToUpper($entity);
        $object = $this->doctrine->getRepository(self::ENTITY_PATH.$class)->find($id);
        if (!$object) {
            $this->addFlash('danger', $entity.'_not_found');
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }
        $form = $this->createDeleteForm($entity, $id);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->doctrine->getManager();
            try {
                $this->uploader->handleFile($form, $object, $entity);
                $em->remove($object);
                $em->flush();
                $this->addFlash('success', $entity.'_deleted');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
                $this->addFlash('danger', explode("\n", $exception_message)[0]);
            }
        }

        return $this->redirectToRoute('admin_index', ['entity' => $entity]);

    }

    private function createDeleteForm($entity, $id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_delete', ['entity' => $entity, 'id' => $id]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Transform snake case <name_of_entity> to upper case <NameOfEntity>
     *
     * @param $entity
     * @return mixed
     */
    private function getSnakeToUpper($entity)
    {
        return implode('', array_map(function($str){return ucfirst($str);}, explode('_', $entity)));
    }

    /**
     * @Route("/{entity}/{id}/file", name="admin_file", methods={"GET"})
     */
    public function showFile(Request $request, $entity, $id): Response
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $class = $this->getSnakeToUpper($entity);
        $object = $this->doctrine->getRepository(self::ENTITY_PATH.$class)->find($id);
        if (!$object) {
            $this->addFlash('danger', $entity.'_not_found');
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }
        if ($user->getRole() == 'ROLE_COLLABORATION' && $object->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        try {
            $response = new BinaryFileResponse($this->uploader->getFile($object));
            $response->headers->set('Content-Type', $this->uploader->getMime($object));
            return $response;
        } catch (\Exception $e) {
            $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
            $this->addFlash('danger', explode("\n", $exception_message)[0]);
            return $this->redirectToRoute('admin_index', ['entity' => $entity]);
        }

    }


}
