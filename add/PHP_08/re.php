<?php 

	
	//1.ajax  
	//json_encode(name)  json_decode()
	//'{name:'zhangsan',age:12}'
	//JSON.parse(res)

	//1.引入  require("url")  
	//2.实例化出一个smarty对象
	//3.配置
	
	//变量的输出
	$smarty -> assign("name",'php');
	$arr = array("apple"=>5,"orange"=>8);
	$smarty -> assign("arr",$arr)
	$smarty -> display("")

	//变量调节器
	//1.首字母大写
	//2.字符串拼接
	//3.date_format
	//4.default
	//5.upper  lower
	//6.escape
	//7.nl2br



 ?>

 {*$name*}
 {$arr['apple']}
 {$arr.orange}


