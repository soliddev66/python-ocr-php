<?php

	$img_file_name = 'FL-16-01-2021-05-11-56.jpg';
	$output = shell_exec("python main.py ".$img_file_name);
	$string = file_get_contents("data.json");
	$json_a = json_decode($string, true);

	$SunD = $json_a['SunD'];
	$EarthD = $json_a['EarthD'];
	$NNodeD = $json_a['NNodeD'];
	$SNodeD = $json_a['SNodeD'];
	$MoonD = $json_a['MoonD'];
	$MercuryD = $json_a['MercuryD'];
	$VenusD = $json_a['VenusD'];
	$MarsD = $json_a['MarsD'];
	$JupiterD = $json_a['JupiterD'];
	$SaturnD = $json_a['SaturnD'];
	$UranusD = $json_a['UranusD'];
	$NeptuneD = $json_a['NeptuneD'];
	$PlutoD = $json_a['PlutoD'];


	$SunP = $json_a['SunP'];
	$EarthP = $json_a['EarthP'];
	$NNodeP = $json_a['NNodeP'];
	$SNodeP = $json_a['SNodeP'];
	$MoonP = $json_a['MoonP'];
	$MercuryP = $json_a['MercuryP'];
	$VenusP = $json_a['VenusP'];
	$MarsP = $json_a['MarsP'];
	$JupiterP = $json_a['JupiterP'];
	$SaturnP = $json_a['SaturnP'];
	$UranusP = $json_a['UranusP'];
	$NeptuneP = $json_a['NeptuneP'];
	$PlutoP = $json_a['PlutoP'];

	echo '$SunD = '.$SunD."<br>";
	echo '$EarthD = '.$EarthD."<br>";
	echo '$NNodeD = '.$NNodeD."<br>";
	echo '$SNodeD = '.$SNodeD."<br>";
	echo '$MoonD = '.$MoonD."<br>";
	echo '$MercuryD = '.$MercuryD."<br>";
	echo '$VenusD = '.$VenusD."<br>";
	echo '$MarsD = '.$MarsD."<br>";
	echo '$JupiterD = '.$JupiterD."<br>";
	echo '$SaturnD = '.$SaturnD."<br>";
	echo '$UranusD = '.$UranusD."<br>";
	echo '$NeptuneD = '.$NeptuneD."<br>";
	echo '$PlutoD = '.$PlutoD."<br>";

	echo "<br>";

	echo '$SunP = '.$SunP."<br>";
	echo '$EarthP = '.$EarthP."<br>";
	echo '$NNodeP = '.$NNodeP."<br>";
	echo '$SNodeP = '.$SNodeP."<br>";
	echo '$MoonP = '.$MoonP."<br>";
	echo '$MercuryP = '.$MercuryP."<br>";
	echo '$VenusP = '.$VenusP."<br>";
	echo '$MarsP = '.$MarsP."<br>";
	echo '$JupiterP = '.$JupiterP."<br>";
	echo '$SaturnP = '.$SaturnP."<br>";
	echo '$UranusP = '.$UranusP."<br>";
	echo '$NeptuneP = '.$NeptuneP."<br>";
	echo '$PlutoP = '.$PlutoP."<br>";

?>