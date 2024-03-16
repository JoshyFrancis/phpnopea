<?php
trait Hash{
	protected static $rounds = 10;
	public static function make($value, array $options = []){
        $hash = password_hash($value, PASSWORD_BCRYPT, [
            //'cost' => Hash::cost($options),
			'cost' => usingHash::cost($options),
        ]);

        if ($hash === false) {
            throw new Exception('Bcrypt hashing not supported.');
        }
        return $hash;
    }
	public static function check($value, $hashedValue){
        if(strlen($hashedValue) === 0) {
            return false;
        }
        return password_verify($value, $hashedValue);
    }
    public static function needsRehash($hashedValue, array $options = []){
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
           // 'cost' => Hash::cost($options),
		   'cost' => usingHash::cost($options),
        ]);
    }
    public static function setRounds($rounds){
        //Hash::$rounds = (int) $rounds;
		usingHash::$rounds = (int) $rounds;
        return $this;
    }
    public static function cost(array $options = []){
        //return isset($options['rounds']) ? $options['rounds'] : Hash::$rounds;
		return isset($options['rounds']) ? $options['rounds'] : usingHash::$rounds;
    }
}
class usingHash {
 use Hash;
}

