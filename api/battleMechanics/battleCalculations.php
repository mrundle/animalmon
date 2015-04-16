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
		$hit = hitCalculation($team, $moveAttributes['BASE_ACCURACY'], $selfAttributes['ACCURACY'], $foeAttributes['EVASION']);
	}
	else{
		$hit = hitCalculation($team, 100, 1, 1);
	}
	if($moveAttributes['BASE_DAMAGE'] != 0 && $hit){
		$damage = damageCalculation($team, $moveAttributes['BASE_DAMAGE'], $moveAttributes['CRITICAL_HIT'], $selfAttributes['ATTACK'], $foeAttributes['DEFENSE'], $selfAttributes['SPEED'], $foeAttributes['SPEED']);

		$_SESSION['battleLog'] = $_SESSION['battleLog'] . $move . " hit " . $foeAnimalmon . " for " . $damage . "!<br>";

		$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] -= $damage;
		$target = $moveAttributes['TARGET'];
		$effect = key($moveAttributes['EFFECT']);
		$target2 = $moveAttributes['TARGET2'];
		$effect2 = key($moveAttributes['EFFECT2']);
	}
	else{
		$target = $moveAttributes['TARGET'];
		$effect = key($moveAttributes['EFFECT']);
		$target2 = $moveAttributes['TARGET2'];
		$effect2 = key($moveAttributes['EFFECT2']);
	}
	statusCalculations($target, $effect, $target2, $effect2, $target2, $team, $foeTeam, $selfAnimalmon, $foeAnimalmon, $move, $hit);
	if($_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] < 1) {
		$_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] = 0;
		$_SESSION['battleLog'] = $_SESSION['battleLog'] . $foeAnimalmon . " fainted!<br>";
	}
	$_SESSION['battleLog'] = $_SESSION['battleLog'] . "<br><br>";

}
function statusCalculations($target, $effect, $target2, $effect2, $target2, $team, $foeTeam, $selfAnimalmon, $foeAnimalmon, $move, $hit){
	if($effect != null && $hit && $_SESSION[$foeTeam][$_SESSION[$foeTeam]['currentAnimalmon']]['STATS']['HEALTH'] > 0){
		if($target == "Self") $target = $selfAnimalmon;
		else $target = $foeAnimalmon;
		if($target2 == "Self") $target2 = $selfAnimalmon;
		else $target2 = $foeAnimalmon;
		if($effect == 'Gathering Power'){
			$_SESSION[$team][$selfAnimalmon]['MOVES'][$move]['BASE_DAMAGE'] += 5;
			$_SESSION['battleLog'] = $_SESSION['battleLog'] . $move . " just got stronger!<br>";
		}
		else{
			statusSwitchCase($team, $foeTeam, $target, $effect, $selfAnimalmon, $foeAnimalmon);
			$_SESSION['battleLog'] = $_SESSION['battleLog'] . $target . " is now " . $effect . "!<br>";
		}
		if($effect2 != null){
			if($effect2 == 'Gathering Power'){
				$_SESSION[$team][$selfAnimalmon]['MOVES'][$move]['BASE_DAMAGE'] += 5;
				$_SESSION['battleLog'] = $_SESSION['battleLog'] . $move . " just got stronger!<br>";
			}
			else{
				statusSwitchCase($team, $foeTeam, $target2, $effect2, $selfAnimalmon, $foeAnimalmon);
				$_SESSION['battleLog'] = $_SESSION['battleLog'] . $target2 . " is now " . $effect2 . "!<br>";
			}
		}
	}	
}
function statusSwitchCase($team, $foeTeam, $target, $effect){
	switch($effect){
		case "Intimidation":
			$_SESSION[$foeTeam][$target]['STATS']['ATTACK'] *= 0.75;
			$_SESSION[$foeTeam][$target]['STATS']['DEFENSE'] *= 0.75;
			break;
		case "Poisoned":
			break;
		case "Hibernation":
			break;
		case "Stealth":
			break;
		case "Sure Footed":
			$_SESSION[$team][$target]['STATS']['SPEED'] *= 1.25;
			$_SESSION[$team][$target]['STATS']['EVASION'] *= 1.25;
			break;
		case "Slowed":
			$_SESSION[$foeTeam][$target]['STATS']['SPEED'] *= 0.75;
			$_SESSION[$foeTeam][$target]['STATS']['EVASION'] *= 0.75;
			break;
		case "Blinded":
			$_SESSION[$foeTeam][$target]['STATS']['ACCURACY'] *= 0.50;
			break;
		case "Death":
			$_SESSION[$foeTeam][$target]['STATS']['HEALTH'] = 0;
			$_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['STATS']['HEALTH'] = 0;
			break;
		case "Infected":
			break;
		case "Bleeding":
			break;
		case "Hasted":
			$_SESSION[$team][$target]['STATS']['SPEED'] *= 1.50;
			break;
		case "Brave":
			$_SESSION[$team][$target]['STATS']['ATTACK'] *= 1.50;
			break;
		case "Enraged":
			$_SESSION[$team][$target]['STATS']['EVASION'] *= 0.75;
			$_SESSION[$team][$target]['STATS']['DEFENSE'] *= 0.75;
			break;
		case "Focused":
			$_SESSION[$team][$target]['STATS']['ACCURACY'] *= 1.50;
			break;			
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

function AIMoveCalculation($team){
	moveTypeCalculation($team, array_rand($_SESSION[$team][$_SESSION[$team]['currentAnimalmon']]['MOVES']));
}

function AISwapAnimalmon($team){
	$deadAnimalmon = $_SESSION[$team]['currentAnimalmon'];
	$animalmonArray = array_keys($_SESSION[$team]);
	unset($animalmonArray[array_search('currentAnimalmon', $animalmonArray)]);
	foreach($animalmonArray as $animalmon){
		if($_SESSION[$team][$animalmon]['STATS']['HEALTH'] > 0){
			$_SESSION[$team]['currentAnimalmon'] = $animalmon;
			$_SESSION['battleLog'] = $_SESSION['battleLog'] . $animalmon . " was swapped in!<br>";
			return;
		}
	}
	$_SESSION['battleLog'] = $_SESSION['battleLog'] . "YOU WIN!!!!!!!!!!<br>";
	$_SESSION['winStatus'] = "WIN";
}
?>
