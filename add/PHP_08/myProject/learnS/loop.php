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

	$students = array(
			array("name"=>'Mary','sex'=>'gril','age'=>20),
			array('name'=>'jack','sex'=>'boy','age'=>21),
			array('name'=>'tom','sex'=>'boy','age'=>19)
		);

	class MyObject{
		public $name = "类";
		function meth1(){
			return "hello world";
		}

		function meth2($params){
			return $params;
		}

		function meth3($params){
			return $params[0].'and'.$params[1];
		}
	}

	$myobj = new MyObject();

	$smarty -> assign('test','测试');
	$smarty -> assign('myobj',$myobj);
	$smarty -> assign('students',$students);

	$smarty -> display('tpl/loop.html');

	//循环语句
	//1.section
	//2.foreach
	


	



 ?>