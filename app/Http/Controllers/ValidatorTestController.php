<?php
namespace App\Http\Controllers;
use Validator;
use Phpnopea\Http\Request;

class ValidatorTestController extends Controller{
    public function create(){
        return view('validatortest.create');
    }
    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:5',          
			'title' => 'required|string|min:3|max:6',
			'email' => 'required|string|email|max:255',
			//'password' => 'required|confirmed',
			'password'   => 'required|min:4|same:password_confirmation',
			'profilepic.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:400', 
			//'profilepic.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:400',
            
        ],
		[
				'profilepic.required'    => 'Please Select a file.',
				'profilepic.max'    => 'The :attribute must be less than :max KB.',
				'image' => 'The :attribute must be an image.',
				'mimes'      => 'The :attribute must be one of the following types: :values',
				'password.same'      => 'The :attribute and confirm password field must match.',
		]);

        if ($validator->fails()) {
            return redirect('validatortest/create')->withErrors($validator)->withInput();
        }
        /**/
        var_dump($request->all());
		return 'Validation ok<br><a href="'.url('validatortest/create').'" >Back</a>';
    }
}
