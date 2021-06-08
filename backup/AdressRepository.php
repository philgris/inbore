<?php

namespace App\Repository\Core;

use App\Entity\Adress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;

/**
 * INFO VAR 
 * repository_full_class_name :  App\Repository\Core\AdressRepository   *
 * repository_class_name :  AdressRepository  *
 * repository_var :  adressRepository   *
 * entity_var_singular :  adress   *
 * entity_class_name :  Adress   * 
 */

/**
 * @method Adress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adress[]    findAll()
 * @method Adress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class AdressRepository extends ServiceEntityRepository 
{
// InBORe Repository generation

    const NAME_FIELD_SEARCH_BOOTGRID = 'name';
    const NAME_FIELD_RESULTS_TYPEAHEAD = 'name';
    const MAX_RESULTS_TYPEAHEAD = 20;
    const NAME_MAIN_LINKED_TABLE = '';
    const NAME_FK_TO_LINKED_TABLE = ''; // ex.  NAME_MAIN_LINKED_TABLE_fk
    const NAME_MAIN_AGGREGATE_TABLE = 'contact';
    const NAME_FK_TO_MAIN_AGGREGATE_TABLE = 'adress_fk'; // ex. adress_fk

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adress::class); 
    }    
 
    public function findSearch($orderBy, $searchPhrase, $idFk = null, $nameFk= null, $ReturnColumNameOnly = 0) 
    {
       // List of the fields to show ['nameEntity.id' =>'id','nameEntity.field1' =>'nameEntity_field1' .... ] 
       $date_user_fields = [];       
       // define the technical fields date-cre, date_maj, user_cre, user_maj
       $date_user_fields = [ 'adress.date_user_cre' =>'date_cre','adress.date_user_maj' =>'date_maj',
            'adress.user_cre' =>'user_cre','adress.user_maj' =>'user_maj'];
       
       // define the array of fields to show in the list of records id, ... Only the first 10 are visible 
       $fields_toshow = ['adress.id' =>'id', 'adress.name' =>'adress_name',];
       // merge all fields to display
       $fields_toshow = array_merge($fields_toshow, $date_user_fields);
       
       $aggregate_fields_toshow = [];
       $leftJoin = '';
       /** // List of agreggate fields to show : use this syntax to get a link in the field list :
       $aggregate_fields_toshow = ["cast( max(CASE WHEN ".self::NAME_MAIN_AGGREGATE_TABLE.".".self::NAME_FK_TO_MAIN_AGGREGATE_TABLE." IS NOT NULL THEN adress.id ELSE NULL END) as character varying)" => "link_".self::NAME_MAIN_AGGREGATE_TABLE.""];       
       // with the left join  section LIKE :
       $leftJoin = " LEFT JOIN ".self::NAME_MAIN_AGGREGATE_TABLE." ON ".self::NAME_MAIN_AGGREGATE_TABLE.".".self::NAME_FK_TO_MAIN_AGGREGATE_TABLE." = adress.id " ;
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
       $where = " WHERE LOWER(adress.".self::NAME_FIELD_SEARCH_BOOTGRID.") LIKE '". strtolower($searchPhrase) . "%'";
       // CASE where idFk and nameFk are defined
        if ($idFk != null && $nameFk != null) {
            if ($nameFk == 'core'.ucfirst(self::NAME_MAIN_LINKED_TABLE).'Fk')  $where .= " AND adress.".self::NAME_FK_TO_LINKED_TABLE." = '". $idFk. "'"  ;
        }
       
       
       /** // Add  LEFT JOIN section if needed : 
       $leftJoin .= ' LEFT JOIN contact ON adress.nameEntityRel_fk = nameEntityRel.id ' ;
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
       $rawSql = $select." FROM adress ". $leftJoin ." ". $where ." ". $groupBy." ORDER BY ". $orderBy;

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
      * @return Adress[] Returns an array of Adress objects
     */    
    public function findSearchAction($q)
    {
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $qb = $this->createQueryBuilder('adress');        
            $qb->select('adress.id, adress.'.self::NAME_FIELD_RESULTS_TYPEAHEAD.' as code');
            for ($i = 0; $i < count($query); $i++) {
                $qb->andWhere('(LOWER(adress.'.self::NAME_FIELD_RESULTS_TYPEAHEAD.') like :q' . $i . ')');
                $qb->setParameter('q' . $i, $query[$i] . '%');
            }
            $qb->orderBy('code', 'ASC');
            $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);

        return $qb->getQuery()->getResult() ;
    }

    
    // /**
    //  * @return Adress[] Returns an array of Adress objects
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
    public function findOneBySomeField($value): ?Adress    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
