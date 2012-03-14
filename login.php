<?php	
	session_start();
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>YouLess - Energy Monitor</title>
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
		<link type="text/css" href="css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div id="topHeader"></div>
		<div id="header">
		
			<div id="logo">
				<span style="line-height:26px;float:left;"><span style="font-weight:bold;">YOU</span>LESS</span>
				<span style="font-size:13px;line-height:13px;float:left;">ENERGY MONITOR</span>
			</div>
					
		</div>
		<div id="container">
		
			<div id="loginForm">
				<form method="post" action="index.php">
				<table>
					<tr>
						<td>Gebruikersnaam:</td>
						<td><input type="text" name="user" size="20" /></td>
					</tr>
					<tr>
						<td>Wachtwoord:</td>
						<td><input type="password" name="pass" size="20" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="hidden" name="login" value="1" />
							<input id="loginSubmit" type="submit" value="Inloggen"/>
						</td>
					</tr>
				</table>

				</form>
			</div>

		</div>
	</body>
</html>