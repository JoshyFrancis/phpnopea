@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                     
                        <div class="alert alert-success">
                            {{ date('l jS \of F Y h:i:s A') }} @ {{ date('now') }}
                        </div>
                     
						
                    You are logged in!
                    <br>
                    <?php 
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
                    ?>
                    <br>
                    @include('include_test')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('content2')
	Section Content 2
@endsection
