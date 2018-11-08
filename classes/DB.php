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
			$DBH->setAttribute(\PDO::ATTR_CASE,\PDO::CASE_NATURAL);
			$DBH->setAttribute(\PDO::ATTR_ORACLE_NULLS,\PDO::NULL_NATURAL);
			$DBH->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES,false);
			$DBH->setAttribute(\PDO::ATTR_EMULATE_PREPARES,true);//Default false
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
    public static function bindValues ($statement,$bindings){		
		foreach ($bindings as $key => $value) {
			//$statement->bindValue(//taken from laravel
            //    is_string($key) ? $key : $key + 1, $value,is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR
            //);
            $key=is_string($key)?$key:$key+1;
			if($value instanceof DateTimeInterface){
                $statement->bindValue($key,$value->format('Y-m-d H:i:s'),\PDO::PARAM_STR);
            }elseif(is_string($value)){
				$statement->bindValue($key,$value,\PDO::PARAM_STR);
            }elseif(is_bool($value)){
				$statement->bindValue($key,$value,\PDO::PARAM_BOOL);
			}elseif(is_int($value)){
				$statement->bindValue($key,$value,\PDO::PARAM_INT);
			}elseif(is_float($value)){
				$statement->bindValue($key,$value,\PDO::PARAM_STR);
            }elseif(is_object($value)){
                $statement->bindValue($key,null,\PDO::PARAM_NULL);
            }elseif($value===null){
                $statement->bindValue($key,null,\PDO::PARAM_NULL);
            }elseif($value === false){
                $statement->bindValue($key,0,\PDO::PARAM_INT);
            }else{
				$statement->bindValue($key,$value,\PDO::PARAM_STR);
			}
        }
	}
    public static function select($sql,$bindings=[]){
			self::createDB();
		try{
			$STH = self::$DBH->prepare($sql); 
			//$STH->execute( self::prepareBindings($bindings));
			//self::bindValues($STH,self::prepareBindings($bindings));
			self::bindValues($STH,$bindings);
			$STH->execute();
		}catch(Exception $e){
			throw new \Illuminate\Database\QueryException($sql,$bindings,$e);
		}
		return $STH->fetchAll(self::$fetchMode); 
    }
    public static function update($sql,$bindings=[]){
			self::createDB();
        $STH = self::$DBH->prepare($sql);
		//$STH->execute( self::prepareBindings($bindings));
		//self::bindValues($STH,self::prepareBindings($bindings));
		self::bindValues($STH,$bindings);
		$STH->execute();
		return $STH->rowCount();
    }
    public static function delete($sql,$bindings=[]){   
		return self::update($sql,$bindings);
    }
    public static function insert($sql,$bindings=[]){   
		return self::update($sql,$bindings);
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
