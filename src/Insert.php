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
 * Generates insert query string syntax
 *
 * @vendor   Eden
 * @package  Postgre
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Insert extends \Eden\Sql\Insert
{
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        $multiValList = array();
        foreach ($this->setVal as $val) {
            $multiValList[] = '('.implode(', ', $val).')';
        }
        
        return 'INSERT INTO "'. $this->table
            . '" ("'.implode('", "', $this->setKey).'") VALUES '
            . implode(", \n", $multiValList).';';
    }
    
    /**
     * Set clause that assigns a given field name to a given value.
     * You can also use this to add multiple rows in one call
     *
     * @param *string      $key   The column name
     * @param *scalar|null $value The column value
     * @param int          $index For what row is this for?
     *
     * @return this
     * @notes loads a set into registry
     */
    public function set($key, $value, $index = 0)
    {
        //argument test
        Argument::i()
            //Argument 1 must be a string
            ->test(1, 'string')
            //Argument 2 must be scalar or null
            ->test(2, 'scalar', 'null');
        
        if (!in_array($key, $this->setKey)) {
            $this->setKey[] = $key;
        }
        
        if (is_null($value)) {
            $value = 'NULL';
        } else if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }
        
        $this->setVal[$index][] = $value;
        return $this;
    }
}
