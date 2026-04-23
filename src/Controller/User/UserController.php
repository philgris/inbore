<?php

/*
 *
 * Authors : see information concerning authors of InBORe project in file AUTHORS.md
 *
 * InBORE is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * InBORE is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Controller\User;

use App\Controller\EntityController;
use App\Entity\Core\User;
use App\Form\Enums\Action;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * User controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("user")]
class UserController extends EntityController {

  /**
   * Lists all user entities.
   *
   * 
   */
  #[Route("/", name: "user_index", methods: ["GET"])]
  #[IsGranted('ROLE_ADMIN')]
  public function indexAction() {

    $users = $this->entityManager->getRepository(User::class)->findAll();

    return $this->render('user/index.html.twig', array(
      'users' => $users,
    ));
  }

  /**
   * Get currently logged in user public informations
   *
   */
  #[Route("/current", name: "user_current", methods: ["GET"])]
  public function currentUserAction() {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    $user = $this->getUser();
    return new JSONResponse([
      "username" => $user->getUsername(),
      "role" => $user->getRole(),
      "name" => $user->getName(),
      "institution" => $user->getInstitution(),
      "email" => $user->getEmail(),
    ]);
  }

  /**
   * Return to json format a list of fields to show  tab_station_toshow with the following criterion :
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   */
  #[Route("/indexjson", name: "user_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'user.dateMaj' => 'desc',
      'user.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $tab_toshow = [];
    $entities_toshow = $this->entityManager->getRepository(User::class)
      ->createQueryBuilder('user')
      ->where('LOWER(user.username) LIKE :criteriaLower')
      ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')) . '%')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb_entities = count($entities_toshow);
    $entities_toshow = array_slice($entities_toshow, $minRecord, $rowCount);

    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateCre = ($entity->getDateCre() !== null)
       ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
       ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "user.id" => $id,
        "user.username" => $entity->getUsername(),
        "user.password" => $entity->getPassword(),
        "user.email" => $entity->getEmail(),
        "user.role" => $entity->getRole(),
        "user.name" => $entity->getName(),
        "user.institution" => $entity->getInstitution(),
        "user.commentaireUser" => $entity->getCommentaireUser(),
        "user.dateCre" => $DateCre,
        "user.dateMaj" => $DateMaj,
        "user.userCre" => $entity->getUserCre(),
        "user.userMaj" =>  $entity->getUserMaj(),
      );
    }
    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "total" => $nb_entities, // total data array
    ]);
  }

  /**
   * Creates a new user entity.
   *
   * 
   */
  #[Route("/new", name: "user_new", methods: ["GET", "POST"])]
  #[IsGranted('ROLE_ADMIN')]
  public function newAction(Request $request, UserPasswordHasherInterface $hasher) {
    $user = new User();
    $form = $this->createForm('App\Form\Core\UserType', $user, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $plainPassword = $user->getPlainPassword();
      $passwordHash = $hasher->hashPassword($user, $plainPassword);

      $user->setPassword($passwordHash);
      $this->entityManager->persist($user);
      
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'user/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('user_edit', array(
        'id' => $user->getId(), 'valid' => 1,
      ));
    }

    return $this->render('user/edit.html.twig', array(
      'user' => $user,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a user entity.
   *
   */
  #[Route("/{id}", name: "user_show", methods: ["GET"])]
  public function showAction(User $user) {
    $deleteForm = $this->createDeleteForm($user);

    $editForm = $this->createForm('App\Form\Core\UserType', $user, [
      'action_type' => Action::show->value,
    ]);
    return $this->render('user/edit.html.twig', array(
      'user' => $user,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing user entity.
   *
   * 
   */
  #[Route("/{id}/edit", name: "user_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, User $user, UserPasswordHasherInterface $hasher) {
    // Vérifier que l'utilisateur est bien authentifié
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    // Récupérer l'utilisateur actuellement connecté
    $currentUser = $this->getUser();
    // test si l'utilisateur est ADMIN ou SUPER_ADMIN
    $isCurrentUserSuperAdmin  =  ($currentUser->getRole() == 'ROLE_SUPER_ADMIN') ? true : false;
    $isCurrentUserAdmin   =  ($currentUser->getRole() == 'ROLE_ADMIN') ? true : false;
    // Vérifier si l'utilisateur à éditer est un super administrateur
    $isTargetUserSuperAdmin = in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    // Vérifier si l'utilisateur à éditer est un administrateur
    $isTargetUserAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
    // Empêcher les administrateurs d'éditer d'autres administrateurs ou super administrateurs (sauf eux-mêmes)
    if ($isCurrentUserAdmin) {
        if (($isTargetUserAdmin || $isTargetUserSuperAdmin) && $currentUser->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('ACCESS DENIED: Admins cannot edit other admins or super admins.');
        }
    }
    // Empêcher les non-administrateurs d'éditer d'autres utilisateurs
    if (!$isCurrentUserAdmin && !$isCurrentUserSuperAdmin && $currentUser->getId() !== $user->getId()) {
        throw $this->createAccessDeniedException('ACCESS DENIED: You can only edit your own profile.');
    }    
    
    // Créer les formulaires de suppression et d'edition
    $deleteForm = $this->createDeleteForm($user);
    $editForm = $this->createForm('App\Form\Core\UserType', $user, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        if($editForm->getViewData()->getPlainPassword()!=null) {
            $plainPassword = $user->getPlainPassword();
            $passwordHash = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($passwordHash);
        }

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'user/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('user/edit.html.twig', array(
        'user' => $user,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('user/edit.html.twig', array(
      'user' => $user,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a user entity.
   *
   */
    #[Route("/{id}", name: "user_delete", methods: ["DELETE", "POST"])]
    #[IsGranted('ROLE_ADMIN')]
  public function deleteAction(Request $request, User $user) {
        
    // Vérifier que l'utilisateur est bien authentifié
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    // Récupérer l'utilisateur actuellement connecté
    $currentUser = $this->getUser();
    // test si l'utilisateur est ADMIN ou SUPER_ADMIN
    $isCurrentUserSuperAdmin  =  ($currentUser->getRole() == 'ROLE_SUPER_ADMIN') ? true : false;
    $isCurrentUserAdmin   =  ($currentUser->getRole() == 'ROLE_ADMIN') ? true : false;
    // Vérifier si l'utilisateur à supprimer est un super administrateur
    $isTargetUserSuperAdmin = in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    // Vérifier si l'utilisateur à supprimer est un administrateur
    $isTargetUserAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
    // Empêcher les administrateurs de supprimer d'autres administrateurs ou super administrateurs
    if ($isCurrentUserAdmin) {
        if ($isTargetUserAdmin || $isTargetUserSuperAdmin) {
            throw $this->createAccessDeniedException('ACCESS DENIED: Admins cannot DELETE other User admin or super-admin');
        }
    }
    // Empêcher les non-administrateurs de supprimer d'autres utilisateurs
    if (!$isCurrentUserAdmin && !$isCurrentUserSuperAdmin ) {
        throw $this->createAccessDeniedException('ACCESS DENIED: You cannot DELETE User profile.');
    }        
        
    $form = $this->createDeleteForm($user);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');

    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      if ($isCurrentUserAdmin  || $isCurrentUserSuperAdmin) {
        try {
          $this->entityManager->remove($user);
          $this->entityManager->flush();
        } catch (\Exception $e) {
          $exception_message = addslashes(
            html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
          );
          return $this->render(
            'user/index.html.twig',
            ['exception_message' => explode("\n", $exception_message)[0]]
          );
        }
      } else {
        return $this->render('user/index.html.twig', array('exception_message' => 'You can\'t delete this Admin user account'));
      }
    }

    return $this->redirectToRoute('user_index');
  }

  /**
   * Creates a form to delete a user entity.
   *
   * @param User $user The user entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(User $user) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
