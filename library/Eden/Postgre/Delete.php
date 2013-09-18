<?php //-->
/*
 * This file is part of the Postgre package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Postgre;

use Eden\Sql\Delete as SqlDelete;

/**
 * Generates delete query string syntax
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Delete extends SqlDelete 
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
		if(is_string($table)) {
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