<?php

namespace App\Repository\Core;

use App\Entity\Media;
use App\Services\FileUploader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Core\GenericFunction;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @method Adress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adress[]    findAll()
 * @method Adress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class MediaRepository extends ServiceEntityRepository 
{
// InBORe Repository generation	
    // Search Column in list : ex1 media.nameOfField ; ex2 CONCAT(media.nameOfField,\':\', entityRel.nameOfField)'
    const BOOTGRID_SEARCH_COLUMN = 'filename'; 
    // Name (Database) of the  Autocomplete Field for Select2 
    const DBNAME_FIELD_TO_AUTOCOMPLETE = 'filename';
	// Max number of result to show in the auto-complete list (default = 20)
	const MAX_RESULTS_TO_AUTOCOMPLETE = 20;
	
    private $service;
    private $fileUploader;
    private $request;

    public function __construct(ManagerRegistry $registry, GenericFunction $service, FileUploader $fileUploader, RequestStack $requestStack)
    {
        $this->service = $service;
        $this->fileUploader = $fileUploader;
        $this->request = $requestStack->getCurrentRequest();
        parent::__construct($registry, Media::class);
    }
	
	public function findSearch($ReturnColumNameOnly = 0)
    {
        $fields = [
            // id

			
            // bootgrid column to search 
           self::BOOTGRID_SEARCH_COLUMN             => 'id',		   
						
            // specific 
            'media.filename'                                    => 'filename',
            'media.name'                                    => 'name',
			
            // aggregation

            // technical
            'CONCAT(\'\',media.dateMaj,\'\')'             => 'date_maj',
            'CONCAT(\'\',media.dateCre,\'\')'             => 'date_cre',
            'userMaj.name'             => 'user_maj',
            'userCre.name'             => 'user_cre',
            'media.userCre'                               => 'user_cre_id',
            
                        
            // links
            
        ];
		
        // orderBy : default
        $orderByDefault =  [
            'CONCAT(\'\',media.dateMaj,\'\')'   => 'DESC',
            'media.id'                         => 'DESC'
        ];

        $qb = $this->createQueryBuilder('media');		

        // join entities
        /** 
	$qb
            ->leftJoin('media.idEntityRel', 'entityrel')
        ;
	*/
		
	// reduction
	$qb
            ->addGroupBy('media.id')
            ->addGroupBy('userMaj.id')
            ->addGroupBy('userCre.id')
        ;
		
	// GENERIC CODE  - DONT CHANGE THE CODE UNDER THIS LINE  //
		
	$aliases = array_values($fields);

        if ($ReturnColumNameOnly==1) {
            if (false!==$index=array_search('user_cre_id', $aliases)) {
                unset($aliases[$index]);
            }
            return $aliases;
        }

        // join technical fields
        $qb->leftJoin('App:Core\\User', 'userCre', 'WITH', 'media.userCre = userCre.id')
            ->leftJoin('App:Core\\User', 'userMaj', 'WITH', 'media.userMaj = userMaj.id')
        ;

        // query
        $searchPhrase = $this->request->get('searchPhrase');
        if ($this->request->get('searchPattern') && !$searchPhrase) {
            $searchPhrase = $this->request->get('searchPattern');
        }
        if (strlen($searchPhrase)>0) {
            $qb->where('LOWER('. self::BOOTGRID_SEARCH_COLUMN .') LIKE :search')
                ->setParameter('search', strtolower($searchPhrase).'%')
            ;
        }

        // foreign key
        if ($this->request->get('idFk') && filter_var($this->request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
            $nameFk = lcfirst($this->service->getFkName(substr($this->request->get('nameFk'), 4, -2)));
            $qb->andWhere('media.'.$nameFk.' = :'.$nameFk)
                ->setParameter($nameFk, $this->request->get('idFk'))
            ;
        }

        // count all records for the current search
        $total = count(
            $qb
                ->select('COUNT(media.id)')
                ->getQuery()
                ->getScalarResult()
        );

        // select
        $select = [];
        foreach ($fields as $field => $alias) {
            $select[] = $field.' '.$alias;
        }
        $select = implode(', ', $select);

        // limit
        $rowCount = $this->request->get('rowCount') ?: 10;
        $minRecord = intval($this->request->get('current') - 1) * $rowCount;
        $qb->select($select)
            ->setFirstResult($minRecord)
            ->setMaxResults($rowCount)
        ;

        // sort
        $orderBy = $this->request->get('sort');
        if (empty($orderBy)) {
            $orderBy = $orderByDefault;
        } else {
            $orderBy = [];
            foreach ($this->request->get('sort') as $sort => $order) {
                if (in_array($sort, $aliases)) {
                    $orderBy[$sort] = $order;
                }
            }
        }
        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        // records
        $rows = $qb->getQuery()->getResult();

        // json
        $json = [
            'current'       => intval($this->request->get('current')),
            'rowCount'      => $rowCount,
            'rows'          => $rows,
            'searchPhrase'  => $searchPhrase,
            'total'         => $total
        ];

        return $json;
    }

    /**
      * @return Media[] Returns an array of Adress objects
     */    
    public function findSearchAction($q)
    {
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $qb = $this->createQueryBuilder('media');        
            $qb->select('media.id, media.'.self::DBNAME_FIELD_TO_AUTOCOMPLETE.' as code');
            for ($i = 0; $i < count($query); $i++) {
                $qb->andWhere('(LOWER(media.'.self::DBNAME_FIELD_TO_AUTOCOMPLETE.') like :q' . $i . ')');
                $qb->setParameter('q' . $i, $query[$i] . '%');
            }
            $qb->orderBy('code', 'ASC');
            $qb->setMaxResults(self::MAX_RESULTS_TO_AUTOCOMPLETE);

        return $qb->getQuery()->getResult() ;
    }
	
}