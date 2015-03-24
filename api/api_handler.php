<?php
	// connects to the db using animalmon/animalmon
	if(!isset($conn)){
		require 'connect.php';
	}

	// additional libraries go here
	require 'animal_selection_functions.php'; // function library related to selecting animals for your battle team
	require 'battleMechanics/battleCalculations.php'; //function library related to calculating everything that happens when a move is clicked

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

	function secret_exists($secret){
		// get access to db
		global $conn;
	
		// check if the secret exists
		$query_string = "SELECT count(*) FROM PLAYER WHERE secret = '" . (string)$secret . "'";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query,$results);
		
		if($results['COUNT(*)'][0] == 0){
			return false;
		}
		else{
			return true;
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

	function updateGameState(){
		return $_SESSION;

	}

	function handleMove($move){
		$_SESSION['battleTeam1']['battleLog']['action'] = 'move';
		$_SESSION['battleTeam1']['battleLog']['move'] = $move;
		moveTypeCalculation('battleTeam1');
		powerPointCalculation('battleTeam1');
		return $_SESSION;
	}

?>
