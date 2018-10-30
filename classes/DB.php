<?php
class DB{
	private static $DBH=null;
	private static $fetchMode = \PDO::FETCH_OBJ;//\PDO::FETCH_ASSOC
	//private static $search = ["\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"];
    //private static $replace = ["\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"];
    private static $connection;
	function __construct(){
		
		self::createDB();
		
	}
	function __destruct() {
		self::$DBH=null;
    }
    public static function __callStatic ( $name, $args ) {
    }
    public function __call( $name, $args ) {
    }
    public static function createDB () {
        if(self::$DBH===null){
			global $GLOBALS;
			$env=$GLOBALS['env'];
			$connection= 'mysql:host='.$env['DB_HOST'].';dbname='.$env['DB_DATABASE'].';charset=utf8';
			$user=$env['DB_USERNAME'];
			$pass=$env['DB_PASSWORD'];
			$DBH = new \PDO($connection, $user, $pass);
			$DBH->setAttribute(\PDO::ATTR_EMULATE_PREPARES,false);
			$DBH->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
			self::$DBH=$DBH;
			self::$connection=new connection(self::$DBH);
		}
		
    }
    public static function setFetchMode ( $mode) {
        self::$fetchMode=$mode; 
    }
    public static function connection(){
			self::createDB();
        return self::$connection; 
    }
    public static function getPdo(){
			self::createDB();
        return self::$DBH; 
    }
    public static function lastInsertId (){
			self::createDB();
        return self::$DBH->lastInsertId(); 
    }
    //private static function escape($value){
    //    return str_replace(self::$search, self::$replace, $value);
    //}
    public static function prepareBindings ($bindings){
			$out=[];
		foreach ($bindings as $value) {
			if($value instanceof Closure){
				$value =null;
            }elseif($value instanceof DateTimeInterface){
                $value = $value->format('Y-m-d H:i:s');
            }elseif (is_bool($value)){
                $value = (int) $value;
            //}elseif (is_string($value)){
				//$value=PDO::quote($value);
				//$value=self::$DBH->quote($value);
			//	$value=self::escape($value);
            //}elseif ($value===null){
				//continue;		
			}
			$out[]=$value;
        }
        
        return $out;
    }
    public static function select($sql,$bindings=[]){
			self::createDB();
        $STH = self::$DBH->prepare($sql);
		$STH->execute( self::prepareBindings($bindings));
		return $STH->fetchAll(self::$fetchMode); 
    }
    public static function update($sql,$bindings=[]){
			self::createDB();
        $STH = self::$DBH->prepare($sql);
		$STH->execute( self::prepareBindings($bindings));
		return $STH->rowCount();
    }
    public static function delete ($sql,$bindings=[]){   
		return self::update($sql, $bindings);
    }
    public static function insert ($sql,$bindings=[]){   
		return self::update($sql, $bindings);
    }
}
class connection{
	private $DBH=null;
	function __construct($DBH){
		$this->DBH=$DBH;
	}
	public function getPdo(){
        return $this->DBH; 
    }
}
