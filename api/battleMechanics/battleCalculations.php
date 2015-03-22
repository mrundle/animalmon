<?php
session_start();
function accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion){
	$finalAccuracy = ($statAccuracy/$statEvasion) * $baseAccuracy; //calculate the final accuracy
	return $finalAccuracy;
}
function damageCalculation($baseDamage, $statAttack, $statDefense){
	$finalDamage = ($statAttack/$statDefense) * $baseDamage;//calculate the final damage
	return $finalDamage;
}
function powerPointCalculation($team, $move){
	$_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$move]['CURRENT_POWER_POINTS']--;
}
?>
