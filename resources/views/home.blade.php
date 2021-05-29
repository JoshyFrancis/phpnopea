@extends('layouts.app')
@section('links')
	@parent
			Links
	<br>		
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
					<h3>Sample Routes</h3>
					<ul>
						<li>
							<a href="{{url('optional/arg2/asd/1/2')}}">Optional argument - Route::get('optional/arg2/{name?}/{id?}/{c?}', function ($name = null,$id = null,$c = null) {</a>
						</li>
						<li>
							<a href="{{url('routes')}}">Simple route - Route::get('/routes', function ( Request $request ){ </a>
						</li>
						<li>
							<a href="{{url('admin/news')}}">Namespace - Route::get('admin/news', [ 'uses' => 'NewsController@index' ]); </a>
						</li>
						<li>
							<a href="{{url('match')}}">Match - Route::match(['get', 'post'], '/match', function(Request $request){</a>
						</li>
						<li>
							<a href="{{url('delete')}}">Delete - Route::delete( '/delete', function(Request $request){</a>
						</li>
						<li>
							<a href="{{url('books/shelf/1')}}">Group prefix - Route::group(['prefix' => 'books'], function () {</a>
						</li>
						<li>
							<a href="{{url('books/shelf/store/1')}}">Group prefix - books/shelf/store/1</a>
						</li>
						<li>
							<a href="{{url('books/store/1')}}">Group prefix - books/store/1</a>
						</li>
						<li>
							<a href="{{url('files/1')}}">Storage - Route::get('/files/{id}', function ($id) {</a>
						</li>
						<li>
							<a href="{{url('data')}}">multipart/form-data - Route::post('/data', function (Request $request ) {</a>
						</li>
						<li>
							<a href="{{url('photo')}}">Image route - <img src="{{url('photo')}}" width=64/></a>
						</li>
						<li>
							<a href="{{url('validatortest/create')}}">Validation - Route::get('validatortest/create', 'ValidatorTestController@create');</a>
						</li>
						<li>
							<a href="{{url('rc/resource/create')}}">Resource and Secure Ajax  - Route::resource('rc/resource', 'ResourceController');</a>
						</li>
						<li>
							<a href="{{url('route9999/321/test/123/test2/qwe')}}">Performance 10000 routes  - route9999/321/test/123/test2/qwe</a>
						</li>
						
					</ul>
					<div class="alert alert-info">
						Check routes\web.php for middleware and domain routing.
						 Create Database and update .env
					</div>
					 
					<pre style="color:#000000;background:#ffffff;">CREATE DATABASE IF NOT EXISTS `prologin` <span style="color:#3f5fbf; ">/*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */</span><span style="color:#800080; ">;</span>
					USE `prologin`<span style="color:#800080; ">;</span>

					CREATE TABLE IF NOT EXISTS `users` <span style="color:#808030; ">(</span>
					  `ID` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> AUTO_INCREMENT<span style="color:#808030; ">,</span>
					  `email` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">255</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `password` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">100</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `token` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">255</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `IP` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">500</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `username` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">25</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `first_name` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">25</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `last_name` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">25</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `avatar` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">1000</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">'default.png'</span><span style="color:#808030; ">,</span>
					  `joined` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0'</span><span style="color:#808030; ">,</span>
					  `joined_date` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">10</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `online_timestamp` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0'</span><span style="color:#808030; ">,</span>
					  `oauth_provider` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">40</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `oauth_id` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">1000</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `oauth_token` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">1500</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `oauth_secret` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">500</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `email_notification` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'1'</span><span style="color:#808030; ">,</span>
					  `aboutme` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">1000</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `points` decimal<span style="color:#808030; ">(</span><span style="color:#008c00; ">10</span><span style="color:#808030; ">,</span><span style="color:#008c00; ">2</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0.00'</span><span style="color:#808030; ">,</span>
					  `premium_time` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0'</span><span style="color:#808030; ">,</span>
					  `user_role` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0'</span><span style="color:#808030; ">,</span>
					  `premium_planid` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'0'</span><span style="color:#808030; ">,</span>
					  `active` <span style="color:#800000; font-weight:bold; ">int</span><span style="color:#808030; ">(</span><span style="color:#008c00; ">11</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#0000e6; ">'1'</span><span style="color:#808030; ">,</span>
					  `activate_code` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">255</span><span style="color:#808030; ">)</span> NOT <span style="color:#7d0045; ">NULL</span> DEFAULT <span style="color:#ffffff; background:#dd0000; font-weight:bold; font-style:italic; ">''</span><span style="color:#808030; ">,</span>
					  `remember_token` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">100</span><span style="color:#808030; ">)</span> DEFAULT <span style="color:#7d0045; ">NULL</span><span style="color:#808030; ">,</span>
					  `updated_at` datetime DEFAULT <span style="color:#7d0045; ">NULL</span><span style="color:#808030; ">,</span>
					  `created_at` datetime DEFAULT <span style="color:#7d0045; ">NULL</span><span style="color:#808030; ">,</span>
					  `password_reset_token` varchar<span style="color:#808030; ">(</span><span style="color:#008c00; ">255</span><span style="color:#808030; ">)</span> DEFAULT <span style="color:#7d0045; ">NULL</span><span style="color:#808030; ">,</span>
					  `password_reset_expiry` datetime DEFAULT <span style="color:#7d0045; ">NULL</span><span style="color:#808030; ">,</span>
					  PRIMARY KEY <span style="color:#808030; ">(</span>`ID`<span style="color:#808030; ">)</span>
					<span style="color:#808030; ">)</span> ENGINE<span style="color:#808030; ">=</span>InnoDB DEFAULT <span style="color:#603000; ">CHARSET</span><span style="color:#808030; ">=</span>utf8<span style="color:#800080; ">;</span>
					</pre>

					
					<h4>Include another view manually</h4>
					<?php
						
						include base_path().'/resources/views/include_view.php';
					?>
					<br>
                 @foreach($arr as $a)
				<?php
					echo "Going to skip Single Line comment<br>";
					// commented line contians vars {{test}} m
					echo "skipped<br>";//comment {{skipped }} m
					/*
						This is a 
						multiline comment contains vars {{test2}} m
					 */
					echo "skipped multiline comment <br>";
					$data0='This data is from PHP';
				?>
				<script>
					
					var div=document.createElement('div');
						div.innerHTML='Added by Javascript';
						div.innerHTML+='<br>Added by <?php echo 'php';/*comment {{skipped }} */ ?>';//comment {{skipped }} m
						div.innerHTML+='<br>data0:{{$data0}} m';
						div.innerHTML+='<br>Javascript @{{var1}} : @{{var2}} m';
					/*
						This is a 
						multiline comment contains vars {{test2}} in script block
					 */	
					document.body.appendChild(div);
					
				</script>
					<br>
					{{$a}}
				@endforeach
				
                <div class="panel-body">
                     
                        <div class="alert alert-success">
                            {{ date('l jS \of F Y h:i:s A') }} @ {{ date('now') }}
                        </div>
                     
						
                    You are logged in!
                    <br>
                    <?php 
						echo 'shared data : ' . $shared_data;
						echo '<br>';
						echo $test;
						var_dump($arr);
						/*
						echo "<pre></pre>";
						foreach(debug_backtrace() as $item){
							echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>"; 
							//var_dump($item['args']);
						}
						echo "</pre>";
						*/
						$some_data='this should be avaialble to the following view';
                    ?>
                    <br>
                    @include('include_test')
                    @if($arr[1]===2)
						{{ $arr[1] }}
						<br>
					@endif
                    @foreach($arr as $a)
						{{$a}}
						<br>
					@endforeach
                    @include('include_test2')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('links2')
	@parent
			Links2
	<br>
@endsection
@section('content2')
	Section Content 2
@endsection
@section('content3')
	Section Content 3
@endsection
