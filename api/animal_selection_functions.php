<?php

    if (!isset($conn)) {
        require 'connect.php';
    }

    // Used to view list of animal names
    function view_animal_options() {
        // get access to db
        global $conn;
        
        $query_string = "select name from animal";
        $query = oci_parse($conn, $query_string);

        oci_execute($query);
        $results = array();
        oci_fetch_all($query, $results);
        return $results;        
    }

?>
