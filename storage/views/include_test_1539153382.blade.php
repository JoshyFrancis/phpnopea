Included View
<?php 
	echo $test;
	var_dump($arr);
	
	echo "<pre></pre>";
	foreach(debug_backtrace() as $item){
		echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>"; 
		//var_dump($item['args']);
	}
	echo "</pre>";
	
?>

