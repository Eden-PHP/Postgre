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
 * Generates alter query string syntax
 *
 * @vendor Eden
 * @package Postgre
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Alter extends SqlQuery 
{
	protected $name = null;
	protected $changeFields = array();
	protected $addFields = array();
	protected $removeFields = array();
	protected $addPrimaryKeys = array();
	protected $removePrimaryKeys = array();
	
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
	 * @return Eden\Postgre\Alter
	 */
	public function addField($name, array $attributes) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->addFields[$name] = $attributes;
		return $this;
	}
	
	/**
	 * Adds a primary key
	 *
	 * @param string name
	 * @return Eden\Postgre\Alter
	 */
	public function addPrimaryKey($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->addPrimaryKeys[] = '"'.$name.'"';
		return $this;
	}
	
	/**
	 * Changes attributes of the table given 
	 * the field name
	 *
	 * @param string name
	 * @param array attributes
	 * @return Eden\Postgre\Alter
	 */
	public function changeField($name, array $attributes) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->changeFields[$name] = $attributes;
		return $this;
	}
	
	/**
	 * Returns the string version of the query 
	 *
	 * @param  bool
	 * @return string
	 */
	public function getQuery($unbind = false) 
	{	
		//Argument 1 must be a bool
		Argument::i()->test(1, 'bool');
		
		$fields = array();
		$table = '"'.$this->name.'""';
		
		foreach($this->removeFields as $name) {
			$fields[] = 'DROP COLUMN "'.$name.'"';
		}
		
		foreach($this->addFields as $name => $attr) {
			$field = array('ADD "'.$name.'"');
			if(isset($attr['type'])) {	
				$field[] = isset($attr['length']) ? $attr['type'] 
				. '('.$attr['length'].')' : 
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
		
		foreach($this->changeFields as $name => $attr) {
			$field = array('ALTER COLUMN "'.$name.'"');
			
			if(isset($attr['name'])) {	
				$field = array('CHANGE "'.$name.'"  "'.$attr['name'].'"');
			}
			
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
		
		foreach($this->removePrimaryKeys as $key) {
			$fields[] = 'DROP PRIMARY KEY "'.$key.'"';
		}
		
		if(!empty($this->addPrimaryKeys)) {
			$fields[] = 'ADD PRIMARY KEY ('.implode(', ', $this->addPrimaryKeys).')';
		}
		
		$fields = implode(", \n", $fields);
		
		return sprintf('ALTER TABLE %s %s;', $table, $fields);
	}
	
	/**
	 * Removes a field
	 *
	 * @param string name
	 * @return Eden\Postgre\Alter
	 */
	public function removeField($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->removeFields[] = $name;
		return $this;
	}
	
	/**
	 * Removes a primary key
	 *
	 * @param string name
	 * @return Eden\Postgre\Alter
	 */
	public function removePrimaryKey($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->removePrimaryKeys[] = $name;
		return $this;
	}
	
	/**
	 * Sets the name of the table you wish to create
	 *
	 * @param string name
	 * @return Eden\Postgre\Alter
	 */
	public function setName($name) 
	{
		//Argument 1 must be a string
		Argument::i()->test(1, 'string');
		
		$this->name = $name;
		return $this;
	}
}