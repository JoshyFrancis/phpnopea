<br>
Included View
<br>
<?php 
	echo $some_data;
	
	echo $test;
	var_dump($arr);
	
	echo "<pre></pre>";
	foreach(debug_backtrace() as $item){
		echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>"; 
		
	}
	echo "</pre>";
	
?>

