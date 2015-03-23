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
			
			$secret = login($_POST['username'], $_POST['password']);

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

			$secret = create_user($_POST['username'], $_POST['password']);
			
			if($secret == NULL){
				$result['message'] = 'username already exists';
				$result['status'] = 'fail';
			}
			else{
				$result['secret'] = $secret;
			}

			break;
	
    	    case 'view_animal_options':
            
			// Make sure user secret exists
			if(!isset($_POST['secret'])){
				$result['message'] = 'secret not stored or passed correctly';
				$result['status'] = 'fail';
				break;
			}
			else if(!secret_exists($_POST['secret'])) {
				$result['message'] = 'secret ' . $_POST['secret'] .' does not exist in the database';
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

		case 'startgame':
			$result['message'] = 'you are trying to start a game';
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



















