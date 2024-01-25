<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Services\Core\GenericFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends EntityController {  

  #[Route("/", name: "dashboard")]
  public function indexAction( GenericFunction $service) {

    /** 
     */
    $tab_toshow = [];
    // returns the last records of Table
    /** Exemple of Query 
    $entities_toshow = $this->getRepository(Entity::class)->createQueryBuilder('e')
      ->leftJoin('e.idLinkedTable', 'lt')
      ->where('lt.dateMaj IS NOT NULL')
      ->addOrderBy('e.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "code" => $entity->idLinkedTable()->getCode(),
        ...
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }   
     */
    
   // Sort the array $tab_toshow
    $sort=array();
      foreach ($tab_toshow as $key => $part) {
           $sort[$key] = strtotime($part['dateMaj']);
      }
      array_multisort($sort, SORT_DESC, $tab_toshow);
      
   
    return $this->render('Core/dashboard/index.html.twig', array(
      /**
      'nbCollecte' => $nbcollectes,
       */
      'entities' => $tab_toshow,
    ));
  }

   /**
   * 
   */
  #[Route("/phpinfo/", name: "php_info", methods: ["GET"])]
  #[IsGranted('ROLE_ADMIN')]
  public function phpInfo(Request $request) {
    $locale = $request->getLocale();
    $phpinfo = phpinfo();
    return $this->render('misc/phpinfo.html.twig',['phpinfo' => $phpinfo]);
  }
  

}
