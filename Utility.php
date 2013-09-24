<?php //-->
/*
 * This file is part of the Postgre package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Postgre;

use Eden\Sql\Query as SqlQuery;

/**
 * Generates utility query strings
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Utility extends SqlQuery
{
	protected $query = null;
	
	/**
	 * Query for dropping a table
	 *
	 * @param string the name of the table
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
	 * @param string the name of the table
	 * @param string the new name of the table
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
	 * @param string
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
	 * @param string the name of the table
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