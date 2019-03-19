<?php
/* Smarty version 3.1.32-dev-38, created on 2018-01-04 16:59:06
  from 'E:\xampp\htdocs\myphp\myProject\learnS\tpl\test.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5a4decda89c431_81162894',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c648dd273852e179b624fd40d7e20e27eba2c21c' => 
    array (
      0 => 'E:\\xampp\\htdocs\\myphp\\myProject\\learnS\\tpl\\test.html',
      1 => 1515056344,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a4decda89c431_81162894 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_capitalize')) require_once 'E:\\xampp\\htdocs\\myphp\\myProject\\libs\\plugins\\modifier.capitalize.php';
if (!is_callable('smarty_modifier_date_format')) require_once 'E:\\xampp\\htdocs\\myphp\\myProject\\libs\\plugins\\modifier.date_format.php';
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h3>变量的输出</h3>
	<?php echo $_smarty_tpl->tpl_vars['name']->value;?>

	<h3>smarty注释</h3>
		<h3>数组的输出</h3>
	<em>1.</em><span><?php echo $_smarty_tpl->tpl_vars['arr']->value['language'];?>
</span>
	<em>2.</em><span><?php echo $_smarty_tpl->tpl_vars['arr']->value['version'];?>
</span>
	<h3>变量调节器</h3>
	<ul>
		<li>
			<a href="">capitalize 首字母大写</a>
			<span><?php echo smarty_modifier_capitalize($_smarty_tpl->tpl_vars['str']->value);?>
</span>
		</li>
		<li>
			<a href="">cat 字符串拼接</a>
			<span><?php echo ($_smarty_tpl->tpl_vars['name']->value).(" is the best language in the world");?>
</span>
		</li>
		<li>
			<a href="">date_format 日期格式化</a>
			<span><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['time']->value,"%A %B-%d-%Y %H:%M:%S");?>
</span>
		</li>
		<li>
			<a href="">default 给未赋值或者值为空的变量设置默认值,如果变量有值的时候，使用变量设置的值而非默认值</a>
			<span><?php echo (($tmp = @$_smarty_tpl->tpl_vars['age']->value)===null||$tmp==='' ? '18' : $tmp);?>
</span>
		</li>
		<li>
			<a href="">escape url转码</a>
			<span><?php echo rawurlencode($_smarty_tpl->tpl_vars['url']->value);?>
</span>
					</li>
		<li>
			<a href="">upper 大写</a>
			<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['Blessing']->value, 'UTF-8');?>

		</li>
		<li>
			<a href="">nl2br 换行转换成br标签</a>
			<p><?php echo nl2br($_smarty_tpl->tpl_vars['poetry']->value);?>
</p>
		</li>


	</ul>

</body>
</html><?php }
}
