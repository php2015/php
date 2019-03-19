<?php 
	//引入文件
	require("../libs/Smarty.class.php");
	//实例化对象
	$smarty = new Smarty();
	//配置
	$smarty -> left_delimiter = "{";
	$smarty -> right_delimiter = "}";
	$smarty -> template_dir = "tpl";
	$smarty -> compile_dir = "template_c";
	$smarty -> cache_dir = "cache";

	$smarty -> assign("name","python");

	$smarty -> assign("score",59);

	$smarty -> assign("age",20);

	$smarty -> assign("bool",true);

	$smarty -> display("tpl/conditions.html");
 ?>