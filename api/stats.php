<?php

    // If the database connection isn't set, set it
    if (!isset($conn)) {
        require 'connect.php';
    }

    // Return a player's game stats (and username) given a secret
    function user_stats($secret){
        
        // get access to db
        global $conn;
        
        $query_string = "select username, num_wins, num_loses from player where secret = :secret_bv";        
        $query = oci_parse($conn, $query_string);
        oci_bind_by_name($query, ":secret_bv", $secret);

        oci_execute($query);
        $results = array();
        oci_fetch_all($query, $results);
        return $results;    
    }
    
?>
