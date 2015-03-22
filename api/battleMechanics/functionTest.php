<?php
session_start();
session_unset();
$_SESSION['battleTeam1'] = array('Hippopotamus'=>NULL,'Spider'=>NULL);
$_SESSION['battleTeam2'] = array('Bear'=>NULL, 'Parasite'=>NULL);
echo "<br>Testing Sessions: <a href=http://csevm03.crc.nd.edu:8503/project/api/battleMechanics/getAnimalmonInfo.php>CLICK HERE</a>";
?>
