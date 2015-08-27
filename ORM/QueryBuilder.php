<?php

namespace SDLab\Bundle\SmartUtilsBundle\ORM;

/**
 * This queryBuilder builds 2 query
 * One to find ids of object we want and an other to get objects
 * Main purpose is to replace simple doctrine paginator
 *
 * @author <kevin@sdlab.fr>
 */
class QueryBuilder
{
    protected $qbResult;
    protected $qbIds;
    
    protected $tablesInQbIds = array();
    
    public function __construct($qbResult, $qbIds)
    {
        $this->qbResult = $qbResult;
        $this->qbIds = $qbIds;
    }
    
    public function addTableInQbIds($tableName, $alias)
    {
        if (!$this->isTablePresentInQbIdsFrom($tableName)) {
            $this->qbIds->leftJoin($tableName, $alias);
            $this->addTableInQbIdsFrom($tableName);
        }
    }
    
    protected function isTablePresentInQbIdsFrom($tablename)
    {
        return in_array($tablename, $this->tablesInQbIds);
    }
    
    protected function addTableInQbIdsFrom($tablename)
    {
        $this->tablesInQbIds[$tablename] = $tablename;
    }
    
    public function __call($method, $args)
    {
        call_user_func_array(array($this->qbResult, $method), $args);
        call_user_func_array(array($this->qbIds, $method), $args);
        
        return $this;
    }
    
    public function getQuery()
    {
        return $this->qbResult->getQuery();
    }
    
    public function getPaginator()
    {
        return new Paginator($this->qbResult->getQuery(), $this->qbIds->getQuery(), true);
    }
    
    public function expr()
    {
        return $this->qbResult->expr();
    }
}
