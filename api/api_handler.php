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


	// multiplayer functions
    
    // returns username given secret
    function get_username($secret){
		
		// get access to the db
		global $conn;
		

		$query_string = "select username from player where secret = '" . $secret . "'";
		$query = oci_parse($conn, $query_string);
		$pass = oci_execute($query);
		$num_results = oci_fetch_all($query, $results);
		if($num_results != 1){
			return NULL;
		}
		else{
			return $results['USERNAME'][0];;
		}	
		
	}
	
	// finds a match
	function find_match($secret){
		
		// get access to the db
		global $conn;

		// add user to "waiting" table
	    $username = get_username($secret);
		if ($username == NULL) {
			$results['match_status'] = "ERROR: username not found matching secret.";
			return $results;
		}
		$query_string = "insert into waiting (username, creation_time) values ('" . $username . "',LOCALTIMESTAMP)";
		$query = oci_parse($conn, $query_string);
		$pass = oci_execute($query);
		if(!$pass){
			return NULL;
		}

		// look for match in waiting table
		// current time difference set to 30 seconds for testing purposes
		// ultimately will be 2 seconds
		$query_string = "select w.username as USERNAME from waiting w where w.username != '" . $username . "' and (extract(second from LOCALTIMESTAMP-w.creation_time)) < 2";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query, $query_results);
		if ($num_results < 1){
			$results['match_status'] = "not_found";
		} else {
			$player2_username = $query_results['USERNAME'][0];
			$match_id = create_match($username, $player2_username);
			$results['match_status'] = "found";
			$results['match_id'] = $match_id;
		}

		return $results;
	}

	// checks for a current match. If none found, creates one
	function create_match($username, $other_username){

		// get access to teh db
		global $conn;
		
		// Get my team id from $_SESSION so I can insert the data
		// $my_team_id = $_SESSION['my_team_id'];
		$my_team_id = 1;
	
		// check to see if you have already created the match
		$query_string = "select * from matches where player1_username = '" . $username . "' and (extract(second from LOCALTIMESTAMP-creation_time)) < 4";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query, $results);
		if ($num_results > 0) {
			return $results['MATCH_ID'][0];
		}
	
		// check for a current match
		// current matches are matches made within the last 4 seconds (two 2-second periods)
		$query_string = "select * from matches where player2_username = '" . $username . "' and (extract(second from LOCALTIMESTAMP-creation_time)) < 4";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query, $results);
		if ($num_results < 1) {
			
			// create match
			$query_string = "insert into matches(player1_username, player2_username, player1_team, creation_time) values ('" . $username . "','" . $other_username . "'," . $my_team_id . ",LOCALTIMESTAMP)";
			$query = oci_parse($conn, $query_string);
			oci_execute($query);
			
			// grab match id
			$query_string = "select match_id from matches where player1_username = '" . $username . "' and player2_username = '" . $other_username . "' and extract(second from LOCALTIMESTAMP-creation_time) < 2";
			$query = oci_parse($conn, $query_string);
			oci_execute($query);
			oci_fetch_all($query, $results);
			return $results['MATCH_ID'][0];

		} else {
			
			// add team to row to join match
			$match_id = $results['MATCH_ID'][0];
			$query_string = "update matches set player2_team = " . $my_team_id . " where match_id = " . $match_id;
			$query = oci_parse($conn, $query_string);
			$pass = oci_execute($query);
			return $match_id;	
	
		}
	}

	// confirms a match (used after the ready button is pressed)
	function confirm_match($secret, $match_id){

		// get access to the db
		global $conn;

		// add user to "waiting" table
	    $username = get_username($secret);
		if ($username == NULL) {
			$results['match_status'] = "ERROR: username not found matching secret.";
			return $results;
		}

		// look for match_id in match table
		$query_string = "select * from matches where match_id = '" . $match_id . "' and player1_team is not NULL and player2_team is not NULL";
		$query = oci_parse($conn, $query_string);
		oci_execute($query);
		$num_results = oci_fetch_all($query, $query_results);
		if ($num_results < 1){
			$results['match_status'] = "not_found";
		} else {
			$results['match_status'] = "found";
		}

		return $results;
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
			if($_SESSION['battleTeam2'][$_SESSION['battleTeam2']['currentAnimalmon']]['STATS']['HEALTH'] == 0){
				AISwapAnimalmon('battleTeam2');
			}
			else {
				AIMoveCalculation('battleTeam2');
			}
		}
		else{
			AIMoveCalculation('battleTeam2');
			moveTypeCalculation('battleTeam1', $move);
			powerPointCalculation('battleTeam1', $move);
			if($_SESSION['battleTeam2'][$_SESSION['battleTeam2']['currentAnimalmon']]['STATS']['HEALTH'] == 0){
				AISwapAnimalmon('battleTeam2');
			}
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
