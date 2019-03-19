<?php 
	$username = $_POST['username'];
	$password = $_POST['password'];

	//连接数据库
	$mysqli = new mysqli("localhost","root","","php");

	$mysqli -> query("set names utf8");

	$sql = "SELECT * FROM `reg` WHERE name = '$username'";

	$result = $mysqli -> query($sql);

	$data = $result -> fetch_assoc();

	if($data){
		if($data['password'] == md5($password)){
			//用户输入的密码跟数据库中的密码一致，可以登录
			echo 0;//表示登录成功
		}else{
			echo 1;//表示密码不正确
		}
	}else{
		echo 2;//表示用户不存在
	}
	
 ?>