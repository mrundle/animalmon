<?php 
    // CONNECT TO THE DATABASE
    require './api/connect.php';
?>
<!DOCTYPE html>
<head>

    <title>Animalmon</title>

    <!-- FONT -->
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link rel="stylesheet" href="dist/css/normalize.css">
    <link rel="stylesheet" href="dist/css/skeleton.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/animalmon.css">

    <!-- IMPORT JQUERY (from Google CDN)
    ----------------------------------------- -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- INCLUDE OTHER JAVASCRIPT FILES
    ----------------------------------------- -->
    <script type='text/javascript' src='js/debug.js'></script> <!-- GLOBAL DEBUG VARIABLE -->

    <script>
    $(document).ready(function(){

        // Authenticate
        // (And send back to index.html if not authenticated)
        var to_send = {};
        to_send.type = 'authenticate';
        to_send.secret = localStorage.getItem("secret");
        if(to_send.secret == ""){
            window.location = "index.html";
        }
        else{
            // make the POST request
            $.post('api.php',to_send)
            .done(function(data){    
                parsed_data = JSON.parse(data);
                if (parsed_data['status'] != 'pass'){
                    window.location = 'index.html';
                }
            });
        }
    
        $('#animalToggleBtn').click(function(){
			$('#animalTableDiv').slideToggle();
            if($('#animalToggleBtn').html() === "Animals: Show"){
                $('#animalToggleBtn').html('Animals: Hide');
            }
            else{
                $('#animalToggleBtn').html('Animals: Show');
            }
		});

        $('#movesToggleBtn').click(function(){
            $('#movesTableDiv').slideToggle();
            if($('#movesToggleBtn').html() === "Moves: Show"){
                $('#movesToggleBtn').html('Moves: Hide');
            }
            else{
                $('#movesToggleBtn').html('Moves: Show');
            }
        });

        $('#effectsToggleBtn').click(function(){
            $('#effectsTableDiv').slideToggle();
            if($('#effectsToggleBtn').html() === "Effects: Show"){
                $('#effectsToggleBtn').html("Effects: Hide");
            }
            else{
                $('#effectsToggleBtn').html('Effects: Show');
            }
        });

        $('#backBtn').click(function(){
            window.location = './home.html';
        });

    });
    </script>


</head>
<body>
<container>

    <!-- INCLDUE THE HEADER -->
    <script src="./js/header.js"></script>

    <center>

    <button id='backBtn'>Back to the Main Menu</button>

    <!-- ANIMALS TABLE -->
    <div>
        <button id='animalToggleBtn'>Animals: Hide</button>
    </div>
    <div id='animalTableDiv'>
    <table id='animalTable'>
        <?php
            $query_string = "select name, type, health as hp, attack as att, defense as def, accuracy as acc, evasion as eva, speed as spd, move_1, move_2, move_3, move_4 from animal, stats, animal_moves where animal.name = stats.animal and animal.name = animal_moves.animal order by animal.name";
            $query = oci_parse($conn,$query_string);
            oci_execute($query);
            $is_first = true;
            while (($row = oci_fetch_array($query, OCI_ASSOC)) != false) {
					if($is_first){
						$keys = array_keys($row);
						echo '<thead><tr>';
						foreach($keys as $key){
							echo '<td><b>' . $key . '</b></td>';	
						}
						echo '</tr></thead><tbody>';
						$is_first = false;
					}
    					// Use the uppercase column names for the associative array indices
					echo '<tr>';
    					foreach($keys as $key){
						if(array_key_exists($key,$row)){
							echo '<td>' . $row[$key] . '</td>';
						}
						else{
							echo '<td>-</td>';
						}			
					}
					echo '</tr>';
			}
            echo '</tbody>';
        ?>
    </table>
    </div>

    <!-- MOVES TABLE -->
    <div>
        <button id='movesToggleBtn'>Moves: Hide</button>
    </div>
    <div id='movesTableDiv'>
    <table id='movesTable'>
        <?php
            $query_string = "select name, base_damage as dmg, base_accuracy as acc, effect as effect_1, target, effect2 as effect_2, power_points as pp, target2, critical_hit as crit from moves order by name";
            $query = oci_parse($conn,$query_string);
            oci_execute($query);
            $is_first = true;
            while (($row = oci_fetch_array($query, OCI_ASSOC)) != false) {
					if($is_first){
						$keys = array_keys($row);
						echo '<thead><tr>';
						foreach($keys as $key){
							echo '<td><b>' . $key . '</b></td>';	
						}
						echo '</tr></thead><tbody>';
						$is_first = false;
					}
    					// Use the uppercase column names for the associative array indices
					echo '<tr>';
    					foreach($keys as $key){
						if(array_key_exists($key,$row)){
							echo '<td>' . $row[$key] . '</td>';
						}
						else{
							echo '<td>-</td>';
						}			
					}
					echo '</tr>';
			}
            echo '</tbody>';
        ?>
    </table>
    </div>

    
    <!-- EFFECTS TABLE -->
    <div>
        <button id='effectsToggleBtn'>Effects: Hide</button>
    </div>
    <div id='effectsTableDiv'>
    <table id='effectsTable'>
        <?php
            $query_string = "select name, stackable, description from effects";
            $query = oci_parse($conn,$query_string);
            oci_execute($query);
            $is_first = true;
            while (($row = oci_fetch_array($query, OCI_ASSOC)) != false) {
					if($is_first){
						$keys = array_keys($row);
						echo '<thead><tr>';
						foreach($keys as $key){
							echo '<td><b>' . $key . '</b></td>';	
						}
						echo '</tr></thead><tbody>';
						$is_first = false;
					}
    					// Use the uppercase column names for the associative array indices
					echo '<tr>';
    					foreach($keys as $key){
						if(array_key_exists($key,$row)){
							echo '<td>' . $row[$key] . '</td>';
						}
						else{
							echo '<td>-</td>';
						}			
					}
					echo '</tr>';
			}
            echo '</tbody>';
        ?>
    </table>
    </div>

    </center>


</container>
</body>
</html>
