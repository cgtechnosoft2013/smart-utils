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
    
    protected $tablesInQbCount = array();
    
    public function __construct($qbResult, $qbCount)
    {
        $this->qbResult = $qbResult;
        $this->qbIds = $qbCount;
    }
    
    public function addTableInQbCount($tableName, $alias)
    {
        if (!$this->isTablePresentInQbCountFrom($tableName)) {
            $this->qbIds->leftJoin($tableName, $alias);
            $this->addTableInQbCountFrom($tableName);
        }
    }
    
    protected function isTablePresentInQbCountFrom($tablename)
    {
        return in_array($tablename, $this->tablesInQbCount);
    }
    
    protected function addTableInQbCountFrom($tablename)
    {
        $this->tablesInQbCount[$tablename] = $tablename;
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
