<?php
	include "settings.inc.php";
	include "database.class.php";
	include "session.inc.php";
	
	$db = new Database();
	$settings = $db->getSettings();

?>	
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>YouLess - Energy Monitor</title>
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
		<link type="text/css" href="css/style.css" rel="stylesheet" />
		<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
		<script type="text/javascript" src="js/highstock.src.js"></script>
		<script type="text/javascript" src="js/modules/exporting.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
	</head>
	<body>
		
		<div id="settingsOverlay">

			<form>
				<table>
					<tr>
						<td>Prijs per kWh:</td><td><input type="text" name="cpkwh" value="<?php echo $settings->cpkwh; ?>"/></td>
					</tr>
					<tr>
						<td>Admin wachtwoord:</td><td><input type="password" name="password" value=""/></td>
					</tr>
					<tr>
						<td>Bevestig admin wachtwoord:</td><td><input type="password" name="confirmpassword" value=""/></td>
					</tr>										
					<tr>
						<td><input type="submit" id="saveSettings" value="Opslaan"/></td><td><input type="button" id="hideSettings" value="Sluit"/></td>
					</tr>
				</table>
			</form>	

		</div>
		
		<div id="topHeader">
			<div id="settings"><a href="#" id="showSettings">Instellingen</a></div>
			<div id="logout"><a href="?logout=1">Logout</a></div>
		</div>
		<div id="header">
		
			<div id="logo">
				<span style="line-height:26px;float:left;"><span style="font-weight:bold;">YOU</span>LESS</span>
				<span style="font-size:13px;line-height:13px;float:left;">ENERGY MONITOR</span>
			</div>
		
			<div id="menu">
				<ul class="btn">
					<li class="selected"><a href="#" data-chart="live" class="showChart">Live</a></li>
					<li><a href="#" data-chart="day" class="showChart">Dag</a></li>
					<li><a href="#" data-chart="week" class="showChart">Week</a></li>
					<li><a href="#" data-chart="month" class="showChart">Maand</a></li>
				</ul>
			</div>
			
			<div id="cpkwhCounter" class="counter chart day week month" style="display:none;"></div>
			<div id="wattCounter" class="counter chart live"></div>
			<div id="kwhCounter" class="counter chart day week month" style="display:none;"></div>
		</div>
		<div id="container">
			
			<input type="text" id="datepicker" class="chart day week month" style="display:none;" value="<?php echo date("Y-m-d"); ?>">			
			<div id="history" class="chart day week month" style="height: 500px; min-width: 500px;display:none;"></div>
			<div id="live" class="chart live" style="height: 500px; min-width: 500px;"></div>

		</div>
	</body>
</html>
