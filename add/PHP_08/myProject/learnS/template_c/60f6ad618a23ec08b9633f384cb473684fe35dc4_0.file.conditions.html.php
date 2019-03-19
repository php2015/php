<?php
/* Smarty version 3.1.32-dev-38, created on 2018-01-05 03:16:44
  from 'E:\xampp\htdocs\myphp\PHP_08\myProject\learnS\tpl\conditions.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5a4ee00cd39904_31070985',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '60f6ad618a23ec08b9633f384cb473684fe35dc4' => 
    array (
      0 => 'E:\\xampp\\htdocs\\myphp\\PHP_08\\myProject\\learnS\\tpl\\conditions.html',
      1 => 1515118603,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a4ee00cd39904_31070985 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<!-- 判断，如果变量name的值是PHP，那么让div显示hello PHP，如果是java，显示hello java，如果都不是，那么让它显示 hello everyone -->
	<h3>条件判断</h3>
	<span>1.相等 eq</span>
	<div>
		<?php if ($_smarty_tpl->tpl_vars['name']->value == "php") {?>
			hello php.
		<?php } elseif ($_smarty_tpl->tpl_vars['name']->value == "java") {?>
			hello java
		<?php } else { ?>
			hello everyone
		<?php }?>
	</div>
	<span>2.大于 gt</span>
	<div>
		<?php if ($_smarty_tpl->tpl_vars['score']->value > 60) {?>
			及格
		<?php } else { ?>
			不及格，需要补考
		<?php }?>
	</div>
	<span>3.小于 lt</span>
	<div>
		<?php if ($_smarty_tpl->tpl_vars['age']->value < 18) {?>
			未成年
		<?php } else { ?>
			成年
		<?php }?>
	</div>
	<span>4.大于等于  gte/ge</span>
	<br>
	<span>5.小于  lte/le</span>
	<br>
	<span>6.不等于 ne/neq</span>
	<div>
		<?php if ($_smarty_tpl->tpl_vars['score']->value != 100) {?>
			还有上升的空间
		<?php } else { ?>
			那你很棒棒呀
		<?php }?>
	</div>
	<br>
	<span>7.非  not</span>
	<div>
		<?php if (!$_smarty_tpl->tpl_vars['bool']->value) {?>
			布尔值
		<?php } else { ?>
			哈哈哈哈
		<?php }?>
	</div>
</body>
<?php echo '<script'; ?>
>

<?php echo '</script'; ?>
>
</html><?php }
}
