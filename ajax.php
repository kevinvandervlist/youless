<?php
include "settings.inc.php";
include "request.class.php";	
include "database.class.php";

session_start();

$request = new Request();
$db = new Database();
$settings = $db->getSettings();

if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != false)
{

	if(isset($_GET['a']) && $_GET['a'] == 'live')
	{
		echo $request->getLiveData();
	}
	elseif(isset($_GET['a']) && $_GET['a'] == 'day' && isset($_GET['date']))
	{	
		
		$sqlDate = $_GET['date'];
		
		// Get data from specific day
		$rows = $db->getSpecificDay($sqlDate);
		
		if(count($rows) == 0)
		{
		
			echo '{"ok": 0, "msg":"Geen data beschikbaar op deze datum"}';
		
		}
		else
		{
		
			$i=0;
			$kwh = 0;
			
			foreach($rows as $k)
			{
				$row = explode(",", $k->value);
				$total = count($row);
				
				$time = strtotime($k->time);
				
				$timeAr[$i][] = $time;
				$dataAr[$i] = $row;
				
				for($t=1;$t<$total;$t++)
				{
					$timeAr[$i][$t] = $timeAr[$i][$t-1] +  (int)$k->delta;
				}
				$i++;
				
				// Calculate used kwh
				foreach($row as $key => $val)
				{
					$kwh += ((int)str_replace("\"", "", $val) / 1000) / 60;
				}	
			}
			
			$timeStr = '';
			foreach($timeAr as $k)
			{
				$timeStr .= implode(",", $k);
			}
			
			// Create JS data string
			$i=0;
			$dataStr = '';
			
			foreach($dataAr as $k)
			{
				$dataStr .= ($i!=0 ? "," : "").implode(",", $k);
				$i++;
			}
			
			// Calculate price
			$price = $kwh * (float)$settings->cpkwh;	
			
			// Output data
			echo '{"ok": 1, "kwh": "'. number_format($kwh, 2, ',', '') .'", "price": "'. number_format($price, 2, ',', '') .'", "start": "'. $sqlDate .'", "val": "'. str_replace("\"", "", $dataStr) .'"}';	
		
		}
			
	}
	elseif(isset($_GET['a']) && $_GET['a'] == 'week' && isset($_GET['date']))
	{	
		
		$sqlDate = $_GET['date'];
		
		$week = date('W',strtotime($sqlDate));
		$year = date('Y',strtotime($sqlDate));
	
		$begin = date("Y-m-d", strtotime($year."W".$week));
		$end = date("Y-m-d", strtotime($year."W".$week)+(6*86400));
		
		
		// Get data from specific week
		$rows = $db->getSpecificRange($begin, $end);

		if(count($rows) == 0)
		{
		
			echo '{"ok": 0, "msg":"Geen data beschikbaar op deze datum"}';
		
		}
		else
		{
				
			$i=0;
			$kwh = 0;
			
			foreach($rows as $k)
			{
				$row = explode(",", $k->value);
				$total = count($row);
				
				$time = strtotime($k->time);
				
				$timeAr[$i][] = $time;
				$dataAr[$i] = $row;
				
				for($t=1;$t<$total;$t++)
				{
					$timeAr[$i][$t] = $timeAr[$i][$t-1] +  (int)$k->delta;
				}
				$i++;
				
				// Calculate used kwh
				foreach($row as $key => $val)
				{
					$kwh += ((int)str_replace("\"", "", $val) / 1000) / 60;
				}	
			}
			
			$timeStr = '';
			foreach($timeAr as $k)
			{
				$timeStr .= implode(",", $k);
			}
			
			// Create JS data string
			$i=0;
			$dataStr = '';
			
			foreach($dataAr as $k)
			{
				$dataStr .= ($i!=0 ? "," : "").implode(",", $k);
				$i++;
			}
			
			// Calculate price
			$price = $kwh * (float)$settings->cpkwh;
			
			// Output data
			echo '{"kwh": "'. number_format($kwh, 2, ',', '') .'", "price": "'. number_format($price, 2, ',', '') .'", "start": "'. $begin .'", "val": "'. str_replace("\"", "", $dataStr) .'"}';	
		}
	}
	elseif(isset($_GET['a']) && $_GET['a'] == 'month' && isset($_GET['date']))
	{	
		
		$sqlDate = $_GET['date'];
		
		$month = date('m',strtotime($sqlDate));
		
		$json = json_decode($request->getSpecificMonth($month), true);	
		
		$kwh = 0;
		
		$begin = date("Y-m-d", strtotime($json['tm']));
		foreach($json['val'] as $k => $v)
		{
			$json['val'][$k] = str_replace(",",".",$v);
			$kwh = $kwh + (float)$json['val'][$k];
		}
		$dataStr = implode(",", $json['val']);
		
		// Calculate price
		$price = $kwh * (float)$settings->cpkwh;
		
		// Output data
		echo '{"kwh": "'. number_format($kwh, 2, ',', '') .'", "price": "'. number_format($price, 2, ',', '') .'", "start": "'. $begin .'", "val": "'. $dataStr .'"}';	
	}
	elseif(isset($_GET['a']) && $_GET['a'] == 'saveSettings')
	{
		foreach($_POST as $k => $v)
		{
			$$k = $v;
			if($k != 'password' && $k != 'confirmpassword')
			{
				$db->updateSettings($k, $v);
			}
		}
	
		if($password != "" && $confirmpassword != "" && $password == $confirmpassword)
		{
			$db->updateLogin(sha1($password));
		}
	}
	else
	{
		echo "Error!";
	}
}
else
{
	echo "Login required!";
}
?>