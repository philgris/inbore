<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use App\Services\FileUploader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Core\GenericFunction;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * INFO VAR 
 * repository_full_class_name : <?php if (isset($repository_full_class_name)): ?> <?= $repository_full_class_name ?>  <?php endif ?>
 *
 * repository_class_name : <?php if (isset($repository_class_name)): ?> <?= $repository_class_name ?> <?php endif ?>
 *
 * repository_var : <?php if (isset($repository_var)): ?> <?= $repository_var ?>  <?php endif ?>
 *
 * entity_var_singular : <?php if (isset($entity_var_singular)): ?> <?= $entity_var_singular ?>  <?php endif ?>
 *
 * entity_class_name : <?php if (isset($entity_class_name)): ?> <?= $entity_class_name ?>  <?php endif ?>
 * 
 */

/**
 * 
 */

class <?= $repository_class_name ?> extends ServiceEntityRepository <?= "\n" ?>
{
// InBORe Repository generation	
    // Search Column in list : ex1 <?= lcfirst($entity_class_name) ?>.nameOfField ; ex2 CONCAT(<?= lcfirst($entity_class_name) ?>.nameOfField,\':\', entityRel.nameOfField)'
    const BOOTGRID_SEARCH_COLUMN = 'name_or_expression_of_the_bootgrid_search_column'; 
    // Name (Database) of the  Autocomplete Field for Select2 : ex1  '<?= lcfirst($entity_class_name) ?>.id'; ex2 <?= lcfirst($entity_class_name) ?>.nameOfField ; ex2 CONCAT(<?= lcfirst($entity_class_name) ?>.nameOfField,\':\', entityRel.nameOfField)'
    const DBNAME_FIELD_TO_AUTOCOMPLETE = 'name_of_select2_autocomplete_field';
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
        parent::__construct($registry, <?= $entity_class_name ?>::class);
    }
	
	public function findSearch($ReturnColumNameOnly = 0)
    {
        $fields = [
            // id
            '<?= lcfirst($entity_class_name) ?>.id'                                    => 'id',
			
            // bootgrid column to search 
           self::BOOTGRID_SEARCH_COLUMN             => '<?= lcfirst($entity_class_name) ?>_bootgrid_search_column',
						
            // specific

            // aggregation

            // technical
            'CONCAT(\'\',<?= lcfirst($entity_class_name) ?>.dateMaj,\'\')'             => 'date_maj',
            'CONCAT(\'\',<?= lcfirst($entity_class_name) ?>.dateCre,\'\')'             => 'date_cre',
            'userMaj.name'             => 'user_maj',
            'userCre.name'             => 'user_cre',
            '<?= lcfirst($entity_class_name) ?>.userCre'                               => 'user_cre_id',
            
                        
            // links declarations (1)
            
        ];
		
        // orderBy : default
        $orderByDefault =  [
            'CONCAT(\'\',<?= lcfirst($entity_class_name) ?>.dateMaj,\'\')'   => 'DESC',
            '<?= lcfirst($entity_class_name) ?>.id'                         => 'DESC'
        ];

        $qb = $this->createQueryBuilder('<?= lcfirst($entity_class_name) ?>');		

        // join entities
        /** 
        $qb
            ->leftJoin('<?= lcfirst($entity_class_name) ?>.idEntityRel', 'entityrel')
        ;
        */
	
        // join aggregation : links (2)
        
        // reduction
        $qb
            ->addGroupBy('<?= lcfirst($entity_class_name) ?>.id')
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
        $qb->leftJoin('App:Core\\User', 'userCre', 'WITH', '<?= lcfirst($entity_class_name) ?>.userCre = userCre.id')
            ->leftJoin('App:Core\\User', 'userMaj', 'WITH', '<?= lcfirst($entity_class_name) ?>.userMaj = userMaj.id')
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
            $qb->andWhere('<?= lcfirst($entity_class_name) ?>.'.$nameFk.' = :'.$nameFk)
                ->setParameter($nameFk, $this->request->get('idFk'))
            ;
        }

        // count all records for the current search
        $total = count(
            $qb
                ->select('COUNT(<?= lcfirst($entity_class_name) ?>.id)')
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
      *  Returns an array of Adress objects
     */    
    public function findSearchAction($q)
    {
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $qb = $this->createQueryBuilder('<?= lcfirst($entity_class_name) ?>');        
            $qb->select('<?= lcfirst($entity_class_name) ?>.id, '.self::DBNAME_FIELD_TO_AUTOCOMPLETE.' as code');
            // add Join if necessary : ex. $qb->leftJoin('<?= lcfirst($entity_class_name) ?>.entityRelFk', 'EntityRel');
            for ($i = 0; $i < count($query); $i++) {
                $qb->andWhere('(LOWER('.self::DBNAME_FIELD_TO_AUTOCOMPLETE.') like :q' . $i . ')');
                $qb->setParameter('q' . $i, '%' . $query[$i] . '%');
            }
            $qb->orderBy('code', 'ASC');
            $qb->setMaxResults(self::MAX_RESULTS_TO_AUTOCOMPLETE);

        return $qb->getQuery()->getResult() ;
    }
    
    
    public function save(<?= $entity_class_name ?> $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    public function remove(<?= $entity_class_name ?> $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }    
    
	
}