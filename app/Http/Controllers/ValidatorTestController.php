<?php
namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;

class ValidatorTestController extends Controller{
    public function create(){
        return view('validatortest.create');
    }
    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:5',          
			'title' => 'required',
			'profilepic' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:40', 
			//'profilepic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:40',
            
        ],
		[
				'profilepic.required'    => 'Please Select a file.',
				'max'    => 'The :attribute must be less than :max KB.',
				'image' => 'The :attribute must be an image.',
				'mimes'      => 'The :attribute must be one of the following types: :values',
		]);

        if ($validator->fails()) {
            return redirect('validatortest/create')->withErrors($validator)->withInput();
        }
        /**/
        var_dump($request->all());
		return 'Validation ok<br><a href="'.url('validatortest/create').'" >Back</a>';
    }
}
