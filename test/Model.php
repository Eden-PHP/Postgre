<?php //-->
/*
 * This file is part of the Utility package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Postgre_Test_Model extends PHPUnit_Framework_TestCase
{
	public static $database;
	
	public function setUp() {
		date_default_timezone_set('GMT');
		self::$database = eden('postgre', '127.0.0.1', 'eden', 'postgres', '');
        
        try {
            self::$database->query('DROP TABLE unit_post');
        } catch(Exception $e) {}
		//SCHEMA
		self::$database->query("CREATE TABLE unit_post (
			post_id bigserial primary key,
			post_slug varchar(255) NOT NULL,
			post_title varchar(255) default NULL,
			post_detail text default NULL,
			post_active int default 1,
			post_type text default 'post',
			post_flag int default 0,
			post_visibility text default 'public',
			post_status text default 'published',
			post_published text NOT NULL,
			post_created text NOT NULL,
			post_updated text NOT NULL
		);");
	}
	
	/* FACTORY METHODS */
    public function testAlter() 
    {
		
    }

    public function testDrop() {
        self::$database->query('DROP TABLE unit_post');
    }
}
