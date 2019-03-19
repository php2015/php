<?php 
	//引入Smarty文件
	require("../libs/Smarty.class.php");

	//实例化一个smarty对象
	$smarty = new Smarty();

	// var_dump($smarty);
	
	//对smarty进行相关的配置
	
	//配置左定界符
	$smarty -> left_delimiter = "{";
	//配置右定界符
	$smarty -> right_delimiter = "}";
	//配置HTML模板地址
	$smarty -> template_dir = "tpl";
	//模版编译生成的文件目录
	$smarty -> compile_dir = "template_c";
	//缓存目录
	// $smarty -> cache_dir = "cache";

	// //开起缓存
	// $smarty -> caching = true;
	// //缓存时间
	// $smarty -> cache_lifetime = 120;//单位是S
	
	//smarty常用的两个方法配置
	//assign(name,value) 用于在模板运行期间赋值变量 参数1：变量名，参数2：变量值
	
	//display() 显示编写好的模板 参数：模板地址
	
	$smarty -> assign("name","php");

	$smarty -> assign("str","hello");

	//注册一个数组
	$arr = array("language"=>"html","version"=>"5");

	$smarty -> assign("arr",$arr);

	//注册一个当前时间
	//time() 函数返回的是一个Unix时间戳          从1970/1/1到现在的秒数
	//设置时区
	date_default_timezone_set("Asia/Shanghai");
	$smarty -> assign("time",time());

	$smarty -> assign('age','22');

	$smarty -> assign('url','http://www.taobao.com?id=1&name=xxx');

	$smarty -> assign('Blessing','happy new year');

	$smarty -> assign('poetry','我慢慢地、慢慢地了解到，
								所谓父女母子一场，
								只不过意味着，
								你和他的缘分就是今生今世不断地在目送他的背影渐行渐远。
								你站在小路的这一端，
								看着他逐渐消失在小路转弯的地方，
								而且，
								他用背影默默告诉你：
								不必追。'
					);

	$smarty -> display("tpl/test.html");











	










 ?>