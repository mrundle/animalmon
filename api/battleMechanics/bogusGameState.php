<?php
session_start();
$_SESSION['battleTeam1']['currentAnimalmon'] = Hippopotamus;
$_SESSION['battleTeam2']['currentAnimalmon'] = Parasite;
print_r($_SESSION);
echo "<br>Testing Battles: <a href=http://csevm03.crc.nd.edu:8503/project/api/battleMechanics/battleStateText.html>CLICK HERE</a>";
?>
