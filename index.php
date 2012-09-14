<?php
ini_set('memory_limit', '256M');

//Game Settings
$num_player_tiles = 7;
$tile_points = array(
	'a'=>1,
	'b'=>3,
	'c'=>3,
	'd'=>2,
	'e'=>1,
	'f'=>4,
	'g'=>2,
	'h'=>4,
	'i'=>1,
	'j'=>8,
	'k'=>5,
	'l'=>1,
	'm'=>3,
	'n'=>1,
	'o'=>1,
	'p'=>3,
	'q'=>10,
	'r'=>1,
	's'=>1,
	't'=>1,
	'u'=>1,
	'v'=>4,
	'w'=>4,
	'x'=>10,
	'y'=>4,
	'z'=>8,
	'-'=>0,
);
$tile_freq = array(
	'a'=>9,
	'b'=>2,
	'c'=>2,
	'd'=>4,
	'e'=>12,
	'f'=>2,
	'g'=>3,
	'h'=>2,
	'i'=>9,
	'j'=>1,
	'k'=>1,
	'l'=>4,
	'm'=>2,
	'n'=>6,
	'o'=>8,
	'p'=>2,
	'q'=>1,
	'r'=>6,
	's'=>4,
	't'=>6,
	'u'=>4,
	'v'=>2,
	'w'=>2,
	'x'=>1,
	'y'=>2,
	'z'=>1,
//	'-'=>2,
);
//End Game Settings


$total_tiles = array_sum($tile_freq);

//Returns an array of random tiles for the player.
function get_player_tiles() {
	global $num_player_tiles;
	global $tile_freq;
	
	//generate numbered tile array from tile frequency array
	//e.g. [0]=>'a', [1]=>'a', [2]=>'b'...
	$tiles = array();
	foreach($tile_freq as $k=>$v) {
		while ($v>0) {
			$tiles[] = $k;
			$v--;
		}
	}
	
	//Choose random tiles from the numbered tile array
	$selected_tile_keys = array();
	$i=0;
	while ($i<$num_player_tiles){
		//get a random tile key
		$random_tile_key = rand(0, count($tiles)-1);
		//verify is original and add to selected array
		if(!in_array($random_tile_key, $selected_tile_keys)) {
			//is original
			$selected_tile_keys[] = $random_tile_key;
			$i++;
		}
	}
	
	//build output array of letters
	$output = array();
	foreach ($selected_tile_keys as $v) {
		$output[] = $tiles[$v];
	}
	return $output;
}

//Returns an array of valid words from the dictionary array that can be made with the input letters.
//Iterates through all letter combinations, starting with all the letters and working down.
function get_words_from_letters($letter_array) {
	//Include alphabetized dictionary
	//Dictionary courtesy of: http://www.freescrabbledictionary.com/enable.txt
	require_once('alpha_dict.php');
	
	$combinations = array();
	$matches = array();

	//alphabetize input array of letters
	sort($letter_array);
	$num_letters = count($letter_array);

	//The total number of possible combinations  
	$total = pow(2, $num_letters);

	//Loop through all combinations of letters in $letter_array and create $combinations array.
	$i=0;
	while($i<$total) {
		$j=0;
		$string = '';
		while ($j<$total) {
			if (pow(2, $j) & $i)
				$string .= $letter_array[$j];
			$j++;
		}
		if ($string != '')
			$combinations[] = $string;
		$i++;
	}

	
	//Check dictionary for combinations
	$combinations = array_unique($combinations);
	//check dictionary indexes to see if alphabetized letter string is a match. If so, add values to $matches array
	foreach ($combinations as $letter_string) {
		if(array_key_exists($letter_string, $dictionary)) {
			foreach($dictionary[$letter_string] as $v) {
				$matches[] = $v;
			}
		}		
	}
	
	
	//remove duplicate matches
	$matches = array_unique($matches);
	
	//Assign point values to each word	
	global $tile_points;
	$output = array();
	
	//loop through words and find out how many points each is worth, then reorder.
	foreach ($matches as $k=>$v) {
		$letters = str_split($v);
		$points = 0;
		foreach ($letters as $l) {
			$points += $tile_points[$l];
		}
		$output[$points] = $v;
		krsort($output);
	}

	//return array of matches.
	return $output;

}

if($_POST['generate']) {
	$letter_array = get_player_tiles();
	$player_letters = implode($letter_array);
}
elseif($_POST['get_words']) {
	//validate input
	$letters = strtolower($_POST['letters']);
	if(preg_match('/^[a-z]+$/',$letters)) {
		$player_letters = substr($letters, 0, 8);
		$words = get_words_from_letters(str_split($player_letters));
	} else {
	   echo 'illegal input. stick to letters.';
	}
	
}


?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<h2>Scrabble Letter Generator and Word Finder</h2>
		<form action="index.php" method="post">
			<input type="submit" value="Generate Letters" name="generate"></input>
		</form>
		<form action="index.php" method="post">
			<label for="letters">Letters:</label>
			<input type="text" id="letters" name="letters" value="<?php echo $player_letters;?>"></input>
			<input type="submit" value="Get Words!" name="get_words" />
		</form>

<?php
if (is_array($words)) {
	echo '<table><tr><td style="width:75px;">Points</td><td>Word</td></tr>';
	foreach($words as $k=>$v) {
		echo '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
	}
	echo '</table>';
}

?>
    </body>
</html>
