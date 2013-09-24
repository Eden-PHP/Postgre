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
 * Generates create table query string syntax
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Create extends SqlQuery 
{
	protected $name	= null;
	protected $fields = array();
	protected $primaryKeys = array();
	protected $oids	= false;
	
	/**
	 * Construct: set table name, if given
	 *
	 * @param string|null
	 */
	public function __construct($name = null) 
	{
		if(is_string($name)) {
			$this->setName($name);
		}
	}
	
	/**
	 * Adds a field in the table
	 *
	 * @param string name
	 * @param array attributes
	 * @return Eden\Postgre\Create
	 */
	public function addField($name, array $attributes) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->fields[$name] = $attributes;
		return $this;
	}
	
	/**
	 * Adds a primary key
	 *
	 * @param string name
	 * @return Eden\Postgre\Create
	 */
	public function addPrimaryKey($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->primaryKeys[] = $name;
		return $this;
	}
	
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 * @notes returns the query based on the registry
	 */
	public function getQuery($unbind = false) 
	{	
		$table = '"'.$this->name.'"';
		
		$fields = array();
		foreach($this->fields as $name => $attr) {
			$field = array('"'.$name.'"');
			if(isset($attr['type'])) {	
				$field[] = isset($attr['length']) ? 
					$attr['type'] . '('.$attr['length'].')' : 
					$attr['type'];
				
				if(isset($attr['list']) && $attr['list']) {
					$field[count($field)-1].='[]';
				}
			}
			
			if(isset($attr['attribute'])) {
				$field[] = $attr['attribute'];
			}
			
			if(isset($attr['unique']) && $attr['unique']) {
				$field[] = 'UNIQUE';
			}
			
			if(isset($attr['null'])) {
				if($attr['null'] == false) {
					$field[] = 'NOT NULL';
				} else {
					$field[] = 'DEFAULT NULL';
				}
			}
			
			if(isset($attr['default'])&& $attr['default'] !== false) {
				if(!isset($attr['null']) || $attr['null'] == false) {
					if(is_string($attr['default'])) {
						$field[] = 'DEFAULT \''.$attr['default'] . '\'';
					} else if(is_numeric($attr['default'])) {
						$field[] = 'DEFAULT '.$attr['default'];
					}
				}
			}
			
			$fields[] = implode(' ', $field);
		}
		
		$oids = $this->oids ? 'WITH OIDS': null;
		$fields = !empty($fields) ? implode(', ', $fields) : '';
		$primary = !empty($this->primaryKeys) ? 
			', PRIMARY KEY ("'.implode('", ""', $this->primaryKeys).'")' : 
			'';
		
		return sprintf('CREATE TABLE %s (%s%s) %s', $table, $fields, $primary, $oids);
	}
	
	/**
	 * Sets a list of fields to the table
	 *
	 * @param array fields
	 * @return Eden\Postgre\Create
	 */
	public function setFields(array $fields) 
	{
		$this->fields = $fields;
		return $this;
	}
	
	/**
	 * Sets the name of the table you wish to create
	 *
	 * @param string name
	 * @return Eden\Postgre\Create
	 */
	public function setName($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Sets a list of primary keys to the table
	 *
	 * @param array primaryKeys
	 * @return Eden\Postgre\Create
	 */
	public function setPrimaryKeys(array $primaryKeys) 
	{
		$this->primaryKeys = $primaryKeys;
		return $this;
	}
	
	/**
	 * Specifying if query should add the OIDs as columns
	 *
	 * @param bool
	 * @return Eden\Postgre\Create
	 */
	public function withOids($oids) 
	{
		//Argument 1 must be a boolean
		Argument::i()->test(1, 'bool');
		
		$this->oids = $oids;
		return $this;
	}
	
}