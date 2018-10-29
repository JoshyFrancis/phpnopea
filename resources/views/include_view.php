View will be included
<?php 
	$view = View::make('include_view.index',['array_data'=>[ 'ar1'=>[1,2],'ar2'=>[3,4]]] );
	//var_dump($view);
	//echo (string) $view;
	echo $view->render();//also gets errors if any
