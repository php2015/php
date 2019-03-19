<?php
/* Smarty version 3.1.32-dev-38, created on 2018-01-05 04:35:49
  from 'E:\xampp\htdocs\myphp\PHP_08\myProject\learnS\tpl\header.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5a4ef2952231d0_44681559',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '751ad6a60ca91594434bc20277a6e43adbb348d2' => 
    array (
      0 => 'E:\\xampp\\htdocs\\myphp\\PHP_08\\myProject\\learnS\\tpl\\header.html',
      1 => 1515123347,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a4ef2952231d0_44681559 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<p><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</p>
	<h1>我是被引入进来的头文件<?php echo $_smarty_tpl->tpl_vars['test']->value;?>
</h1>

</body>
</html><?php }
}
