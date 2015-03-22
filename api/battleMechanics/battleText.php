<!doctype html>
<body style="margin:0; padding: 0;">
Foe's animalmon:
<p id = "battleTeam2" ></p>
Select your starting animalmon:
<button type="button" id="1">1</button>
<button type="button" id="2">2</button>
<button type="button" id="3">3</button>
<button type="button" id="4">4</button>
<button type="button" id="5">5</button>
<button type="button" id="6">6</button>

<script type="text/javascript">
	var session = <?php echo json_encode($_SESSION, JSON_PRETTY_PRINT)?>;
	battleTeam1 = Object.keys(session['battleTeam1']);
	battleTeam2 = Object.keys(session['battleTeam2']);

	document.getElementById("battleTeam2").innerHTML = battleTeam2.join(", ");

	document.getElementById('1').style.visibility = 'hidden';
	document.getElementById('2').style.visibility = 'hidden';
	document.getElementById('3').style.visibility = 'hidden';
	document.getElementById('4').style.visibility = 'hidden';
	document.getElementById('5').style.visibility = 'hidden';
	document.getElementById('6').style.visibility = 'hidden';
	var battleTeam1Length = battleTeam1.length;
	for (var i = 0; i < battleTeam1Length; i++){
		document.getElementById(i+1).style.visibility = 'visible';
		document.getElementById(i+1).innerHTML = battleTeam1[i];
	}
</script>
</body>
</html>
