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
 * Generates utility query strings
 *
 * @vendor   Eden
 * @package  Postgre
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Utility extends \Eden\Sql\Query
{
    /**
     * @var string|null $query The query string
     */
    protected $query = null;
    
    /**
     * Query for dropping a table
     *
     * @param *string $table The name of the table
     *
     * @return Eden\Postgre\Utility
     */
    public function dropTable($table)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        $this->query = 'DROP TABLE "' . $table .'"';
        return $this;
    }
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query.';';
    }
    
    /**
     * Query for renaming a table
     *
     * @param *string $table The name of the table
     * @param *string $name  The new name of the table
     *
     * @return Eden\Postgre\Utility
     */
    public function renameTable($table, $name)
    {
        //Argument 1 must be a string, 2 must be string
        Argument::i()->test(1, 'string')->test(2, 'string');
        
        $this->query = 'RENAME TABLE "' . $table . '" TO "' . $name . '"';
        return $this;
    }
    
    /**
     * Specify the schema
     *
     * @param *string $schema The schema name
     *
     * @return Eden\Postgre\Utility
     */
    public function setSchema($schema)
    {
        $this->query = 'SET search_path TO '.$schema;
        return $this;
    }
    
    /**
     * Query for truncating a table
     *
     * @param *string $table The name of the table
     *
     * @return Eden\Postgre\Utility
     */
    public function truncate($table)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        $this->query = 'TRUNCATE "' . $table .'"';
        return $this;
    }
}
