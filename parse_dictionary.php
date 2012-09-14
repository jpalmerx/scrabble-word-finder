<pre>
<?php
ini_set('memory_limit', '128M');

$dictionary = file('enable.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//$dictionary = array_slice($dictionary, 0, 1000);
//print_r($dictionary);

//reformat array for alphabetical search
$ouput = array();
foreach ($dictionary as $v) {
	
	$alpha_string = str_split($v);
	sort($alpha_string);
	$alpha_string = implode($alpha_string);
	
	$output[$alpha_string][] = $v;
}

//print_r($output);

$data = '
<?php
$dictionary = array(';
foreach ($output as $k=>$v) {
	$data.='"'.$k.'" => array("'.implode('","', $v).'")';
	$data.=',';
}
$data = rtrim($data, ',');
$data.=');
?>
';

file_put_contents('alpha_dict.php', $data);
?>
</pre>
Success
