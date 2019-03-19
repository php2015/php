<?php 
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$email = $_POST['email'];

	$mysqli = new mysqli("localhost","root","","php");

	$mysqli -> query("set names utf8");

	$sql = "INSERT INTO `reg` VALUES (NULL,'$username','$password','$email','')";

	$result = $mysqli -> query($sql);

	if($result){
		echo 0;
	}else{
		echo 1;
	}
 ?>