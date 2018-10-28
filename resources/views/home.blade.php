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
				<?php
					echo "Going to skip Single Line comment<br>";
					// commented line contians vars{{test}}
					echo "skipped<br>";//comment {{skipped }}
					/*
						This is a 
						multiline comment contains vars {{test2}}
					 */
					echo "skipped multiline comment <br>";
					$data0='data0';
				?>
				<script>
					
					var div=document.createElement('div');
						div.innerHTML='Added by Javascript';//comment {{skipped }}
						div.innerHTML+='<br>Added by <?php echo 'php';//comment {{skipped }} ?>';//comment {{skipped }}
						div.innerHTML+='<br>data0:{{$data0}}';
						div.innerHTML+='<br>Javascript @{{var1}} : @{{var2}}';
					/*
						This is a 
						multiline comment contains vars {{test2}} in script block
					 */	
					document.body.appendChild(div);
					
				</script>
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
                    @if($arr[1]===1)
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
