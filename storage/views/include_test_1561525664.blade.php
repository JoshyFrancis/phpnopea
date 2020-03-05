<br>
Included View
<br>
<?php
echo 'Passed from above view : '. $some_data;
echo '<br>';
echo $test;
var_dump($arr);
echo "<pre></pre>";
foreach(debug_backtrace() as $item){
if(isset($item['file'])){
echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>";
}
}
echo "</pre>";
?>
