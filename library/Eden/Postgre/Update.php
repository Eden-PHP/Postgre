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
 * Generates update query string syntax
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Update extends Delete 
{
	protected $set = array();
	
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 * @notes returns the query based on the registry
	 */
	public function getQuery() 
	{
		$set = array();
		foreach($this->set as $key => $value) {
			$set[] = '"'.$key.'" = '.$value;
		}
		
		return 'UPDATE '. $this->table 
			. ' SET ' . implode(', ', $set) 
			. ' WHERE '. implode(' AND ', $this->where).';';
	}
	
	/**
	 * Set clause that assigns a given field name to a given value.
	 *
	 * @param string
	 * @param string
	 * @return this
	 * @notes loads a set into registry
	 */
	public function set($key, $value) 
	{
		//argument test
		Argument::i()
			//Argument 1 must be a string
			->test(1, 'string')				
			//Argument 2 must be scalar or null
			->test(2, 'scalar', 'null');	
		
		if(is_null($value)) {
			$value = 'NULL';
		} else if(is_bool($value)) {
			$value = $value ? 'TRUE' : 'FALSE';
		}
		
		$this->set[$key] = $value;
		
		return $this;
	}
}