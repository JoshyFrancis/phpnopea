<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Phpnopea\Http\Request;
class ResourceController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $request){
        return 'index';
    }
    public function create(Request $request){		 
		$path=str_replace('/create','', $request->path());
		
	  return '<html>
		<head>
		<script>
			function ajax() {
				var txtdata=document.getElementById("txtdata");
					txtdata.innerHTML = "";
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						txtdata.innerHTML = this.responseText;
					}
				};
				xmlhttp.open("GET", "'.url('API/'.$path).'?_token='.csrf_token().'"  , true);
				//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				xmlhttp.send();
			}
			function json() {
				var txtdata=document.getElementById("txtdata");
					txtdata.innerHTML = "";
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						txtdata.innerHTML = this.responseText;
					}
				};
				xmlhttp.open("POST", "'.url('API/'.$path).'?_token='.csrf_token().'" , true);
				xmlhttp.setRequestHeader("Content-type", "application/json");
				xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				xmlhttp.send(JSON.stringify({a: "a", b: "b"}));
			}
		</script>
		</head>
		<body>
		'.url('API/'.$path).'
		<form action="'.url($path).'" method="POST"  >
			'.csrf_field().'
			<input type="hidden" name="field1" value="form_data" /><br/>
			Post
			<input type="button" onclick="document.forms[0].submit();" value="submit" />
	   </form>
	   <form action="'.url($path).'/2" method="POST"  >
			'.csrf_field().'
			<input type="hidden" name="_method" value="DELETE">
			<input type="hidden" name="field2" value="form_data2" /><br/>
			Delete
			<input type="button" onclick="document.forms[1].submit();" value="submit" />
	   </form>
	   <form action="'.url($path).'/2" method="POST"  >
			'.csrf_field().'
			<input type="hidden" name="_method" value="PUT">
			<input type="hidden" name="field2" value="form_data2" /><br/>
			Update
			<input type="button" onclick="document.forms[2].submit();" value="submit" />
	   </form>
	   <p>Ajax Data: <span id="txtdata"></span></p>
	   <input type="button" onclick="ajax();" value="Ajax" />
	   <p></p>
	   <input type="button" onclick="json();" value="Json" />
	   <p></p>
	   <input type="button" onclick="location=\''.url($path).'/1/something/m\';" value="Method test" />
	   </body>
		</html>
	';
    }
    public function store(Request $request){
		var_dump($request->all());
        return  'store';
    }
    public function show(Request $request,$id){		 
        return  'show : '.$id;
    }
    public function edit(Request $request,$id){		 
        return  'edit : '.$id;
    }
    public function destroy(Request $request,$id){		 
        return  'destroy : '.$id;
    }
    public function update(Request $request,$id){		 
        return  'update : '.$id;
    }
    public function API(Request $request){
		var_dump($request->all());
		
		echo '<br>is ajax?' . ($request->ajax()?'true':'false') .'<br>';
		echo '<br>is ajax secure?' . ($request->ajax_secure()?'true':'false')  .'<br>';
		
        return  'API';
    }
    public function method_test(Request $request,$id2,$id){
			var_dump($id);		 
			var_dump($id2);
        return  'method_test' ;
    }
}
