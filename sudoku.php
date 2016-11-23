<?php
	if(isset($_POST['num']))
	{
		$num = $_POST['num'];
		$ans = $_POST['num'];
		$debug = null;

		$starttime = explode(' ', microtime());
		$starttime = $starttime[1] + $starttime[0];

		// $ans = normal_crack($ans, $debug);
		$ans = crazy_crack($ans);

		$mtime = explode(' ', microtime());
		$totaltime = $mtime[0] + $mtime[1] - $starttime;
	}elseif(isset($_GET['load']) && $_GET['load'] == 'sample'){
		$sample[0] = array(
			array("5", "", "2", "", "6", "", "", "", ""),
			array("", "", "", "4", "", "", "", "8", "2"),
			array("", "", "", "", "", "2", "", "", "1"),
			array("", "", "", "8", "", "", "", "5", ""),
			array("", "", "3", "", "", "", "6", "", ""),
			array("", "7", "", "", "", "9", "", "", ""),
			array("8", "", "", "2", "", "", "", "", ""),
			array("2", "4", "", "", "", "1", "", "", ""),
			array("", "", "", "", "3", "", "2", "", "7")
		);

		$sample[1] = array(
			array("", "", "", "", "", "", "", "", "3"),
			array("1", "", "4", "", "", "", "", "", ""),
			array("", "", "", "", "6", "", "1", "8", "4"),
			array("", "", "", "3", "1", "", "", "4", ""),
			array("", "1", "2", "9", "", "8", "5", "3", ""),
			array("", "3", "", "", "2", "7", "", "", ""),
			array("9", "6", "8", "", "5", "", "", "", ""),
			array("", "", "", "", "", "", "2", "", "6"),
			array("5", "", "", "", "", "", "", "", "")
		);

		$sample[2] = array(
			array("6", "1", "", "", "", "", "5", "", ""),
			array("", "", "9", "7", "", "2", "", "", ""),
			array("2", "", "", "", "", "", "8", "", ""),
			array("", "4", "", "9", "6", "", "", "", ""),
			array("", "", "6", "", "3", "", "2", "", ""),
			array("", "", "", "", "2", "7", "", "4", ""),
			array("", "", "3", "", "", "", "", "", "5"),
			array("", "", "", "3", "", "9", "4", "", ""),
			array("", "", "4", "", "", "", "", "9", "8")
		);

		$sample[3] = array(
			array("3", "4", "", "", "", "", "", "", ""),
			array("9", "", "", "2", "", "", "", "", ""),
			array("6", "", "", "", "", "8", "9", "", ""),
			array("", "", "7", "", "8", "9", "", "2", ""),
			array("", "3", "", "", "2", "", "", "6", ""),
			array("", "9", "", "4", "3", "", "8", "", ""),
			array("", "", "4", "1", "", "", "", "", "2"),
			array("", "", "", "", "", "5", "", "", "8"),
			array("", "", "", "", "", "", "", "4", "5")
		);

		$num = $sample[rand()%4];
	}
	

	function set_impossible_block($row, $col, $array)
	{
		for($x=0; $x<9; $x++){
			$array[$row][$x] = false;
			$array[$x][$col] = false;
		}

		$row = intval($row / 3)*3;
		$col = intval($col / 3)*3;
		for($i=$row; $i<$row+3; $i++){
			for($j=$col; $j<$col+3; $j++){
				$array[$i][$j] = false;
			}
		}

		return $array;
	}

	function set_impossible($ans, $guest)
	{
		for($i=0; $i<9; $i++){
			for($j=0; $j<9; $j++){
				if($ans[$i][$j] == $guest)
				{
					$array = set_impossible_block($i, $j, $array);
				}

				if(!empty($ans[$i][$j]))
				{
					$array[$i][$j] = false;
				}
			}
		}

		for($i=0; $i<9; $i++){
			for($j=0; $j<9; $j++){

				if($array[$i][$j] === false)
					continue;

				$onelinerow = true;
				$onelinecol = true;
				$row = intval($i / 3)*3;
				$col = intval($j / 3)*3;
				for($x=$row; $x<$row+3; $x++){
					for($y=$col; $y<$col+3; $y++){

						if($i != $x && $array[$x][$y] !== false){
							$onelinerow = false;
						}

						if($j != $y && $array[$x][$y] !== false){
							$onelinecol = false;
						}

					}
				}

				if($onelinerow == true){
					$m = intval($j / 3)*3;
					for($n=0; $n<9; $n++){
						if(!($n >= $m && $n <= $m+2)){
							$array[$i][$n] = false;
						}
					}
				}

				if($onelinecol == true){
					$m = intval($i / 3)*3;
					for($n=0; $n<9; $n++){
						if(!($n >= $m && $n <= $m+2))
							$array[$n][$j] = false;
					}
				}
			}
		}
		return $array;
	}

	function normal_crack($ans, &$debug = null)
	{
		do{
			$found = false;
			for($guest=1; $guest<=9; $guest++){
				
				// Check impossibble
				$array = set_impossible($ans, $guest);	

				// Debug
				if(isset($_GET['debug']) && $_GET['debug'] == $guest){
					$debug = $array;
					// break 2;					
				}

				for($i=0; $i<9; $i++){
					for($j=0; $j<9; $j++){
						if($array[$i][$j] !== false)
						{
							$duplicate = false;

							$row = intval($i / 3)*3;
							$col = intval($j / 3)*3;
							for($x=$row; $x<$row+3; $x++){
								for($y=$col; $y<$col+3; $y++){
									if($array[$x][$y] !== false && !($x == $i && $y == $j))
									{	
										$duplicate = true;
										break 2;
									}
								}
							}

							if($duplicate == false){
								$ans[$i][$j] = $guest;
								$found = true;
							}
							
						}
					}
				}
			}

		}while($found);

		return $ans;
	}

	function crazy_crack($ans, $row = null, $col = null, $guest = null)
	{
		if(isset($row) && isset($col) && isset($guest)){
			$ans[$row][$col] = $guest;
		}

		$ans = normal_crack($ans);
		for($i=0; $i<9; $i++){
			for($j=0; $j<9; $j++){
				if($ans[$i][$j] != "") continue;
				for($guest=1; $guest<=9; $guest++){
					$array = set_impossible($ans, $guest);
					if($array[$i][$j] === false) continue;
					$result = crazy_crack($ans, $i, $j, $guest);
					if($result != false)
						return $result;
				}
				return false;
			}
		}

		return $ans;
	}

	// 00 01 02  03 04 05  06 07 08
	// 10 11 12  13 14 15  16 17 18
	// 20 21 22  23 24 25  26 27 28
	//
	// 30 31 32  33 34 35  36 37 38
	// 40 41 42  43 44 45  46 47 48
	// 50 51 52  53 54 55  56 57 58
	//
	// 60 61 62  63 64 65  66 67 68
	// 70 71 72  73 74 75  76 77 78
	// 80 81 82  83 84 85  86 87 88
?>
<html>
<head>
	<title>Suduku</title>
	<style type="text/css">
	body{
		font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	}
	#box{
		width: 406px;
		margin: 0 auto;
		text-align: center;
	}
	table{
	}
	.num{
		width: 40px;
		height: 40px;
		line-height: 40px;
		font-size: 20px;
		text-align: center;
	}
	.debug{
		background-color: #FFC3B7;
		border: 1px solid grey;
	}
	.original{
		background-color: #EEE;
		border: 1px solid grey;
	}
	button{
		margin: 15px 0;
		width: 100%;
		line-height: 35px;
		padding: 0;
	}
	a{
		font-size: 12px;
		text-decoration: none;
	}
	</style>
</head>
<body>
<div id="box">
<h1>Sudoku Crack</h1>
<form method="post">
<table>
	<?php
	for($i=0; $i<9; $i++){
		if($i==3 || $i==6) echo '<tr><td colspan="11"></td></tr>';
		echo '<tr>';
		for($j=0; $j<9; $j++){
			if($j==3 || $j==6) echo '<td></td>';
			echo '<td><input type="text" name="num['.$i.']['.$j.']" class="num'.(!empty($num[$i][$j]) ? ' original' : '').' '.(isset($debug[$i][$j]) && $debug[$i][$j] === false ? ' debug' : '').'" value="'.(isset($ans[$i][$j]) ? $ans[$i][$j] : @$num[$i][$j]).'" /></td>';
		}
		echo '</tr>';
	}
	?>
</table>
<button type="submit">CRACK NOW</button>
<small>Sudoku cracked in <b><?php printf('%.3f', $totaltime); ?></b> seconds.</small>
<p><a href="?load=sample">Load sample</a></p>
</form>
</div>

</body>
</html>
