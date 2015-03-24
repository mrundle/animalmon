<?php
function getAnimalmon($animalmon){
	// get access to the db
	global $conn;

	// fetch the animal types
	$query_string = "SELECT * FROM ANIMAL WHERE name = '" . $animalmon . "'";
	$query = oci_parse($conn, $query_string);
	oci_execute($query);
	$animal = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
	$animalInfo['TYPE'] = $animal['TYPE'];
	$animalInfo['TYPE2'] = $animal['TYPE2'];

	// fetch the animal moves
	$query_string = "SELECT * FROM ANIMAL_MOVES WHERE ANIMAL = '" . $animalmon . "'";
	$query = oci_parse($conn, $query_string);
	oci_execute($query);
	$animal_moves = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
	$animalInfo['MOVES'][$animal_moves['MOVE_1']] = NULL;
	$animalInfo['MOVES'][$animal_moves['MOVE_2']] = NULL;
	$animalInfo['MOVES'][$animal_moves['MOVE_3']] = NULL;
	$animalInfo['MOVES'][$animal_moves['MOVE_4']] = NULL;

	// fetch the move stats
	foreach ($animalInfo['MOVES'] as $move => $value){
		$query_string = "SELECT * FROM MOVES WHERE NAME = '" . $move . "'";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$move_stats = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
		$animalInfo['MOVES'][$move]['BASE_DAMAGE'] = $move_stats['BASE_DAMAGE'];
		$animalInfo['MOVES'][$move]['BASE_ACCURACY'] = $move_stats['BASE_ACCURACY'];
		$animalInfo['MOVES'][$move]['POWER_POINTS'] = $move_stats['POWER_POINTS'];
		$animalInfo['MOVES'][$move]['BASE_DAMAGE'] = $move_stats['BASE_DAMAGE'];
		$animalInfo['MOVES'][$move]['CRITICAL_HIT'] = $move_stats['CRITICAL_HIT'];
		$animalInfo['MOVES'][$move]['TARGET'] = $move_stats['TARGET'];
		$animalInfo['MOVES'][$move]['EFFECT'][$move_stats['EFFECT']] = $move_stats['EFFECT'];
		$animalInfo['MOVES'][$move]['TARGET2'] = $move_stats['TARGET2'];
		$animalInfo['MOVES'][$move]['EFFECT2'][$move_stats['EFFECT2']] = $move_stats['EFFECT2'];

		// fetch the effect descriptions
		$query_string = "SELECT * FROM EFFECTS WHERE NAME = '" . $move_stats["EFFECT"] . "'";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$effect = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
		$animalInfo['MOVES'][$move]['EFFECT'][$move_stats['EFFECT']] = $effect['DESCRIPTION'];

		$query_string = "SELECT * FROM EFFECTS WHERE NAME = '" . $move_stats["EFFECT2"] . "'";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$effect2 = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
		$animalInfo['MOVES'][$move]['EFFECT2'][$move_stats['EFFECT2']] = $effect2['DESCRIPTION'];
	}

	// fetch the animal stats
	$query_string = "SELECT * FROM STATS WHERE ANIMAL = '" . $animalmon . "'";
	$query = oci_parse($conn, $query_string);
	oci_execute($query);
	$animal_stats = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
	$animalInfo['STATS']['HEALTH'] = $animal_stats['HEALTH'];
	$animalInfo['STATS']['ATTACK'] = $animal_stats['ATTACK'];
	$animalInfo['STATS']['DEFENSE'] = $animal_stats['DEFENSE'];
	$animalInfo['STATS']['ACCURACY'] = $animal_stats['ACCURACY'];
	$animalInfo['STATS']['EVASION'] = $animal_stats['EVASION'];
	$animalInfo['STATS']['SPEED'] = $animal_stats['SPEED'];
	return $animalInfo;
}
function getSessionAnimalmon(){
	$teams = ["battleTeam1","battleTeam2"];
	foreach ($teams as $team){
		foreach ($_SESSION[$team] as $animalmon => $value){
			// get access to the db
			global $conn;

			// fetch the animal types
			$query_string = "SELECT * FROM ANIMAL WHERE name = '" . $animalmon . "'";
			$query = oci_parse($conn, $query_string);
			oci_execute($query);
			$animal = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
			$_SESSION[$team][$animalmon]['TYPE'] = $animal['TYPE'];
			$_SESSION[$team][$animalmon]['TYPE2'] = $animal['TYPE2'];

			// fetch the animal moves
			$query_string = "SELECT * FROM ANIMAL_MOVES WHERE ANIMAL = '" . $animalmon . "'";
			$query = oci_parse($conn, $query_string);
			oci_execute($query);
			$animal_moves = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
			$_SESSION[$team][$animalmon]['MOVES'][$animal_moves['MOVE_1']] = NULL;
			$_SESSION[$team][$animalmon]['MOVES'][$animal_moves['MOVE_2']] = NULL;
			$_SESSION[$team][$animalmon]['MOVES'][$animal_moves['MOVE_3']] = NULL;
			$_SESSION[$team][$animalmon]['MOVES'][$animal_moves['MOVE_4']] = NULL;
			echo print_r($animal_moves);

			// fetch the move stats
			foreach ($_SESSION[$team][$animalmon]['MOVES'] as $move => $value){
				$query_string = "SELECT * FROM MOVES WHERE NAME = '" . $move . "'";
				$query = oci_parse($conn, $query_string);
				oci_execute($query);
				$move_stats = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
				$_SESSION[$team][$animalmon]['MOVES'][$move]['BASE_DAMAGE'] = $move_stats['BASE_DAMAGE'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['BASE_ACCURACY'] = $move_stats['BASE_ACCURACY'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['POWER_POINTS'] = $move_stats['POWER_POINTS'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['CURRENT_POWER_POINTS'] = $move_stats['POWER_POINTS'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['BASE_DAMAGE'] = $move_stats['BASE_DAMAGE'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['CRITICAL_HIT'] = $move_stats['CRITICAL_HIT'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['TARGET'] = $move_stats['TARGET'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['EFFECT'][$move_stats['EFFECT']] = $move_stats['EFFECT'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['TARGET2'] = $move_stats['TARGET2'];
				$_SESSION[$team][$animalmon]['MOVES'][$move]['EFFECT2'][$move_stats['EFFECT2']] = $move_stats['EFFECT2'];
	
				// fetch the effect descriptions
				$query_string = "SELECT * FROM EFFECTS WHERE NAME = '" . $move_stats["EFFECT"] . "'";
				$query = oci_parse($conn, $query_string);
				oci_execute($query);
				$effect = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
				$_SESSION[$team][$animalmon]['MOVES'][$move]['EFFECT'][$move_stats['EFFECT']] = $effect['DESCRIPTION'];

				$query_string = "SELECT * FROM EFFECTS WHERE NAME = '" . $move_stats["EFFECT2"] . "'";
				$query = oci_parse($conn, $query_string);
				oci_execute($query);
				$effect2 = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
				$_SESSION[$team][$animalmon]['MOVES'][$move]['EFFECT2'][$move_stats['EFFECT2']] = $effect2['DESCRIPTION'];
			}

			// fetch the animal stats
			$query_string = "SELECT * FROM STATS WHERE ANIMAL = '" . $animalmon . "'";
			$query = oci_parse($conn, $query_string);
			oci_execute($query);
			$animal_stats = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
			$_SESSION[$team][$animalmon]['STATS']['MAX_HEALTH'] = $animal_stats['HEALTH'];
			$_SESSION[$team][$animalmon]['STATS']['HEALTH'] = $animal_stats['HEALTH'];
			$_SESSION[$team][$animalmon]['STATS']['ATTACK'] = $animal_stats['ATTACK'];
			$_SESSION[$team][$animalmon]['STATS']['DEFENSE'] = $animal_stats['DEFENSE'];
			$_SESSION[$team][$animalmon]['STATS']['ACCURACY'] = $animal_stats['ACCURACY'];
			$_SESSION[$team][$animalmon]['STATS']['EVASION'] = $animal_stats['EVASION'];
			$_SESSION[$team][$animalmon]['STATS']['SPEED'] = $animal_stats['SPEED'];
		}
	}
}
?>
