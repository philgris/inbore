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

use App\Services\Core\GenericFunction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class DashboardController extends AbstractController {  
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
   * @Route("/", name="dashboard")
   * @Security("is_granted('ROLE_INVITED')")
   * @author Philippe Grison  <philippe.grison@mnhn.fr>
   */
  public function indexAction( GenericFunction $service) {
    // load Doctrine Manager
    $em = $this->doctrine->getManager();
    /** 
     */
    $tab_toshow = [];
    // returns the last records of Table
    /** Exemple of Query 
    $entities_toshow = $em->getRepository(self::ENTITY_PATH.'Table')->createQueryBuilder('t')
      ->leftJoin('t.idLinkedTable', 'lt')
      ->where('lt.dateMaj IS NOT NULL')
      ->addOrderBy('t.dateMaj', 'DESC')
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
   * @Route("/phpinfo/", name="php_info", methods={"GET"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function phpInfo(Request $request) {
    $locale = $request->getLocale();
    $phpinfo = phpinfo();
    return $this->render('misc/phpinfo.html.twig',['phpinfo' => $phpinfo]);
  }
  

}
