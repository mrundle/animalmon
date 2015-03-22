<?php
session_start();
function accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion){
	$finalAccuracy = ($statAccuracy/$statEvasion) * $baseAccuracy; //calculate the final accuracy
	return $finalAccuracy;
}
function damageCalculation($team, $baseDamage, $critChance, $statAttack, $statDefense){
	$modifer = 1.0;
	if(critCalculation($critChance) == TRUE){
		$modifier += 0.5;
		$_SESSION[$team]['battleLog']['crit'] = TRUE;
	}
	else{
		$_SESSION[$team]['battleLog']['crit'] = FALSE;
	}
	$finalDamage = (($statAttack/$statDefense) * $baseDamage) * $modifier;//calculate the final damage
	return $finalDamage;
}
function critCalculation($critChance){
	if(rand() % 100 < $critChance){
		return TRUE;
	}
	else{
		return FALSE;
	}
}
function hitCalculation($team, $baseAccuracy, $statAccuracy, $statEvasion){
	if(rand() % 100 < accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion)){
		$_SESSION[$team]['battleLog']['hit'] = TRUE;
		return TRUE;
	}
	else{
		$_SESSION[$team]['battleLog']['hit'] = FALSE;
		return FALSE;
	}
}
function powerPointCalculation($team, $move){
	$_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$move]['CURRENT_POWER_POINTS']--;
}
?>
