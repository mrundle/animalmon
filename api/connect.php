<?php
	$conn = oci_connect('animalmon','animalmon','xe');
	if(!$conn){
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		exit(1);
	}
?>
