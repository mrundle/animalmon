<?php
	error_reporting(-1);
	ini_set('display_errors', 'On');

	require 'api/api_handler.php'; // Introduces API functions and connects to the db
	
	// Set up the result array that will be returned
	$result = array();
	$result['status'] = 'pass'; // the status should be pass/fail

	// Check POST data to see if a 'type' was specified in the API call
	if(!isset($_POST['type'])){
		$result['status'] = 'fail';
		$result['message'] = 'no type defined in post data';
		echo json_encode($result);
		exit(0);
	}

	// Handle the API call based on type
	switch($_POST['type']){

		case 'login':

			// Make sure username and password values are set in POST data
			if(!isset($_POST['username']) or !isset($_POST['password'])){
				$result['message'] = 'username and/or password not entered/set';
				$result['status'] = 'fail';
				break;
			}

			// Make sure username and password values are not blank
			if($_POST['username'] == '' or $_POST['password'] == ''){
				$result['message'] = 'enter values for both username and password';
				$result['status'] = 'fail';
				break;	
			}
		    
            // clean username and password
            $username = addslashes($_POST['username']);
            $password = addslashes($_POST['password']);
	
			$secret = login($username, $password);

			if($secret == NULL){
				$result['message'] = 'username or password invalid';
				$result['status'] = 'fail';
			}
			else{
				$result['secret'] = $secret;
			}

			break;

		case 'create_user':
			
			// Make sure username and password values are set in POST data
			if(!isset($_POST['username']) or !isset($_POST['password'])){
				$result['message'] = 'username and/or password not entered/set';
				$result['status'] = 'fail';
				break;
			}

			// Make sure username and password values are not blank
			if($_POST['username'] == "" or $_POST['password'] == ""){
				$result['message'] = 'enter values for both username and password';
				$result['status'] = 'fail';
				break;	
			}

            // clean username and password
            $username = addslashes($_POST['username']);
            $password = addslashes($_POST['password']);

			$secret = create_user($username, $password);
			
			if($secret == NULL){
				$result['message'] = 'username already exists';
				$result['status'] = 'fail';
			}
			else{
				$result['secret'] = $secret;
			}

			break;

    case 'authenticate':
            // Make sure secret is set
            if(!isset($_POST['secret'])){
                $result['message'] = 'secret not set';
                $result['status'] = 'fail';
                break;
            }            
            else if($_POST['secret'] == '' or empty($_POST['secret'])){
                $result['message'] = 'secret not set';
                $result['status'] = 'fail';
                break;
            }     

            // clean secret
            $secret = addslashes($_POST['secret']);

            // Validate secret
			$username = secret_exists($secret);
            if(is_null($username)){
                $result['message'] = 'invalid secret';
                $result['status'] = 'fail';
            }
            else{
                $result['status'] = 'pass';
                $result['username'] = $username;
            }
            break;
	
    case 'view_animal_options':
            
			// Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			
            // clean secret
            $secret = addslashes($_POST['secret']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}

			$animals = view_animal_options();
			if($animals == NULL){
				$result['message'] = 'failed to get animals';
				$result['status'] = 'fail';
			}
			else{
				$result['animals'] = $animals;
			}

			break;

    case 'find_match':
            
			// Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			
            // clean secret
            $secret = addslashes($_POST['secret']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}
            
			$match_results = find_match($secret);
			if($match_results == NULL){
				$result['message'] = 'find_match function call failed';
				$result['status'] = 'fail';
			}
			else{
				$result['match_results'] = $match_results;
			}

			break;

	case 'confirm_match':

			// Make sure user secret and match_id exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			} else if(!isset($_POST['match_id'])){
				$result['message'] = 'match id not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}

            // clean secret and match_id
            $secret = addslashes($_POST['secret']);
			$match_id = addslashes($_POST['match_id']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}
            
			$match_results = confirm_match($secret, $match_id);
			if($match_results == NULL){
				$result['message'] = 'confirm_match function call faileded';
				$result['status'] = 'fail';
			}
			else{
				$result['match_results'] = $match_results;
			}

			break;
 

		case 'startgame':
    		session_unset();
			// Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			
            // clean secret
            $secret = addslashes($_POST['secret']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}
      
			// Make sure animal input is set
      		if(!isset($_POST['animals'])){
        		$result['message'] = 'animals array not set';
        		$result['status'] = 'fail';
      		}
      		else if(count($_POST['animals']) != 6){
        		$result['message'] = 'need 6 animals';
        		$result['status'] = 'fail';
      		}
      
      		// Set the users animals
      		set_animals($_POST['animals']);
      		$result['status'] = 'pass';
      
      		break;

        # saves the battle team to the database. similar to start game, except the actual game creation logic is postponned until a match is made
		case 'multiplayer_make_team':
    		session_unset();
			// Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			
            // clean secret
            $secret = addslashes($_POST['secret']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}
      
			// Make sure animal input is set
      		if(!isset($_POST['animals'])){
        		$result['message'] = 'animals array not set';
        		$result['status'] = 'fail';
      		}
      		else if(count($_POST['animals']) != 6){
        		$result['message'] = 'need 6 animals';
        		$result['status'] = 'fail';
      		}
      
      		// Set the users animals
      		multiplayer_make_team($_POST['animals'], $secret);
      		$result['status'] = 'pass';
      
      		break;

		case 'update':
			$gameState = updateGameState();
			if($gameState == NULL){
				$result['message'] = 'Something went wrong';
				$result['status'] = 'fail';
			}
			else{
				$result['gameState'] = $gameState;
			}
			break;

		case 'move':
			$gameState = handleMove($_POST['move']);
			if($gameState == NULL){
				$result['message'] = 'Something went wrong';
				$result['status'] = 'fail';
			}
			else{
				$result['gameState'] = $gameState;
			}
			break;

		case 'swap':
			$gameState = handleSwap($_POST['animalmon']);
			if($gameState == NULL){
				$result['message'] = 'Something went wrong';
				$result['status'] = 'fail';
			}
			else{
				$result['gameState'] = $gameState;
			}
			break;

        case 'stats':
            // Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			
            // clean secret
            $secret = addslashes($_POST['secret']);

            if(!secret_exists($secret)) {
				$result['message'] = 'secret ' . $secret .' does not exist in the database';
				$result['status'] = 'fail';
				break;
			}
            
			$stats_results = user_stats($secret);
			if($stats_results == NULL){
				$result['message'] = 'user_stats function call failed';
				$result['status'] = 'fail';
			}
			else{
				$result['stats_results'] = $stats_results;
			}

            break;

		default:
			$result['message'] = 'unrecognized api call type';
			$result['status'] = 'fail';
	}


	// return the result 
	echo json_encode($result);

	// Close the DB connection 
	oci_close($conn);

	exit(0);
?>



















