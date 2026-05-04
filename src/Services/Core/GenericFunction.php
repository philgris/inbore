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
use Doctrine\ORM\QueryBuilder;


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

    //  escapeLike(string $value): string    
    public function escapeLike(string $value): string
    {
        return str_replace(
            ['\\',  '_' ],
            ['\\\\',  '\\_'],
            $value
        );
    }

    public function parseIntegerFilter($value)
    {
        $value = trim($value);

        // Cas 1 : Intervalle (ex: "10-20" ou "-10--20")
        if (preg_match('/^([-+]?\d+)\:([-+]?\d+)$/', $value, $matches)) {
            return [
                'type' => 'range',
                'min' => (int)$matches[1],
                'max' => (int)$matches[2],
            ];
        }
        // Cas 2 : Liste de valeurs (ex: "10;20;30" ou "-10;-20;30")
        elseif (strpos($value, ';') !== false) {
            $values = array_map('intval', explode(';', $value));
            return [
                'type' => 'in',
                'values' => $values,
            ];
        }
        // Cas 3 : Supérieur à (ex: ">10" ou ">-10")
        elseif (preg_match('/^\>([-+]?\d+)$/', $value, $matches)) {
            return [
                'type' => 'greater_than',
                'value' => (int)$matches[1],
            ];
        }
        // Cas 4 : Inférieur à (ex: "<10" ou "<-10")
        elseif (preg_match('/^\<([-+]?\d+)$/', $value, $matches)) {
            return [
                'type' => 'lower_than',
                'value' => (int)$matches[1],
            ];
        }
        // Cas par défaut : Égalité (ex: "-10")
        else {
            return [
                'type' => 'equals',
                'value' => (int)$value,
            ];
        }
    }    

    public function parseFloatFilter($value)
    {
        $value = trim($value);

        // Cas 1 : Intervalle (ex: "10.5-20.8" ou "-10.5--20.8")
        if (preg_match('/^([-+]?[\d\.]+)\:([-+]?[\d\.]+)$/', $value, $matches)) {
            return [
                'type' => 'range',
                'min' => (float)$matches[1],
                'max' => (float)$matches[2],
            ];
        }
        // Cas 2 : Liste de valeurs (ex: "10.5;20.8;30.2" ou "-10.5;-20.8;30.2")
        elseif (strpos($value, ';') !== false) {
            $values = array_map('floatval', explode(';', $value));
            return [
                'type' => 'in',
                'values' => $values,
            ];
        }
        // Cas 3 : Supérieur à (ex: ">10.5" ou ">-10.5")
        elseif (preg_match('/^\>([-+]?[\d\.]+)$/', $value, $matches)) {
            return [
                'type' => 'greater_than',
                'value' => (float)$matches[1],
            ];
        }
        // Cas 4 : Inférieur à (ex: "<10.5" ou "<-10.5")
        elseif (preg_match('/^\<([-+]?[\d\.]+)$/', $value, $matches)) {
            return [
                'type' => 'lower_than',
                'value' => (float)$matches[1],
            ];
        }
        // Cas par défaut : Égalité (ex: "-10.5")
        else {
            return [
                'type' => 'equals',
                'value' => (float)$value,
            ];
        }
    }

public function parseDateFilter(string $value): array
{
    $value = trim($value);

    // --- 1. Intervalle : YYYY[-MM[-DD]]:YYYY[-MM[-DD]]
    if (preg_match('/^(\d{4}(?:-\d{2}(?:-\d{2})?)?):(\d{4}(?:-\d{2}(?:-\d{2})?)?)$/', $value, $matches)) {
        $start = $matches[1];
        $end = $matches[2];

        // Compléter les dates partielles
        $startDate = $this->parsePartialDate($start, 'start');
        $endDate = $this->parsePartialDate($end, 'end');

        return [
            'type' => 'date_range',
            'start' => $startDate->format('Y-m-d'), // Format chaîne
            'end' => $endDate->format('Y-m-d'),
        ];
    }

    // --- 2. Comparateurs : >YYYY, <YYYY-MM-DD, >=YYYY-MM, <=YYYY
    if (preg_match('/^(>=|<=|>|<)\s*(\d{4}(?:-\d{2}(?:-\d{2})?)?)$/', $value, $matches)) {
        $operator = $matches[1];
        $dateStr = $matches[2];

        // Compléter les dates partielles
        $date = $this->parsePartialDate($dateStr, 'exact');

        return [
            'type' => 'comparison',
            'operator' => $operator,
            'date' => $date, // Retourne un objet DateTime
        ];
    }

    // --- 3. Année seule (ex: 2025)
    if (preg_match('/^\d{4}$/', $value)) {
        $start = new \DateTime($value . '-01-01');
        $end = new \DateTime($value . '-12-31');
        return [
            'type' => 'date_range',
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];
    }

    // --- 4. Année et mois (ex: 2025-01)
    if (preg_match('/^\d{4}-\d{2}$/', $value)) {
        $start = new \DateTime($value . '-01');
        $end = clone $start;
        $end->modify('last day of this month');
        return [
            'type' => 'date_range',
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];
    }

    // --- 5. Date exacte (ex: 2025-01-14)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return [
            'type' => 'exact_date',
            'value' => $value, // Format chaîne
        ];
    }

    // --- 6. Par défaut : tenter de parser en DateTime
    try {
        $date = new \DateTime($value);
        return [
            'type' => 'exact_date',
            'value' => $date->format('Y-m-d'), // Format chaîne
        ];
    } catch (\Exception $e) {
        throw new \InvalidArgumentException("Date filter invalid: $value");
    }
}

// Méthode auxiliaire pour compléter les dates partielles
private function parsePartialDate(string $dateStr, string $context): \DateTime
{
    if (strlen($dateStr) === 4) {
        $date = new \DateTime($dateStr . '-01-01');
        if ($context === 'end') {
            $date->modify('last day of december this year');
        }
    } elseif (strlen($dateStr) === 7) {
        $date = new \DateTime($dateStr . '-01');
        if ($context === 'end') {
            $date->modify('last day of this month');
        }
    } elseif (strlen($dateStr) === 10) {
        $date = new \DateTime($dateStr);
    } else {
        throw new \InvalidArgumentException("Invalid partial date format: $dateStr");
    }

    $date->setTime(0, 0, 0);
    return $date;
}    
    
     /**
     * Applique le filtre numérique sur WHERE
     */
    private function applyNumericFilter(string $alias, string $field, array $parsed, array &$where, array &$params)
    {
        switch ($parsed['type']) {
            case 'range':
                $paramMin = "filter_{$alias}_min";
                $paramMax = "filter_{$alias}_max";
                $where[] = "$field BETWEEN :$paramMin AND :$paramMax";
                $params[$paramMin] = $parsed['min'];
                $params[$paramMax] = $parsed['max'];
                break;

            case 'in':
                $placeholders = [];
                foreach ($parsed['values'] as $i => $v) {
                    $param = "filter_{$alias}_$i";
                    $placeholders[] = ":$param";
                    $params[$param] = $v;
                }
                $where[] = "$field IN (".implode(', ', $placeholders).")";
                break;

            case 'greater_than':
                $param = "filter_$alias";
                $where[] = "$field > :$param";
                $params[$param] = $parsed['value'];
                break;

            case 'lower_than':
                $param = "filter_$alias";
                $where[] = "$field < :$param";
                $params[$param] = $parsed['value'];
                break;

            default:
                $param = "filter_$alias";
                $where[] = "$field = :$param";
                $params[$param] = $parsed['value'];
                break;
        }
    }
    
    /**
     * Applique un filtre date sur WHERE
     */
    private function applyDateFilter(QueryBuilder $qb, string $alias, string $field, array $parsed, array &$params)
    {
        if ($parsed['type'] === 'exact_date') {
            $param = "filter_{$alias}_exact";
            $qb->andWhere("SUBSTRING($field, 1, 10) = :$param");
            $params[$param] = $parsed['value']; // Format YYYY-MM-DD

        } elseif ($parsed['type'] === 'date_range') {
            $paramStart = "filter_{$alias}_start";
            $paramEnd = "filter_{$alias}_end";
            $qb->andWhere("SUBSTRING($field, 1, 10) BETWEEN :$paramStart AND :$paramEnd");
            $params[$paramStart] = $parsed['start'];
            $params[$paramEnd] = $parsed['end'];

        } elseif ($parsed['type'] === 'comparison') {
            $param = "filter_{$alias}_comp";
            $dateStr = $parsed['date']->format('Y-m-d');
            $qb->andWhere("SUBSTRING($field, 1, 10) {$parsed['operator']} :$param");
            $params[$param] = $dateStr;
        }
    }   

     /**
     * Applique le filtre numérique sur HAVING
     */
    private function applyAggregationNumericFilter(string $alias, string $field, array $parsed, array &$havingConditions, array &$params)
    {
        switch ($parsed['type']) {
            case 'range':
                $paramMin = "filter_{$alias}_min";
                $paramMax = "filter_{$alias}_max";
                $havingConditions[] = "$field BETWEEN :$paramMin AND :$paramMax";
                $params[$paramMin] = $parsed['min'];
                $params[$paramMax] = $parsed['max'];
                break;
            case 'in':
                $placeholders = [];
                foreach ($parsed['values'] as $i => $v) {
                    $param = "filter_{$alias}_$i";
                    $placeholders[] = ":$param";
                    $params[$param] = $v;
                }
                $havingConditions[] = "$field IN (".implode(', ', $placeholders).")";
                break;
            case 'greater_than':
                $param = "filter_$alias";
                $havingConditions[] = "$field > :$param";
                $params[$param] = $parsed['value'];
                break;
            case 'lower_than':
                $param = "filter_$alias";
                $havingConditions[] = "$field < :$param";
                $params[$param] = $parsed['value'];
                break;
            default:
                $param = "filter_$alias";
                $havingConditions[] = "$field = :$param";
                $params[$param] = $parsed['value'];
                break;
        }
    }

    /**
     * Applique le filtre Date sur HAVING
     */
    private function applyAggregationDateFilter(string $alias, string $field, array $parsed, array &$havingConditions, array &$params)
    {
        if ($parsed['type'] === 'exact_date') {
            $param = "filter_{$alias}_exact";
            $havingConditions[] = "SUBSTRING($field, 1, 10) = :$param";
            $params[$param] = $parsed['value'];

        } elseif ($parsed['type'] === 'date_range') {
            $paramStart = "filter_{$alias}_start";
            $paramEnd = "filter_{$alias}_end";
            $havingConditions[] = "SUBSTRING($field, 1, 10) BETWEEN :$paramStart AND :$paramEnd";
            $params[$paramStart] = $parsed['start'];
            $params[$paramEnd] = $parsed['end'];

        } elseif ($parsed['type'] === 'comparison') {
            $param = "filter_{$alias}_comp";
            $dateStr = $parsed['date']->format('Y-m-d');
            $havingConditions[] = "SUBSTRING($field, 1, 10) {$parsed['operator']} :$param";
            $params[$param] = $dateStr;
        }
    }

    /**
     * Applique les filtres sur le QueryBuilder
     *
     * @param QueryBuilder $qb
     * @param array $fields alias => champ DQL
     * @param array $types alias => type (integer/date/aggregation/string)
     * @param array $filters alias => valeur
     * @return array paramètres utilisés pour Doctrine
     */
    public function applyFilters(QueryBuilder $qb, array $fields, array $types, array $filters): array
    {
        $whereConditions = [];
        $havingConditions = [];
        $parameters = [];
        foreach ($filters as $alias => $value) {
            if (!isset($fields[$alias])) continue;
            $field = $fields[$alias];
            $value = trim($value);
            $type = $types[$alias] ?? 'string';
            // test si il y a le signe = comme premier caractère => filtre suivant le type déclaré sinon comme une chaine
            if (str_starts_with($value, '=')) {
                $value = substr($value, 1);
                if ($type == 'string') $type = 'exact_string';
            } else {
                if (!str_starts_with($value, '>') && !str_starts_with($value, '<')) {
                    if($type == 'aggregation_integer' || $type == 'aggregation_float' || 'aggregation_date') {
                        $type = 'aggregation';
                    } else {
                        $type = 'string';
                    }
                }
            }  
            if ($value === '' || $value == '<' || $value == '>') continue;
            switch ($type) {
                case 'integer':
                    $parsed = $this->parseIntegerFilter($value);
                    $this->applyNumericFilter($alias, $field, $parsed, $whereConditions, $parameters);
                    break;
                case 'float':
                    $parsed = $this->parseFloatFilter($value);
                    $this->applyNumericFilter($alias, $field, $parsed, $whereConditions, $parameters);
                    break;
                case 'date':
                    $parsed = $this->parseDateFilter($value);
                    $this->applyDateFilter($qb, $alias, $field, $parsed, $parameters);
                    break;
                case 'aggregation':
                    $paramName = "filter_$alias";
                    $havingConditions[] = "LOWER($field) LIKE LOWER(:$paramName) ESCAPE '\\'";
                    $parameters[$paramName] = "%".$this->escapeLike($value)."%";
                    break;
                case 'aggregation_integer':
                    $parsed = $this->parseIntegerFilter($value);
                    $this->applyAggregationNumericFilter($alias, $field, $parsed, $havingConditions, $parameters);
                    break;
                case 'aggregation_float':
                    $parsed = $this->parseFloatFilter($value);
                    $this->applyAggregationNumericFilter($alias, $field, $parsed, $havingConditions, $parameters);
                    break;
                case 'aggregation_date':
                    $parsed = $this->parseDateFilter($value);
                    $this->applyAggregationDateFilter($alias, $field, $parsed, $havingConditions, $parameters);
                    break;   
                case 'exact_string':
                    $paramName = "filter_$alias";
                    $whereConditions[] = "LOWER($field) LIKE LOWER(:$paramName) ESCAPE '\\'";
                    $parameters[$paramName] = $this->escapeLike($value);
                    break;
                default:
                    $paramName = "filter_$alias";
                    $whereConditions[] = "LOWER($field) LIKE LOWER(:$paramName) ESCAPE '\\'";
                    $parameters[$paramName] = "%".$this->escapeLike($value)."%";
                    break;
            }
        }
        if (!empty($whereConditions)) {
            $qb->andWhere(implode(' AND ', $whereConditions));
        }
        if (!empty($havingConditions)) {
            $qb->andHaving(implode(' AND ', $havingConditions));
        }
        foreach ($parameters as $name => $val) {
            $qb->setParameter($name, $val);
        }
        return $parameters;
    }

    
}
