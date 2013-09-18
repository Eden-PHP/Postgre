<?php //-->
/*
 * This file is part of the Postgre package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Postgre;

use Eden\Sql\Insert as SqlInsert;

/**
 * Generates insert query string syntax
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Insert extends SqlInsert 
{
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 */
	public function getQuery() 
	{
		$multiValList = array();
		foreach($this->setVal as $val) {
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
	 * @param string
	 * @param string
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
		
		if(!in_array($key, $this->setKey)) {
			$this->setKey[] = $key;
		}
		
		if(is_null($value)) {
			$value = 'NULL';
		} else if(is_bool($value)) {
			$value = $value ? 'TRUE' : 'FALSE';
		}
		
		$this->setVal[$index][] = $value;
		return $this;
	}
}
