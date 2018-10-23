<?php
class Auth{
	public $id;
	public $name;
	public $remember_time=((60*60)*24)*365;//365 days
	public static function __callStatic($method, $parameters){
			Route::$auth->guard();
		return Route::$auth;
	}
	public function __call($method, $parameters){
			Route::$auth->guard();
		return Route::$auth;
    }
	public function guest(){
			$this->check();
		return !Route::$request->session->get('_login',false);
	}
	public function guard(){
		$this->id=Route::$request->session->get('_userID',null);
		$this->name=Route::$request->session->get('_userName',null);
		return $this;
	}
	public function check(){
		global $GLOBALS;
			$session_name=$GLOBALS['session_name'];
		$remember_cookie=$session_name.'_remember';
		$cookie=Route::$request->cookies->get($remember_cookie);
		$login=Route::$request->session->get('_login',false);
		if($cookie){
			$login=false;
			list($token,$time)=explode('_',$cookie);
				$token.='_'.$time;
			$rows =DB::select('SELECT ID,password,username from users where remember_token=?' ,[$token ] );
			if(count($rows)>0){
				//if(time()<=(int)$time){
					 $login=true;
						$ID=$rows[0]->ID;
					 Route::$request->session->put('_userID',$ID);
					 Route::$request->session->put('_userName',$rows[0]->username );
				//}else{
				//	remove_cookie($remember_cookie);
				//}
			}
			Route::$request->session->put('_login',$login);
			Route::$request->session->save();
		}
		return $login;
	}
	public function attempt($credentials,$remember=''){
		global $GLOBALS;
			$app_key=$GLOBALS['app_key'];
			$env=$GLOBALS['env'];
			$session_name=$GLOBALS['session_name'];
			$remember_cookie=$session_name.'_remember';
		$login=false;
		Route::$request->session->put('_login',false);
			$email=$credentials['email'];
			$password=$credentials['password'];
			$active= isset($credentials['active'])?$credentials['active']:null;
			//var_dump($remember);
			
				remove_cookie($remember_cookie);
							
				DB::setFetchMode(\PDO::FETCH_ASSOC);
				$rows =DB::select('SELECT ID,password,username from users where email=? '.( $active!=null ?' and active=?':''),[$email,$active ] );
				DB::setFetchMode(\PDO::FETCH_OBJ);
				
			if(count($rows)>0){
				
					$ID=$rows[0]['ID'];
					
					/*
						
						$hash = hash_hmac('sha512', $password, $app_key); 
						var_dump($hash); 
						if (hash_hmac('sha256', $password, $app_key)== $rows[0]['password']){
					
						var_dump($password);
						var_dump($rows[0]['password']);
					
						$options = [
							'cost' => 10,
						];
						$hash=password_hash( $password  , PASSWORD_BCRYPT,$options );// laravel method
						var_dump($hash);
						var_dump($rows[0]['password']);
						var_dump(password_verify($password, $hash));// true
						if(	password_needs_rehash($hashedValue, PASSWORD_BCRYPT,  $options)){
					 
					*/
					
		
				if (password_verify($password, $rows[0]['password'])){
					//var_dump($credentials);
					// header('Location: http://www.example.com/',true,302); /* Redirect browser */
					// header( 'refresh:5;url=wherever.php' ); 
					//echo 'You\'ll be redirected in about 5 secs. If not, click <a href="wherever.php">here</a>.'; 
					// 301 Moved Permanently
					//header("Location: /foo.php",TRUE,301);
					 $login=true;
					 Route::$request->session->put('_userID',$ID);
					 Route::$request->session->put('_userName',$rows[0]['username']);
					 
					if($remember==='on'){
						$time=time()+$this->remember_time;
						$token=bin2hex(openssl_random_pseudo_bytes(32)).'_'.$time;
						
						$rows_affected =DB::update('UPDATE users set remember_token=? where ID=? '.( $active!=null ?' and active=?':''),[$token,$ID,$active ] );
						if($rows_affected>0){
							set_cookie($remember_cookie ,$token ,$time);
						}
					}
				}
			}
		 
		Route::$request->session->put('_login',$login);
		Route::$request->session->save();
		return $login;
	}
	public function logout(){
		global $GLOBALS;
		$session_name=$GLOBALS['session_name'];
		$remember_cookie=$session_name.'_remember';
			remove_cookie($remember_cookie);
		//Route::$request->session()->destroy_current();
		Route::$request->session->restart();
	}
}
function auth(){
	Route::$auth->guard();	
	return Route::$auth;
}
