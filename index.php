<?php
	$SERVER = "localhost";
	$DBNAME = "sweetwaterDB";
	$USER = 'root';
	$PASSWORD = '';
	
	try{
		//Connection/error 
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
			$res = $res."<br>".$result['orderid']." ".$result['comments']." ".$result['shipdate_expected'];
		}
		return $res;
	}
	
	function htmlBuild($keywords, $filteredContent){
		
		$tabs = '<div class="tab">';
		$addTabs = "";
		foreach ($keywords as $keyword){
			$addTabs  = $addTabs .'<button class = "tabs" onclick="openTab"(event,\''.$keyword.'\')">'.$keyword.'</button>';
		}
		$tabs = $tabs.$addTabs.'</div>';
		
		
		
	}

	

?>