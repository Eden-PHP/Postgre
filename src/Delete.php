<?php //-->
/*
 * This file is part of the Postgre package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Postgre;

/**
 * Generates delete query string syntax
 *
 * @vendor   Eden
 * @package  postgre
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Delete extends \Eden\Sql\Delete
{
    protected $table = null;
    protected $where = array();
    
    /**
     * Construct: set table name, if given
     *
     * @param string|null
     */
    public function __construct($table = null)
    {
        if (is_string($table)) {
            $this->setTable($table);
        }
    }
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        return 'DELETE FROM "'. $this->table . '" WHERE '. implode(' AND ', $this->where).';';
    }
}
