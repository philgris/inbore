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

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


/**
 * Service GenericFunction
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class GenericFunction
{
    private $entityManager;
    private $config;
    private $delimiter;
    private $prefixe;
    private $suffixe;
    const FIXE_FK_DELIMITER = '_'; //  ex. '_' if the foreign key name like : prefixe_entityRel / entityRel_suffixe
    const PREFIXE_FK = 'id'; // ex. 'id' if the foreign key name like : id_entityRel
    const SUFFIXE_FK = ''; // ex. 'fk' if the foreign key name like : entityRel_fk

     
    public function __construct(EntityManagerInterface $manager, ParameterBagInterface $config) {
        $this->entityManager = $manager ;
        $this->config = $config->get('admin');
        $this->delimiter = ( isset($this->config['fk_name']['fixe_fk_delimiter']) ) ? $this->config['fk_name']['fixe_fk_delimiter'] : self::FIXE_FK_DELIMITER;
        $this->prefixe = ( isset($this->config['fk_name']['prefixe_fk']) ) ? $this->config['fk_name']['prefixe_fk'] : self::PREFIXE_FK;
        $this->suffixe = ( isset($this->config['fk_name']['suffixe_fk']) ) ? $this->config['fk_name']['suffixe_fk'] : self::SUFFIXE_FK;
    }

    /**
     *  GetNameToSymfony($db_name, $type='field')
     * function that return name (field/entity/function) used by symfony from db_name (field/table)
     * return for db_name = dbName (cas $type=field)
     * return for  db_name = DbName(cas $type=entity)
     * return for  db_name = setDbName (cas $type=set)
     * return for  db_name = getDbName (cas $type=get)
     */
    public function GetNameToSymfony($db_name, $type = 'field') {

      // the type can be 'field' or 'table'
      $db_name_in_array = explode('_', $db_name);
      $name_to_symfony = '';
      $compt                   = 0;
      foreach ($db_name_in_array as $v) {
        if (!$compt && $type == 'field') {
          $name_to_symfony = $v;
        } else {
          $name_to_symfony = $name_to_symfony. ucfirst($v);
        }
        $compt++;
      }
      if ($type == 'set') {
        $name_to_symfony = 'set' . $name_to_symfony;
      }
      if ($type == 'get') {
        $name_to_symfony = 'get' . $name_to_symfony ;
      }

      return $name_to_symfony;
    }

    public function GetFkName($entityRel){
        if(isset($this->config['fk_name'][$entityRel]) ) {
            $nameFk = $this->config['fk_name'][$entityRel];
        } else {
            if ($this->prefixe !== '') {
                $nameFk = ($this->delimiter == '_') ? ucfirst($this->prefixe).ucfirst($entityRel) : ucfirst($this->prefixe).$this->delimiter.$entityRel;
            } else {
                $nameFk = $entityRel;
            }
            if ($this->suffixe !== '') {
                $nameFk = ($this->delimiter == '_') ? $nameFk.ucfirst($this->suffixe) : $nameFk.$this->delimiter.$this->suffixe;
            }   
        }
        return $nameFk;
    }
        
    public function GetUserCreId($entity){
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        return $userCreId;
    }
    
    public function GetUserCreUsername($entity){
        $em = $this->entityManager;
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        $query = $em->createQuery('SELECT user.username FROM App:Core\\User user WHERE user.id = '.$userCreId.'')->getResult();
        $userCre = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userCre;
    }
 
    public function GetUserMajUsername($entity){
        $em = $this->entityManager;
        $userMajId = ($entity->getUserMaj() !== null) ? $entity->getUserMaj() : 0;
        $query = $em->createQuery('SELECT user.username FROM App:Core\\User user WHERE user.id = '.$userMajId.'')->getResult();
        $userMaj = (count($query) > 0) ? $query[0]['username'] : 'NA';
        return $userMaj;
    }
    
        public function GetUserCreUserfullname($entity){
        $em = $this->entityManager;
        $userCreId = ($entity->getUserCre() !== null) ? $entity->getUserCre() : 0;
        $query = $em->createQuery('SELECT user.name FROM App:Core\\User user WHERE user.id = '.$userCreId.'')->getResult();
        $userCre = (count($query) > 0) ? $query[0]['name'] : 'NA';
        return $userCre;
    }
 
    public function GetUserMajUserfullname($entity){
        $em = $this->entityManager;
        $userMajId = ($entity->getUserMaj() !== null) ? $entity->getUserMaj() : 0;
        $query = $em->createQuery('SELECT user.name FROM App:Core\\User user WHERE user.id = '.$userMajId.'')->getResult();
        $userMaj = (count($query) > 0) ? $query[0]['name'] : 'NA';
        return $userMaj;
    }
    
    public function SetArrayCollection($nameArrayCollection, $entity){
        $method = 'get'.ucfirst($nameArrayCollection);
        // memorize ArrayCollection EstFinancePar
        $originalArrayCollection = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
        	$originalArrayCollection->add($entityCollection);
        }
        return $originalArrayCollection;
    }

    public function DelArrayCollection($nameArrayCollection, $entity, $originalArrayCollection ){
        $method = 'get'.ucfirst($nameArrayCollection);
        $em = $this->entityManager;  
            // delete ArrayCollections
            foreach ($entity->$method() as $entityCollection) {
                foreach ($originalArrayCollection as $key => $toDel) {
                    if ($toDel === $entityCollection) {
                        unset($originalArrayCollection[$key]);
                    }
                }
            }
            // remove the relationship 
            foreach ($originalArrayCollection as $entityCollection) {
                 $em->remove($entityCollection);
            }
        return true;
    }

    public function SetArrayCollectionEmbed($nameArrayCollection, $nameArrayCollectionEmbed, $entity){
        $method = 'get'.ucfirst($nameArrayCollection);
        $methodEmbed = 'get'.ucfirst($nameArrayCollectionEmbed);
        $listOriginalArrayCollection = [];
        // memorize ArrayCollection EstFinancePar
        $originalArrayCollection = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
        	$originalArrayCollection->add($entityCollection);
        }
        $listOriginalArrayCollection[$nameArrayCollection] = $originalArrayCollection;
        // 
        $originalArrayCollectionEmbed = new ArrayCollection();
        foreach ($entity->$method() as $entityCollection) {
	        foreach ($entityCollection->$methodEmbed() as $entityCollectionEmbed) {
	        	$originalArrayCollectionEmbed->add($entityCollectionEmbed);	        	
	        }
        }
        $listOriginalArrayCollection[$nameArrayCollectionEmbed] = $originalArrayCollectionEmbed;
        return $listOriginalArrayCollection;
    }

    public function DelArrayCollectionEmbed($nameArrayCollection, $nameArrayCollectionEmbed, $entity, $listOriginalArrayCollection ){
        // 
        $method = 'get'.ucfirst($nameArrayCollection);
        $methodEmbed = 'get'.ucfirst($nameArrayCollectionEmbed);
        $originalArrayCollection = $listOriginalArrayCollection[$nameArrayCollection];
        $originalArrayCollectionEmbed = $listOriginalArrayCollection[$nameArrayCollectionEmbed];
        $em = $this->entityManager;         
        // delete ArrayCollectionsEmbed
        foreach ($entity->$method() as $entityCollection) {
            foreach ($entityCollection->$methodEmbed() as $entityCollectionEmbed) {
                foreach ($originalArrayCollectionEmbed as $key => $toDel) {
                    if ($toDel === $entityCollectionEmbed) {
                        unset($originalArrayCollectionEmbed[$key]);
                    }
                }
            }    
        }
        foreach ($originalArrayCollectionEmbed as $entityCollectionEmbed) {
             $em->remove($entityCollectionEmbed);
        }
        // delete ArrayCollections
        foreach ($entity->$method() as $entityCollection) {
            foreach ($originalArrayCollection as $key => $toDel) {
                if ($toDel === $entityCollection) {
                    unset($originalArrayCollection[$key]);
                }
            }
        }
        foreach ($originalArrayCollection as $entityCollection) {
             $em->remove($entityCollection);
        }        
            
        return true;
    }
    
}
