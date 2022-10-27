<?php

namespace App\Repository\Core;

use App\Entity\Protocol;
use App\Services\FileUploader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Core\GenericFunction;

/**
 * INFO VAR 
 * repository_full_class_name :  App\Repository\Core\ProtocolRepository   *
 * repository_class_name :  ProtocolRepository  *
 * repository_var :  protocolRepository   *
 * entity_var_singular :  protocol   *
 * entity_class_name :  Protocol   * 
 */

/**
 * @method Adress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adress[]    findAll()
 * @method Adress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class AdminRepository extends ServiceEntityRepository
{
// InBORe Repository generation

    const MAX_RESULTS_TYPEAHEAD = 20;

//    const NAME_FIELD_SEARCH_BOOTGRID = 'code';
//    const NAME_FIELD_RESULTS_TYPEAHEAD = 'code';
//    const MAX_RESULTS_TYPEAHEAD = 20;

//    const NAME_MAIN_LINKED_TABLE = '';
//    const NAME_FK_TO_MAIN_LINKED_TABLE = ''; // ex.  NAME_MAIN_LINKED_TABLE_fk
//    const NAME_MAIN_AGGREGATE_TABLE = '';
//    const NAME_FK_TO_MAIN_AGGREGATE_TABLE = ''; // ex. protocol_fk
//    const LIST_OF_COLLECTIONS_EMBEDED = ['medialinks']; // array('Name_of_array_collection1','Name_of_array_collection2' ...)
//    const LIST_OF_COLLECTIONS_EMBEDED_EMBEDED = []; // ex. array('Name_of_array_collection1' => 'Name_of_array_collection1_embeded', ...')

    private $service;
    private $fileUploader;
    private $config;

    public function __construct(ManagerRegistry $registry, GenericFunction $service, FileUploader $fileUploader, ParameterBagInterface $config)
    {
        $this->service = $service;
        $this->fileUploader = $fileUploader;
        $this->config = $config->get('admin');
        parent::__construct($registry, Protocol::class);
    }


    //@TODO dql refactoring
    public function findSearch($entity, $orderBy, $searchPhrase, $idFk = null, $nameFk = null, $ReturnColumNameOnly = 0)
    {
        if (isset($this->config[$entity]['bootgrid']['select'])) {
            $sql = $this->config[$entity]['bootgrid']['select'];
            if (!preg_match('/\s*INSERT|DELETE|UPDATE\s+/ims', $sql, $m)) {
                if (preg_match('/\s*SELECT\s+(.*)\s+FROM\s+/ims', $sql, $selectFrom)) {
                    if (preg_match_all('/ AS ([a-z0-9_]+)\s*[\,\r\n]/i', $selectFrom[0], $asField)) {
                        if ($ReturnColumNameOnly) {
                            return $asField[1];
                        }
                        $rsm = new ResultSetMapping();
                        foreach ($asField[1] as $field) {
                            $rsm->addScalarResult($field, $field);
                        }

                        $where = [];
                        if ($searchPhrase != null && $searchPhrase != '' && isset($this->config[$entity]['bootgrid']['search'])) {
                            $where[] = "LOWER(cast( ".$this->config[$entity]['bootgrid']['search']." as character varying)) LIKE '". strtolower($searchPhrase) . "%'";
                        }

                        if ($nameFk && $idFk) {
                            $where[] = $entity.'.'.$nameFk.'='.$idFk;
                        }

                        if (count($where)) {
                            if (preg_match('/(.*)\s+(GROUP\s+BY\s+.*)$/ims', $sql, $matches)) {
                                $sql = $matches[1]."\n".'WHERE '.implode(' AND ', $where)."\n".$matches[2];
                            } else {
                                $sql .= "\n".'WHERE '.implode(' AND ', $where);
                            }
                        }

                        $order = [];
                        if ($orderBy != '') {
                            $order[] = $orderBy;
                        } else {
                            if (isset($this->config[$entity]['bootgrid']['order'])) {
                                if (preg_match('/[a-z0-9_\.]+\s+asc|desc/i', $this->config[$entity]['bootgrid']['order'], $m)) {
                                    $order[] = $this->config[$entity]['bootgrid']['order'];
                                }
                            }
                            $order[] = 'date_maj DESC';
                            $order[] = $asField[1][0].' DESC';
                        }
                        if (count($order)) {
                            $sql .= "\n".'ORDER BY '.implode(', ', $order);
                        }

                        $em = $this->getEntityManager();
                        $query = $em->createNativeQuery($sql, $rsm);
                        $result = $query->getArrayResult();
                        return $result;
                    }
                }
            }
        }
        return [];
    }

    public function findSearchAction($entity, $q)
    {
        if (!isset($this->config[$entity]['bootgrid']['search'])) {
            return [];
        }
        $field = $this->config[$entity]['bootgrid']['search'];
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $qb = $this->createQueryBuilder($entity);
            $qb->select($entity.'.id, '.$entity.'.'.$field.' as '.$field);
            for ($i = 0; $i < count($query); $i++) {
                $qb->andWhere('(LOWER('.$entity.'.'.$field.') like :q' . $i . ')');
                $qb->setParameter('q' . $i, $query[$i] . '%');
            }
            $qb->orderBy($field, 'ASC');
            $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);

        return $qb->getQuery()->getResult() ;
    }
//    public function findSearchAction($entity, $q)
//    {
//        if (!isset($this->config[$entity]['typeahead']['field'])) {
//            return [];
//        }
//        $field = $this->config[$entity]['typeahead']['field'];
//        $max = isset($this->config[$entity]['typeahead']['results']) ? $this->config[$entity]['typeahead']['results'] : self::MAX_RESULTS_TYPEAHEAD;
//        $query = explode(' ', strtolower(trim(urldecode($q))));
//        $qb = $this->createQueryBuilder($entity);
//            $qb->select($entity.'.id, '.$entity.'.'.$field.' as '.$field);
//            for ($i = 0; $i < count($query); $i++) {
//                $qb->andWhere('(LOWER('.$entity.'.'.$field.') like :q' . $i . ')');
//                $qb->setParameter('q' . $i, $query[$i] . '%');
//            }
//            $qb->orderBy($field, 'ASC');
//            $qb->setMaxResults($max);
//
//        return $qb->getQuery()->getResult() ;
//    }

}
