<?php 
	//对变量进行 JSON 编码
	//json_encode()
	//对 JSON 格式的字符串进行解码
	//json_decode()
	
	$mysqli = new mysqli("localhost","root","","php");
	$mysqli -> query("set names utf8");

	$sql = "SELECT * FROM `movies` WHERE name='美人鱼'";

	$result = $mysqli -> query($sql);

	$data = $result -> fetch_assoc();

	// print_r($data);
	
	echo json_encode($data);




 ?>