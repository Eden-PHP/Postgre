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
 * Abstractly defines a layout of available methods to
 * connect to and query a Postgre database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all SQL classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   Eden
 * @package  Postgre
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Index extends \Eden\Sql\Index
{
    /**
     * @var string $host Database host
     */
    protected $host = 'localhost';

    /**
     * @var string $port Database port
     */
    protected $port = '5432';

    /**
     * @var string|null $name Database name
     */
    protected $name = null;

    /**
     * @var string|null $user Database user name
     */
    protected $user = null;

    /**
     * @var string|null $pass Database password
     */
    protected $pass = null;
    
    /**
     * Construct: Store connection information
     *
     * @param *string      $host Database host
     * @param *string|null $name Database name
     * @param *string|null $user Database user name
     * @param string|null  $pass Database password
     * @param number|null  $port Database port
     */
    public function __construct($host, $name, $user, $pass = null, $port = null)
    {
        //argument test
        Argument::i()
            //Argument 1 must be a string or null
            ->test(1, 'string', 'null')
            //Argument 2 must be a string
            ->test(2, 'string')
            //Argument 3 must be a string
            ->test(3, 'string')
            //Argument 4 must be a string or null
            ->test(4, 'string', 'null')
            //Argument 5 must be a number or null
            ->test(5, 'numeric', 'null');
        
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }
        
    /**
     * Returns the alter query builder
     *
     * @param *string $name Name of table
     *
     * @return Eden\Postgre\Alter
     */
    public function alter($name = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Alter::i($name);
    }
    
    /**
     * Connects to the database
     *
     * @param array $options the connection options
     *
     * @return Eden\Postgre\Index
     */
    public function connect(array $options = array())
    {
        $host = $port = null;
        
        if (!is_null($this->host)) {
            $host = 'host='.$this->host.';';
            if (!is_null($this->port)) {
                $port = 'port='.$this->port.';';
            }
        }
        
        $connection = 'pgsql:'.$host.$port.'dbname='.$this->name;
        
        $this->connection = new \PDO($connection, $this->user, $this->pass, $options);
        
        $this->trigger('postgre-connect');
        
        return $this;
    }
        
    /**
     * Returns the create query builder
     *
     * @param *string $name Name of table
     *
     * @return Eden\Postgre\Create
     */
    public function create($name = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Create::i($name);
    }
    
    /**
     * Returns the delete query builder
     *
     * @param *string|null $table The table name
     *
     * @return Eden\Postgre\Delete
     */
    public function delete($table = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Delete::i($table);
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string $table   The name of the table
     * @param array   $filters Where filters
     *
     * @return array
     */
    public function getColumns($table, $schema = null)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string')->test(2, 'string', 'null');
        
        $select = array(
            'columns.table_schema',
            'columns.column_name',
            'columns.ordinal_position',
            'columns.column_default',
            'columns.is_nullable',
            'columns.data_type',
            'columns.character_maximum_length',
            'columns.character_octet_length',
            'pg_class2.relname AS index_type');
        
        $where = array(
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid');
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = Select::i()
            ->select($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS	AS columns', $where[1], false)
            ->leftJoin('pg_index', $where[2], false)
            ->leftJoin('pg_class AS pg_class2', $where[3], false)
            ->getQuery();
        
        $results = $this->query($query);
        
        $columns = array();
        foreach ($results as $column) {
            $key = null;
            if (strpos($column['index_type'], '_pkey') !== false) {
                $key = 'PRI';
            } else if (strpos($column['index_type'], '_key') !== false) {
                $key = 'UNI';
            }
            
            $columns[] = array(
                'Field'     => $column['column_name'],
                'Type'      => $column['data_type'],
                'Default'   => $column['column_default'],
                'Null'      => $column['is_nullable'],
                'Key'       => $key);
        }
        
        return $columns;
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string     $table  the name of the table
     * @param string|null $schema if from a particular schema
     *
     * @return array
     */
    public function getIndexes($table, $schema = null)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string')->test(2, 'string', 'null');
        
        $select = array('columns.column_name',
            'pg_class2.relname AS index_type');
        
        $where = array(
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid');
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = Select::i()
            ->select($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS	AS columns', $where[1], false)
            ->innerJoin('pg_index', $where[2], false)
            ->innerJoin('pg_class AS pg_class2', $where[3], false)
            ->getQuery();
            
        return $this->query($query);
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string     $table  the name of the table
     * @param string|null $schema if from a particular schema
     *
     * @return array
     */
    public function getPrimary($table, $schema = null)
    {
        //Argument 1 must be a string
        Argument::i()->test(1, 'string')->test(2, 'string', 'null');
        
        $select = array('columns.column_name');
        
        $where = array(
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid');
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = Select::i()
            ->select($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS	AS columns', $where[1], false)
            ->innerJoin('pg_index', $where[2], false)
            ->innerJoin('pg_class AS pg_class2', $where[3], false)
            ->where('pg_class2.relname LIKE \'%_pkey\'')
            ->getQuery();
        
        return $this->query($query);
    }
    
    /**
     * Returns a listing of tables in the DB
     *
     * @return array|false
     */
    public function getTables()
    {
        $query = Select::i()
            ->select('tablename')
            ->from('pg_tables')
            ->where("tablename NOT LIKE 'pg\\_%'")
            ->where("tablename NOT LIKE 'sql\\_%'")
            ->getQuery();
        
        return $this->query($query);
    }
    
    /**
     * Returns the insert query builder
     *
     * @param string|null $table Name of table
     *
     * @return Eden\Postgre\Insert
     */
    public function insert($table = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Insert::i($table);
    }
    
    /**
     * Returns the select query builder
     *
     * @param string|array $select Column list
     *
     * @return Eden\Postgre\Select
     */
    public function select($select = '*')
    {
        //Argument 1 must be a string or array
        Argument::i()->test(1, 'string', 'array');
        
        return Select::i($select);
    }
    
    /**
     * Set schema search paths
     *
     * @param string $schema Schema name
     *
     * @return Eden\Postgre\Index
     */
    public function setSchema($schema)
    {
        $schema = array($schema);
        if (func_num_args() > 0) {
            $schema = func_get_args();
        }
        
        $error = Argument::i();
        foreach ($schema as $i => $name) {
            $error->test($i + 1, 'string');
        }
        
        $schema = implode(',', $schema);
        
        $query = $this->utility()->setSchema($schema);
        $this->query($query);
        
        return $this;
    }
    
    /**
     * Returns the update query builder
     *
     * @param string|null $table Name of table
     *
     * @return Eden\Postgre\Update
     */
    public function update($table = null)
    {
        //Argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
        
        return Update::i($table);
    }

    /**
     * Returns the alter query builder
     *
     * @return Eden\Postgre\Utility
     */
    public function utility()
    {
        return Utility::i();
    }
}
