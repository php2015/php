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


	function test($params){
		var_dump($params);
	}

	//注册变量
	// $smarty -> assign();

	//注册插件 （自定义函数）
	// $smarty -> registerPlugin(type,name,function_name);
	
	$smarty -> registerPlugin('function','f_test','test');
	/*
		参数1：type 可以是function、block、compiler、modifier，就是指明要注册的插件是函数，块，编译器还是修时器。

		参数2：你要实现的插件的名称
		参数3：实现的函数的名字
	 */
	
	$smarty -> display("tpl/function.html");
	





 ?>