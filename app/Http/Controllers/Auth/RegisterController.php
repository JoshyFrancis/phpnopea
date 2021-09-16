<?php

namespace App\Http\Controllers\Auth;

use Phpnopea\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Validator;
use Hash;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
           // 'password' => bcrypt($data['password']),
            'password' => Hash::make( trim($data['password']) ),
        ]);
    }
     public function register(Request $request)
    {
        $validator =Validator::make( $request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
		if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
         $user = $this->create($request->all());

        auth()->guard()->login($user);

        return redirect($this->redirectTo);
    }
}
