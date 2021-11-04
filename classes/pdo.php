<?php
define('DB_HOST','localhost'); 
define('DB_NAME','rpi'); 
define('DB_USER','ballyadmin');
define('DB_PASS','nop@567'); 
define('DB_CHAR','utf8'); 
class DBSql {
    protected function __clone() {}
    public $pdo=null;  
    public function __construct() {
        $this->id = null; 
        if($this->pdo == null) {
            $opt = array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                PDO::ATTR_EMULATE_PREPARES => false, 
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            );
            $dsn = "sqlsrv:Server=dbsvr02;Database=smart_gaming";
            //$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            $this->pdo = new PDO($dsn, 'rpiuser', 'nop@567');
        }
    }

    function run($sql, $args = NULL)
    {
        if(!$args)
        {
            return $this->pdo->query($sql); 
        }
        $stmt = $this->pdo->prepare($sql); 
        $stmt->execute($args);  
        return $stmt; 
    }
}

class DB
{
    protected function __clone() {}
    public $pdo=null;  
    public function __construct() {
        $this->id = null; 
        if($this->pdo == null) {
            $opt = array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                PDO::ATTR_EMULATE_PREPARES => false, 
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            );
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
        }
    }

    function run($sql, $args = NULL)
    {
        if(!$args)
        {
            return $this->pdo->query($sql); 
        }
        $stmt = $this->pdo->prepare($sql); 
        $stmt->execute($args);  
        return $stmt; 
    }
}