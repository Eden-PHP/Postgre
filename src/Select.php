<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Postgre;

/**
 * Generates select query string syntax
 *
 * @vendor   Eden
 * @package  Postgre
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Select extends \Eden\Sql\Select
{
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        $joins = empty($this->joins) ? '' : implode(' ', $this->joins);
        $where = empty($this->where) ? '' : 'WHERE '.implode(' AND ', $this->where);
        $sort = empty($this->sortBy) ? '' : 'ORDER BY '.implode(', ', $this->sortBy);
        $limit = is_null($this->page) ? '' : 'LIMIT ' . $this->length .' OFFSET ' .$this->page;
        $group = empty($this->group) ? '' : 'GROUP BY ' . implode(', ', $this->group);
        
        $query = sprintf(
            'SELECT %s FROM %s %s %s %s %s %s;',
            $this->select,
            $this->from,
            $joins,
            $where,
            $group,
            $sort,
            $limit
        );
        
        return str_replace('  ', ' ', $query);
    }
}
