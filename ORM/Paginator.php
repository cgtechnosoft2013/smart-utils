<?php

namespace SDLab\Bundle\SmartUtilsBundle\ORM;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\WhereInWalker;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\NoResultException;

/**
 * Replace doctrine paginator to optimize its logic
 *
 * @author <kevin@sdlab.fr>
 */
class Paginator extends DoctrinePaginator
{
    private $queryIds;
    
    public function __construct($query, $queryIds = null, $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);
        
        $this->queryIds = $queryIds;
    }
    
    public function count()
    {
        
        if ($this->count === null) {
            try {
                
                $this->count = array_sum(array_map('current', $this->getIdsQuery()->getScalarResult()));
            } catch(NoResultException $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }
    
    public function getIterator()
    {
        $offset = $this->getQuery()->getFirstResult();
        $length = $this->getQuery()->getMaxResults();

        if ($this->getFetchJoinCollection()) {
            $subQuery = $this->queryIds;

            if ($this->useOutputWalker($subQuery)) {
                $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker');
            } else {
                $this->appendTreeWalker($subQuery, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker');
            }

            $subQuery->setFirstResult($offset)->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());

            $whereInQuery = $this->cloneQuery($this->getQuery());
            // don't do this for an empty id array
            if (count($ids) == 0) {
                return new \ArrayIterator(array());
            }

            $this->appendTreeWalker($whereInQuery, 'Doctrine\ORM\Tools\Pagination\WhereInWalker');
            $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
            $whereInQuery->setFirstResult(null)->setMaxResults(null);
            $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);

            $result = $whereInQuery->getResult($this->getQuery()->getHydrationMode());
        } else {
            $result = $this->cloneQuery($this->getQuery())
                ->setMaxResults($length)
                ->setFirstResult($offset)
                ->getResult($this->getQuery()->getHydrationMode())
            ;
        }

        return new \ArrayIterator($result);
    }
    
    /**
     * Determines whether to use an output walker for the query.
     *
     * @param Query $query The query.
     *
     * @return bool
     */
    private function useOutputWalker(Query $query)
    {
        if ($this->getUseOutputWalkers() === null) {
            return (Boolean) $query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER) == false;
        }

        return $this->getUseOutputWalkers();
    }

    /**
     * Appends a custom tree walker to the tree walkers hint.
     *
     * @param Query $query
     * @param string $walkerClass
     */
    private function appendTreeWalker(Query $query, $walkerClass)
    {
        $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);

        if ($hints === false) {
            $hints = array();
        }

        $hints[] = $walkerClass;
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
    }
    
    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }
}
