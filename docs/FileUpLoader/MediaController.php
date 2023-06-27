<?php

namespace App\Controller\Core;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\Core\MediaRepository;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunction;


/**
 * @Route("/media")
 */
class MediaController extends AbstractController
{
    /**
     * date of update  : 28/06/2022 
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
       }



    /**
     * @Route("/", name="media_index", methods={"GET"})
     * @Security("is_granted('ROLE_INVITED')")
     */

    public function index(MediaRepository $mediaRepository): Response
    {
        return $this->render('Core/media/index.html.twig', [
            'list_SQL_fetchedVariables' => $mediaRepository->findSearch("", "", null, null, 1),
        ]);
    }


    /**
     * Returns in json format a set of records for auto-complete field (see App\Form\Type\SearchableSelectType)
     *
     * @Route("/search/{q}", name="media_search", requirements={"q"=".+"} )
     */
    public function searchAction($q, MediaRepository $mediaRepository)
    {

        $results = $mediaRepository->findSearchAction($q);

        return $this->json($results);
    }


    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="media_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, MediaRepository $mediaRepository)
    {
        $rowCount = $request->get('rowCount') ?: 10;
        $orderBy = $request->get('sort')
            ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
            : "";
        $minRecord = intval($request->get('current') - 1) * $rowCount;
        $searchPhrase = $request->get('searchPhrase');
        if ($request->get('searchPattern') && !$searchPhrase) {
            $searchPhrase = $request->get('searchPattern');
        }
        if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
            $entities_toshow = $mediaRepository->findSearch($orderBy, $searchPhrase, $request->get('idFk'), $request->get('nameFk'));
        } else {
            $entities_toshow = $mediaRepository->findSearch($orderBy, $searchPhrase);
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
     * Creates a new media entity for modal windows
     *
     * @Route("/newmodal", name="media_newmodal", methods={"GET", "POST"})
     */
    public function newmodalAction( Request $request, FileUploader $fileUploader, $choice_label = null )
    {
        $media = new Media();
        $form = $this->createForm('App\Form\Core\MediaType', $media, [
            'action_type' => Action::create(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return new JsonResponse([
                    'valid' => false,
                    "form" => $this->render('modal-form.html.twig', [
                        'choice_label' => $request['choice_label'],
                        'entityname' => 'media',
                        'form' => $form->createView(),
                    ])->getContent(),
                ]);
            } else {
                $em = $this->doctrine->getManager();
                $em->persist($media);
                try {
                    $em->flush();
                    // upload
                    if ($fileUploader->handleFile($form, $media, 'media')) {
                        $em->persist($media);
                        $em->flush();
                    }

                    $select_id = $media->getId();
                    $method = 'get' . ucfirst($request->get('choice_label'));
                    $select_name = $media->$method();
                    return new JsonResponse([
                        'select_id' => $select_id,
                        'select_name' => $select_name,
                        'entityname' => 'media',
                    ]);
                } catch (\Doctrine\DBAL\Exception $e) {
                    return new JsonResponse([
                        'exception' => true,
                        'exception_message' => $e->getMessage(),
                        'entityname' => 'media',
                    ]);
                }
            }
        }

        return $this->render('modal.html.twig', array(
            'choice_label' => $choice_label,
            'entityname' => 'media',
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/new", name="media_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_COLLABORATION')")
     */
    public function new(Request $request, FileUploader $fileUploader, GenericFunction $genericFunctionService): Response
    {
        $media = new Media();
        $em = $this->doctrine->getManager();

        // check if the relational Entity  is given
        if ($request->get('idFk') && $request->get('nameFk')) {
            // set the RelationalEntityFk for the new Entity
            $nameRelEntity = (substr($request->get('nameFk'), 0, 4) == "core") ? substr($request->get('nameFk'), 4, -2) : $request->get('nameFk', 0, -2);
            $relationalEntity = $em->getRepository('App\\Entity\\' . $nameRelEntity)->find($request->get('idFk'));
            $nameRelEntityFk = $genericFunctionService->GetFkName($nameRelEntity);
            $method = 'set' . $nameRelEntityFk;
            $media->$method($relationalEntity);
        }

        $form = $this->createForm(MediaType::class, $media, ['action_type' => Action::create(),]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($media);
                try {
                    $em->flush();
                    // upload
                    if ($fileUploader->handleFile($form, $media, 'media')) {
                        $em->persist($media);
                        $em->flush();
                    }
                } catch (\Doctrine\DBAL\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('media_index');
                }
                return $this->redirectToRoute('media_edit', [
                    'id' => $media->getId(),
                    'valid' => 1,
                    'idFk' => $request->get('idFk'),
                ]);
            } else {
                $this->addFlash('danger', $form->getErrors(true));
            }
        }
        // Initial form render or form invalid
        return $this->render('Core/media/edit.html.twig', [
            'media' => $media,
            'edit_form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="media_show", methods={"GET"})
     */
    public function show(Media $media): Response
    {

        $deleteForm = $this->createDeleteForm($media);
        $showForm = $this->createForm('App\Form\Core\MediaType', $media, [
            'action_type' => Action::show(),
        ]);

        return $this->render('Core/media/edit.html.twig', [
            'media' => $media,
            'edit_form' => $showForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);

    }

    /**
     * @Route("/{id}/edit", name="media_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_COLLABORATION')")
     */
    public function edit(Request $request, Media $media, FileUploader $fileUploader): Response
    {

        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $media->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        // set Array Collection for N-N relationship and the link-embeded form if needed
        //$edit_form_parameters = $mediaRepository->editActionBeforeFormIsSubmitted($request, $media);
        $edit_form_parameters = array('form_parameters' => ['action_type' => Action::edit(),],
            //'embeded_form' => (isset($embeded_form)) ? $embeded_form : NULL,
        );
        // editAction
        $deleteForm = $this->createDeleteForm($media);
        $editForm = $this->createForm('App\Form\Core\MediaType', $media, $edit_form_parameters['form_parameters']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {

                // upload
                try {
                    $fileUploader->handleFile($editForm, $media, 'media');
                } catch (\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('media_index');
                }

                // flush
                $this->doctrine->getManager()->persist($media);
                try {
                    $this->doctrine->getManager()->flush();
                } catch (\Doctrine\DBAL\Exception $e) {
                    $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
                    $this->addFlash('danger', explode("\n", $exception_message)[0]);
                    return $this->redirectToRoute('media_index');
                }

                //@TODO use flash messages
                return $this->redirectToRoute('media_edit', ['id' => $media->getId()]);
            } else {
                $this->addFlash('danger', $editForm->getErrors(true));
            }
        }

        return $this->render('Core/media/edit.html.twig', array(
            'media' => $media,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'edit_embeded_form' => ($edit_form_parameters['embeded_form'] !== NULL) ? $edit_form_parameters['embeded_form']->createView() : NULL,
        ));

    }

    /**
     * @Route("/{id}", name="media_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_COLLABORATION')")
     */
    public function delete(Request $request, Media $media, FileUploader $fileUploader): Response
    {

        $form = $this->createDeleteForm($media);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->doctrine->getManager();
            try {
                $fileUploader->handleFile($form, $media, 'media');
                $em->remove($media);
                $em->flush();
                $this->addFlash('success', 'media_deleted');
            } catch (\Doctrine\DBAL\Exception $e) {
                $exception_message = addslashes(html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8'));
                $this->addFlash('danger', explode("\n", $exception_message)[0]);
            }
        }

        return $this->redirectToRoute('media_index', [
                    'nameFk'    => $request->get('nameFk'),
                    'idFk'      => $request->get('idFk'),
                ]);

    }

    /**
     * Creates a form to delete a media entity.
     *
     * @param Media $media The media entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Media $media)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('media_delete', array('id' => $media->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


    /**
     * @Route("/{id}/file", name="media_file", methods={"GET"})
     */
    public function getFile(Request $request, Media $media, FileUploader $fileUploader): Response
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_COLLABORATION' && $media->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        try {
            $response = new BinaryFileResponse($fileUploader->getFile($media));
            $response->headers->set('Content-Type', $fileUploader->getMime($media));
            return $response;
        } catch (\Exception $e) {
            $exception_message = html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8');
            $this->addFlash('danger', explode("\n", $exception_message)[0]);
            return $this->redirectToRoute('media_index');
        }

    }


}
