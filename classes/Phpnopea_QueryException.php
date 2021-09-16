<?php
namespace Phpnopea\Database;
use PDOException;
class QueryException extends PDOException{
    protected $sql;
    protected $bindings;
    public function __construct($sql, array $bindings, $previous){
        parent::__construct('', 0, $previous);

        $this->sql = $sql;
        $this->bindings = $bindings;
        $this->code = $previous->getCode();
        $this->message = $this->formatMessage($sql, $bindings, $previous);

        if ($previous instanceof PDOException) {
            $this->errorInfo = $previous->errorInfo;
        }
    }
    protected function formatMessage($sql, $bindings, $previous){
		foreach($bindings as $value){
			if(is_string($value)){
				$value="'".$value."'";
			}elseif($value instanceof DateTimeInterface){
				$value="'".$value->format('Y-m-d H:i:s')."'";
			}elseif(is_bool($value)){
				$value=(int)$value;
			}
				$pos=strpos($sql,'?');
			if($pos!==false){
				$sql=substr($sql, 0, $pos) .$value.substr($sql,  $pos+ 1 ) ;	 	
			}
		}
		$sql='"'.$sql.';"';
        return $previous->getMessage().' (SQL: '.$sql.')';
    }
    public function getSql(){
        return $this->sql;
    }
    public function getBindings(){
        return $this->bindings;
    }
}
