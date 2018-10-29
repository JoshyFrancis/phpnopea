View will be included
<?php 
	$view = View::make('include_view.index' );
	//var_dump($view);
	//echo (string) $view;
	echo $view->render();//also gets errors if any
