<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
 
class Eden_Postgre_Test_Collection extends PHPUnit_Framework_TestCase
{
	public static $database;
	
	public function setUp() {
		date_default_timezone_set('GMT');
		self::$database = eden('postgre', '127.0.0.1', 'eden', 'postgres', 'rooter');
        
	}
	
	/* FACTORY METHODS */
    public function testAlter() 
    {
		$this->assertTrue(true);
    }
}
