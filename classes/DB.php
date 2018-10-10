<?php
class DB{
	private static $DBH=null;
	private static $fetchMode = \PDO::FETCH_OBJ;//\PDO::FETCH_ASSOC
	function __construct(){
		self::createDB();
	}
	function __destruct() {
		//$this->DBH=null;
		self::$DBH=null;
    }
    public static function __callStatic ( $name, $args ) {
        //$callback = array ( self::DBH, $name ) ;
        //return call_user_func_array ( $callback , $args ) ;
        
    }
    public static function createDB () {
        if(self::$DBH==null){
			global $GLOBALS;
			$env=$GLOBALS['env'];
			$connection= 'mysql:host='.$env['DB_HOST'].';dbname='.$env['DB_DATABASE'];
			$user=$env['DB_USERNAME'];
			$pass=$env['DB_PASSWORD'];
			$DBH = new \PDO($connection, $user, $pass);
			$DBH->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			self::$DBH=$DBH;
		}
    }
    public static function setFetchMode ( $mode) {
        self::$fetchMode=$mode; 
    }
    public static function getPdo () {
			self::createDB();
        return self::$DBH; 
    }
    public static function lastInsertId () {
			self::createDB();
        return self::$DBH->lastInsertId(); 
    }
    public static function prepareBindings ( $bindings) {
			$out=[];
		foreach ($bindings as $value) {
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_bool($value)) {
                $value = (int) $value;
            } elseif ($value==null) {
				continue;
			}
			$out[]=$value;
        }
        
        return $out;
    }
    public static function select ( $sql, $bindings=[] ) {
			self::createDB();
        $STH = self::$DBH->prepare($sql );
		$STH->execute( self::prepareBindings( $bindings) );
		return $STH->fetchAll(self::$fetchMode); 
    }
    public static function update ( $sql, $bindings=[] ) {
			self::createDB();
        $STH = self::$DBH->prepare($sql );
		$STH->execute( self::prepareBindings( $bindings) );
		return $STH->rowCount();
    }
    public static function delete ( $sql, $bindings=[] ) {   
		return self::update($sql, $bindings);
    }
    public static function insert ( $sql, $bindings=[] ) {   
		return self::update($sql, $bindings);
    }
}
