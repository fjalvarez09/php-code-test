<?php
	$SERVER = "localhost";
	$DBNAME = "sweetwaterDB";
	$USER = 'root';
	$PASSWORD = '';
	
	try{
		//Connection/error 
		$conPDO = new PDO("mysql:host=$SERVER;dbname=$DBNAME", $USER, $PASSWORD);
		$conPDO -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//Statement Preparation
		$selQuery = $conPDO ->prepare("SELECT * FROM sweetwater_test WHERE  'comments' LIKE '%keyword%' VALUES (:keyword)");
		$selQuery -> bindParam(':keyword',$keyword);
		
		
		
		
	}catch(PDOException $err){
			echo $err->getMessage();
	}
	
	

	
	$res='';
	echo $res;
?>