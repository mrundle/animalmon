<?php
session_start();
function moveTypeCalculation($team){
	if($team == 'battleTeam1'){
		$foeTeam = 'battleTeam2';
	}
	else{
		$foeTeam = 'battleTeam1';
	}
	$moveAttributes = $_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$_SESSION[$team]['battleLog']['move']];
	$selfAttributes = $_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['STATS'];
	$foeAttributes = $_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS'];
	if($moveAttributes['TARGET'] == 'Foe'){
		if(hitCalculation($team, $moveAttributes['BASE_ACCURACY'], $selfAttributes['ACCURACY'], $foeAttributes['EVASION'])){
			if($moveAttributes['BASE_DAMAGE'] != 0){
				$_SESSION[$team]['battleLog']['move_type'] = 'damage';
				$damage = damageCalculation($team, $moveAttributes['BASE_DAMAGE'], $moveAttributes['CRITICAL_HIT'], $selfAttributes['ATTACK'], $foeAttributes['DEFENSE']);
				$_SESSION[$team]['battleLog']['damage'] = $damage;
				$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] -= $damage;
				if($_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] < 1) {
					$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] = 0;
				}
			}
		}
	}
}
function accuracyCalculation($baseAccuracy, $statAccuracy, $statEvasion){
	$finalAccuracy = ($statAccuracy/$statEvasion) * $baseAccuracy; //calculate the final accuracy
	return $finalAccuracy;
}
function damageCalculation($team, $baseDamage, $critChance, $statAttack, $statDefense){
	$modifier = 1.0;
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
function powerPointCalculation($team){
	$_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES'][$_SESSION[$team]['battleLog']['move']]['CURRENT_POWER_POINTS']--;
}
?>
