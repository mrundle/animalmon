<?php

    if (!isset($conn)) {
        require 'connect.php';
    }

    // Used to view list of animal names
    function view_animal_options() {
        // get access to db
        global $conn;
        
        $query_string = "select name, img, health, attack, defense, accuracy, evasion, speed, type, type2 from animal, stats where animal.name = stats.animal";
        $query = oci_parse($conn, $query_string);

        oci_execute($query);
        $results = array();
        oci_fetch_all($query, $results);
        return $results;        
    }

    function list_animals(){
        // get access to db
        global $conn;
        $query_string = "select name from animal";
        $query = oci_parse($conn, $query_string);
        oci_execute($query);
        $reults = array();
        oci_fetch_all($query, $results);
        return $results;
    }

    // Used to view list of my teams
    function view_my_teams($username) {
        // get access to db
        global $conn; 

        $query_string = "select team_id, animal_1, animal_2, animal_3, animal_4, animal_5, animal_6 from teams where username = '" . $username . "'";
        $query = oci_parse($conn, $query_string);

        oci_execute($query);
        $results = array();
        oci_fetch_all($query, $results);
        return $results;        
    }



?>
