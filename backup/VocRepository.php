<?php

namespace App\Repository\Core;

use App\Entity\Voc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Core\GenericFunction;

/**
 * INFO VAR 
 * repository_full_class_name :  App\Repository\Core\VocRepository   *
 * repository_class_name :  VocRepository  *
 * repository_var :  vocRepository   *
 * entity_var_singular :  voc   *
 * entity_class_name :  Voc   * 
 */

/**
 * @method Adress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adress[]    findAll()
 * @method Adress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class VocRepository extends ServiceEntityRepository 
{
// InBORe Repository generation

    const NAME_FIELD_SEARCH_BOOTGRID = 'vocabulary_title';
    const NAME_FIELD_RESULTS_TYPEAHEAD = 'libelle';
    const MAX_RESULTS_TYPEAHEAD = 20;
    const NAME_MAIN_LINKED_TABLE = '';
    const NAME_FK_TO_MAIN_LINKED_TABLE = ''; // ex.  NAME_MAIN_LINKED_TABLE_fk
    const NAME_MAIN_AGGREGATE_TABLE = '';
    const NAME_FK_TO_MAIN_AGGREGATE_TABLE = ''; // ex. voc_fk
    const LIST_OF_COLLECTIONS_EMBEDED = ['']; // ex. array('Name_of_array_collection1, ...')
    const LIST_OF_COLLECTIONS_EMBEDED_EMBEDED = ['']; // ex. array('Name_of_array_collection1' => 'Name_of_array_collection1_embeded', ...')

    public function __construct(ManagerRegistry $registry, GenericFunction $service)
    {
        $this->service = $service;
        parent::__construct($registry, Voc::class); 
    }  
    
 
    public function findSearch($orderBy, $searchPhrase, $idFk = null, $nameFk= null, $ReturnColumNameOnly = 0) 
    {
       // List of the fields to show ['nameEntity.id' =>'id','nameEntity.field1' =>'nameEntity_field1' .... ] 
       $date_user_fields = [];       
       /** // define the technical fields date-cre, date_maj, user_cre, user_maj
       $date_user_fields = [ 'voc.date_cre' =>'date_cre','voc.date_maj' =>'date_maj',
            'voc.user_cre' =>'user_cre','voc.user_maj' =>'user_maj'];
       */
       // define the array of fields to show in the list of records id, ... Only the first 10 are visible 
       $fields_toshow = ['voc.id' =>'id', 'voc.code' =>'code', 'voc.vocabulary_title' => 'libelle', 'voc.parent' =>'parent',];
       // merge all fields to display
       $fields_toshow = array_merge($fields_toshow, $date_user_fields);
       
       $aggregate_fields_toshow = [];
       $leftJoin = '';
       /** // List of agreggate fields to show : use this syntax to get a link in the field list :
       $aggregate_fields_toshow = ["cast( max(CASE WHEN ".self::NAME_MAIN_AGGREGATE_TABLE.".".self::NAME_FK_TO_MAIN_AGGREGATE_TABLE." IS NOT NULL THEN voc.id ELSE NULL END) as character varying)" => "link_".self::NAME_MAIN_AGGREGATE_TABLE.""];       
       // with the left join  section LIKE :
       $leftJoin = " LEFT JOIN ".self::NAME_MAIN_AGGREGATE_TABLE." ON ".self::NAME_MAIN_AGGREGATE_TABLE.".".self::NAME_FK_TO_MAIN_AGGREGATE_TABLE." = voc.id " ;
       */
       
       // build SELECT with the list of fields AND aggregate fields
       $select = 'SELECT ';
       foreach ($fields_toshow as $key => $val) {
              $select .=  $key.' AS '. $val . ',';
       }
       if (count($aggregate_fields_toshow)>0){
            foreach ($aggregate_fields_toshow as $key => $val) {
                   $select .=  $key.' AS '. $val . ',';
            }           
       } 
       $select = substr($select, 0, -1);
                
       // build the WHERE section 
       $where = " WHERE LOWER(voc.".self::NAME_FIELD_SEARCH_BOOTGRID.") LIKE '". strtolower($searchPhrase) . "%'";
       // CASE where idFk and nameFk are defined
        if ($idFk != null && $nameFk != null) {
            if ($nameFk == 'core'.ucfirst(self::NAME_MAIN_LINKED_TABLE).'Fk')  $where .= " AND voc.".self::NAME_FK_TO_MAIN_LINKED_TABLE." = '". $idFk. "'"  ;
        }
             
       /** // Add  LEFT JOIN section if needed : 
       $leftJoin .= ' LEFT JOIN nameEntityRel ON voc.nameEntityRel_fk = nameEntityRel.id ' ;
       */
       
       // build the GROUP BY section if aggregate fiels is defined
       $groupBy = '';
       if (count($aggregate_fields_toshow)>0){
            $groupBy = ' GROUP BY ';
            foreach ($fields_toshow as $key => $val) {
                   $groupBy .=  $key.',';
            }
            $groupBy = substr($groupBy, 0, -1);
        }
       
       // build the end SQL request to execute
       $rawSql = $select." FROM voc ". $leftJoin ." ". $where ." ". $groupBy." ORDER BY ". $orderBy;

       // execute query and fill tab to show in the bootgrid list (see index.htm)
       $em = $this->getEntityManager();
       $stmt = $em->getConnection()->prepare($rawSql);
       $stmt->execute();      
       if ($ReturnColumNameOnly){
            $list_record = $stmt->fetchAssociative();  
            $fields_toshow = [];
            if (count($list_record) > 0) {
                 foreach ($list_record as $key => $val) {
                         $fields_toshow[] = $key;
                 }
            }
        } else {
            $list_record = $stmt->fetchAll();       
            if (count($list_record) > 0) {
                $fields_toshow = $list_record;
            }            
        }
       
       return $fields_toshow ;
    }
    
    
    /**
      * @return Voc[] Returns an array of Adress objects
     */    
    public function findSearchAction($q)
    {
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $qb = $this->createQueryBuilder('voc');        
            $qb->select('voc.id, voc.'.self::NAME_FIELD_RESULTS_TYPEAHEAD.' as code');
            for ($i = 0; $i < count($query); $i++) {
                $qb->andWhere('(LOWER(voc.'.self::NAME_FIELD_RESULTS_TYPEAHEAD.') like :q' . $i . ')');
                $qb->setParameter('q' . $i, $query[$i] . '%');
            }
            $qb->orderBy('code', 'ASC');
            $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);

        return $qb->getQuery()->getResult() ;
    }
    
    /**
      *  Set the ArrayCollections and fill the edit_form_parameters array 
      *  Returns an array of parameters for the edit form
     */      
    public function editActionBeforeFormIsSubmitted(Request $request, Voc $voc)
    {
        /** // set the embed_form here if needed , case of linked-embeded form: 
        $embeded_form = $this->createForm('App\Form\Type\XXXType',["action_type" => Action::show()]);
        */        
        // set the array of parameters for the edit formType view
        $edit_form_parameters = array('form_parameters' => ['action_type' => Action::edit(),],
                                      'embeded_form' => (isset($embeded_form)) ? $embeded_form : NULL,
                                    );        
        // Set the array(s) collection(s) of the Voc entity before the handleRequest action (before the form is submited)
        $this->list_of_collection_embed = [];
        foreach (self::LIST_OF_COLLECTIONS_EMBEDED as $name_of_array_collection) {
            $this->list_of_collection_embed [$name_of_array_collection] = $this->service->setArrayCollection($name_of_array_collection, $voc);
        }
        $this->list_of_collection_embed_embed = [];
        foreach (self::LIST_OF_COLLECTIONS_EMBEDED_EMBEDED as $name_of_array_collection => $name_of_array_collection_embed ) {
            $this->list_of_collection_embed [$name_of_array_collection] = $this->service->setArrayCollectionEmbed($name_of_array_collection, $name_of_array_collection_embed, $voc);
        }
        //       
        return $edit_form_parameters ;
    }
    
    /**
      *  Delete the ArrayCollections
      *  Returns an array of parameters for the edit form
     */ 
     public function editActionAfterFormIsSubmitted(Request $request, Voc $voc)
    {
        // Del the array(s) collection(s) of the Contact entity after the handleRequest and the form ois submited and  validated 
        foreach (self::LIST_OF_COLLECTIONS_EMBEDED as $name_of_array_collection) {
           $this->service->DelArrayCollection($name_of_array_collection, $voc, $this->list_of_collection_embed [$name_of_array_collection]);
        }
        foreach (self::LIST_OF_COLLECTIONS_EMBEDED_EMBEDED as $name_of_array_collection => $name_of_array_collection_embed) {
           $this->service->DelArrayCollectionEmbed($name_of_array_collection, $name_of_array_collection_embed, $voc, $this->list_of_collection_embed_embed [$name_of_array_collection]);
        }
    }    

    
    // /**
    //  * @return Voc[] Returns an array of Adress objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voc    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
