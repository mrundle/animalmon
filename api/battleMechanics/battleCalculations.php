<?php
session_start();
function moveTypeCalculation($team, $move){
	if($team == 'battleTeam1'){
		$foeTeam = 'battleTeam2';
	}
	else{
		$foeTeam = 'battleTeam1';
	}
	$selfAnimalmon = $_SESSION[$team]['currentAnimalmon'];
	$foeAnimalmon = $_SESSION[$foeTeam]['currentAnimalmon'];
	$moveAttributes = $_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$move];
	$selfAttributes = $_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['STATS'];
	$foeAttributes = $_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS'];

	$_SESSION['battleLog'] = $_SESSION['battleLog'] . $selfAnimalmon . " used " . $move . "...<br>";

	if($moveAttributes['TARGET'] == 'Foe'){
		hitCalculation($team, $moveAttributes['BASE_ACCURACY'], $selfAttributes['ACCURACY'], $foeAttributes['EVASION']);
	}
	else{
		hitCalculation($team, 100, 1, 1);
	}
	if($moveAttributes['BASE_DAMAGE'] != 0){
		$damage = damageCalculation($team, $moveAttributes['BASE_DAMAGE'], $moveAttributes['CRITICAL_HIT'], $selfAttributes['ATTACK'], $foeAttributes['DEFENSE'], $selfAttributes['SPEED'], $foeAttributes['SPEED']);

		$_SESSION['battleLog'] = $_SESSION['battleLog'] . "hit " . $foeAnimalmon . " for " . $damage . "!<br>";

		$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] -= $damage;
		if($_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] < 1) {
			$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] = 0;
		}
		$_SESSION[$team]['battleLog']['target'] = $moveAttributes['TARGET'];
		$_SESSION[$team]['battleLog']['effect'] = $moveAttributes['EFFECT'];
		$_SESSION[$team]['battleLog']['target2'] = $moveAttributes['TARGET2'];
		$_SESSION[$team]['battleLog']['effect2'] = $moveAttributes['EFFECT2'];
	}
	else{
		$_SESSION[$team]['battleLog']['move_type'] = 'status';
		$_SESSION[$team]['battleLog']['target'] = $moveAttributes['TARGET'];
		$_SESSION[$team]['battleLog']['effect'] = $moveAttributes['EFFECT'];
		$_SESSION[$team]['battleLog']['target2'] = $moveAttributes['TARGET2'];
		$_SESSION[$team]['battleLog']['effect2'] = $moveAttributes['EFFECT2'];
	}
}
function accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion){
	$finalAccuracy = ($statAccuracy/$statEvasion) * $baseAccuracy; //calculate the final accuracy
	return $finalAccuracy;
}
function damageCalculation($team, $baseDamage, $critChance, $statAttack, $statDefense, $originSpeed, $targetSpeed){
	$modifier = 1.0;
	if(critCalculation($critChance, $originSpeed, $targetSpeed) == TRUE){
		$modifier += 0.5;
		$_SESSION['battleLog'] = $_SESSION['battleLog'] . "CRITICAL HIT!!!<br>";
	}
	$finalDamage = (($statAttack/$statDefense) * $baseDamage) * $modifier;//calculate the final damage
	return $finalDamage;
}
function critCalculation($critChance, $originSpeed, $targetSpeed){
	if(rand() % 100 < ($critChance * ($originSpeed/$targetSpeed))){
		return TRUE;
	}
	else{
		return FALSE;
	}
}
function hitCalculation($team, $baseAccuracy, $statAccuracy, $statEvasion){
	if(rand() % 100 < accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion)){
		return TRUE;
	}
	else{
		$_SESSION['battleLog'] = $_SESSION['battleLog'] . "but missed!<br>";
		return FALSE;
	}
}
function powerPointCalculation($team, $move){
	$_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$move]['CURRENT_POWER_POINTS']--;
}
?>
