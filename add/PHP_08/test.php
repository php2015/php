<?php 
	$mysqli = new mysqli("localhost","root","","php");
	$mysqli -> query("set names utf8");

	$sql = "SELECT * FROM `movies`";

	$result = $mysqli -> query($sql);

	$data = $result -> fetch_all(MYSQLI_ASSOC);

	echo json_encode($data);
 ?>