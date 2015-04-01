<?php
	// connects to the db using animalmon/animalmon
	if(!isset($conn)){
		require 'connect.php';
	}

	// additional libraries go here
	require 'animal_selection_functions.php'; // function library related to selecting animals for your battle team
	require 'battleMechanics/battleCalculations.php'; //function library related to calculating everything that happens when a move is clicked
	require 'battleMechanics/getAnimalmonInfo.php'; //function library related to getting all info related to animals

	// returns pw hash if user exists, else NULL
	function user_exists($username){
		// get access to the db
		global $conn;

		// check if the user exists
		$query_string = "SELECT pwdhash FROM PLAYER WHERE username = '" . $username . "'";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query, $results);
		if($num_results != 1){
			return NULL;
		}
		else{
			return $results['PWDHASH'][0];;
		}
	}

	// Checks if a secret exists in the PLAYER table of the database.
	// If so, returns the corresponding username.
	// If not, returns null.
	// USES: Checking secret existence, matching secret to a username.
	function secret_exists($secret){
		// get access to db
		global $conn;
	
		// check if the secret exists
		$query_string = "SELECT username FROM PLAYER WHERE secret = '" . (string)$secret . "'";// group by username";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query,$results);
		if(count($results['USERNAME']) != 1){
			return null;
		}
		else{
			return $results['USERNAME'][0];
		}
	}

	function update_secret($username,$secret){
		// get access to db
		global $conn;

		$query_string = "UPDATE PLAYER SET SECRET = '" . $secret . "' where username = '" .$username . "'";
		$query = oci_parse($conn, $query_string);
		$pass = oci_execute($query);
		return $pass;
	}

	// 
	function login($username,$password){
		// get access to the db
		global $conn;

		// check if user exists
		$pwdhash = user_exists($username);
		if($pwdhash == NULL){
			return NULL;
		}

		// verify the password
		if($pwdhash != (string)md5($password)){
			return NULL;
		}

		// generate random 'secret' ID, ensure it's unique
		$secret = (string)mt_rand();
		while(secret_exists($secret)){
			$secret = (string)mt_rand();
		}

		// update the users secret in the database
		if(!update_secret($username,$secret)){
			$secret = NULL;
		}

		// return the secret
		return $secret;
	}

	// returns secret upon success
	function create_user($username,$password){
		// get access to the db
		global $conn;

		// check if user exists
		if(user_exists($username) != NULL){
			return NULL;
		}

		$query_string = "INSERT INTO PLAYER (USERNAME, PWDHASH) VALUES ('" . $username . "','" . (string)md5($password) . "')";
		$query = oci_parse($conn, $query_string);
		$pass = oci_execute($query);

		if(!$pass){
			return NULL;
		}

		// generate random 'secret' ID, ensure it's unique
		$secret = (string)mt_rand();
		while(secret_exists($secret)){
			$secret = (string)mt_rand();
		}
		
		// update the users secret in the database
		if(!update_secret($username,$secret)){
			$secret = NULL;
		}

		// return the secret
		return $secret;
	}
  
  function set_animals($animals){
    foreach($animals as $animal){
      $_SESSION['battleTeam1'][$animal] = NULL; 
    }
	$_SESSION['battleTeam2'] = array('Bear'=>NULL, 'Parasite'=>NULL, 'Spider'=>NULL, 'Elk'=>NULL, 'Poison Frog'=>NULL, 'Falcon' => NULL);
	getSessionAnimalmon();
	$_SESSION['battleTeam2']['currentAnimalmon'] = 'Bear';
	$_SESSION['battleTeam1']['currentAnimalmon'] = $animal;
  }

	function updateGameState(){
		return $_SESSION;

	}

	function handleMove($move){
		$_SESSION['battleLog'] = '';
		if($_SESSION['battleTeam1'][$_SESSION['battleTeam1']['currentAnimalmon']]['STATS']['SPEED'] > $_SESSION['battleTeam2'][$_SESSION['battleTeam2']['currentAnimalmon']]['STATS']['SPEED']){
			$_SESSION['battleLog'] = '';
			moveTypeCalculation('battleTeam1', $move);
			powerPointCalculation('battleTeam1', $move);
			if($_SESSION['battleTeam1'][$_SESSION['battleTeam1']['currentAnimalmon']]['STATS']['HEALTH'] == 0)AISwapAnimalmon('battleTeam2');
			else AIMoveCalculation('battleTeam2');
		}
		else{
			AIMoveCalculation('battleTeam2');
			moveTypeCalculation('battleTeam1', $move);
			powerPointCalculation('battleTeam1', $move);
			if($_SESSION['battleTeam1'][$_SESSION['battleTeam1']['currentAnimalmon']]['STATS']['HEALTH'] == 0)AISwapAnimalmon('battleTeam2');
		}
		return $_SESSION;
	}

	function handleSwap($animalmon){
		$prevAnimalmon = $_SESSION['battleTeam1']['currentAnimalmon'];
		$_SESSION['battleTeam1']['currentAnimalmon'] = $animalmon;
		$_SESSION['battleLog'] = $animalmon . " was swapped in!<br>";
		if($_SESSION['battleTeam1'][$prevAnimalmon]['STATS']['HEALTH'] != 0){
			AIMoveCalculation('battleTeam2');
		}
		return $_SESSION;
	}

?>
