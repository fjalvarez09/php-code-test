<?php
	$SERVER = "localhost";
	$DBNAME = "sweetwaterDB";
	$USER = 'root';
	$PASSWORD = '';
	
	try{ 
		$conPDO = new PDO("mysql:host=$SERVER;dbname=$DBNAME", $USER, $PASSWORD);
		$conPDO -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		/* Note
			Made array for ease of adding new keywords to filter by. This way it is easier to add/remove filter criteria.
			Also, considered using a union query for keywords "call me" and "don't call me", but upon viewing some of the results with the general "call" as the query 
			(like for example a comment that said do not call vs don't, or anbother that said just please call), I believe this would be a better filter.
		*/
		$filters = array('candy', 'call', 'referred', 'signature');
		$segments = count($filters);
		$section = [];
		for($i=0;$i<$segments;$i++){			
			$filter = $filters[$i];
			$section[] = dbSelect($conPDO, $filter, false);
		}
		$section[] = dbSelect($conPDO, $filters, true);
		
		if (isset($_POST['update'])) {
			addShipDate($conPDO);
			htmlBuild($filters,$section, false);
		}else{
			//Build html 
			htmlBuild($filters,$section, true);
		}

	}catch(PDOException $err){
			echo $err->getMessage();
	}
		
	function dbSelect($connection,$commentSearch, $isInverse){
		if($isInverse){
			$querStr = "";
			$total = count($commentSearch);
			for($i = 0; $i<$total-1; $i++){
				$querStr = $querStr." NOT LIKE '%".$commentSearch[$i]."%' AND comments";
			}
			$querStr = $querStr." NOT LIKE '%".$commentSearch[$total-1]."%'";
			$querStr = "SELECT * FROM sweetwater_test WHERE comments".$querStr;
			$selQuery = $connection ->prepare($querStr);		
		}else{
			$selQuery = $connection ->prepare("SELECT * FROM sweetwater_test WHERE comments LIKE '%$commentSearch%'");
		}
		
		$selQuery -> execute();
		$res = "";
		foreach ($selQuery as $result){
			$res .= "<tr>";
			$res .= "<td>".$result['orderid']."</td>";
			$res .= "<td>".$result['comments']."</td>";
			$res .= "<td>".$result['shipdate_expected']."</td>";
			$res .= "</tr>";
		}
		$conPDO = null;
		return $res;
	}
	
	function htmlBuild($keywords, $filteredContent, $tableUpdated){
		//Head
		$htmlHead = '';
		$htmlHead .='<!doctype html>';
		$htmlHead .='<html lang="en">';
		$htmlHead .='<head>';
		$htmlHead .='<meta charset="utf-8">';
		$htmlHead .='<meta name="viewport" content="width=device-width, initial-scale=1">';
		$htmlHead .='<link rel="stylesheet" type="text/css" href="css.css">';
		$htmlHead .='<script src="js.js"></script>';
		$htmlHead .='</head>';
		$htmlHead .='<body>';
		
		//Menu
		$tabs = '<div class="tab">';
		$addTabs = "";
		foreach ($keywords as $keyword){
			$addTabs  = $addTabs .'<button class = "tabs" onclick="openTab(event,\''.$keyword.'\')">'.$keyword.'</button>';
		}
		$addTabs .= '<button class = "tabs" onclick="openTab(event,\'misc\')">Misc</button>';
		$addTabs .= '<button class = "tabs" onclick="openTab(event,\'populateDates\')">Populate Dates</button>';
		$tabs = $tabs.$addTabs.'</div>';
		
		//View Body
		$contBody='';
		for($i=0;$i<count($keywords)+1; $i++){
			$tabID = "";
			if ($i == count($keywords)){
				$tabID = "misc";
			}else{
				$tabID = $keywords[$i];
			}
			$contBody .= '<div id= "'.$tabID.'" class = "tabContent">'; 
			$contBody .= '<table>';
			$contBody .= '<tr>';
			$contBody .= '<th style="width:20%">OrderId</th>';
			$contBody .= '<th style="width:70%">Comments</th>';
			$contBody .= '<th>Shipdate_expected</th>';
			$contBody .= '</tr>';
			$contBody .= $filteredContent[$i];
			$contBody .= '</table>';	
			$contBody .= '</div>';
		}
		
			$contBody .= '<div id= "populateDates" class = "tabContent">'; 
			if($tableUpdated){
				$contBody .= '<div id="updatedRecs"> Update Records with Expected shipdate in comments?';
				$contBody .= '<form method = "post">';
				$contBody .= '<button type="submit" name="update" onclick="updateDB()">Submit</button>';
				$contBody .= '</form>';
				$contBody .= '</div>';	
			}else{
				$contBody .= '<div id="updatedRecs"> Records Updated!';
				$contBody .= '</div>';	
			}
			
			$contBody .= '</div>';
		
		//Footer
		$htmlEnd = '';
		$htmlEnd .= '</body>';
		$htmlEnd .= '</html>';
		
		echo $htmlHead.$tabs.$contBody.$htmlEnd;
	}

	function addShipDate($connection){
		$querStr = "";
		$querStr = "SELECT * FROM sweetwater_test WHERE comments LIKE '%Expected Ship Date%'";
		$selQuery = $connection ->prepare($querStr);
		$selQuery ->execute();
		$count = 0;
		
		foreach ($selQuery as $results){
			$sDate = explode("Expected Ship Date: ",$results['comments']);
			$parseDate = substr($sDate[1],0,8);
			$tDate = strtotime($parseDate);
			$date = date("Y-m-d H:i:s", $tDate);
			$id = $results['orderid'];
			$querStr = "UPDATE sweetwater_test SET shipdate_expected = '$date'  WHERE orderid = '$id'";
			$insQuery = $connection ->prepare($querStr);
			$insQuery ->execute();
			$count ++;
		}
		return $count;
	}

?>