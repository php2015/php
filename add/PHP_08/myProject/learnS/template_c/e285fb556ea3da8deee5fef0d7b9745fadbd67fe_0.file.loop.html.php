<?php
/* Smarty version 3.1.32-dev-38, created on 2018-01-05 04:35:49
  from 'E:\xampp\htdocs\myphp\PHP_08\myProject\learnS\tpl\loop.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5a4ef29520bac2_48790585',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e285fb556ea3da8deee5fef0d7b9745fadbd67fe' => 
    array (
      0 => 'E:\\xampp\\htdocs\\myphp\\PHP_08\\myProject\\learnS\\tpl\\loop.html',
      1 => 1515123330,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
  ),
),false)) {
function content_5a4ef29520bac2_48790585 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php $_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>"php的学习"), 0, false);
?>
	<!-- <?php
$__section_student_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_student']) ? $_smarty_tpl->tpl_vars['__smarty_section_student'] : false;
$__section_student_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['students']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_student_0_total = $__section_student_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_student'] = new Smarty_Variable(array());
if ($__section_student_0_total !== 0) {
for ($__section_student_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] = 0; $__section_student_0_iteration <= $__section_student_0_total; $__section_student_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index']++){
?>
		<div></div>
	<?php
}
}
if ($__section_student_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_student'] = $__section_student_0_saved;
}
?> -->
	<table border="1">
		<?php
$__section_student_1_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_student']) ? $_smarty_tpl->tpl_vars['__smarty_section_student'] : false;
$__section_student_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['students']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_student_1_start = $__section_student_1_loop - 1;
$__section_student_1_total = min(($__section_student_1_start+ 1), 4);
$_smarty_tpl->tpl_vars['__smarty_section_student'] = new Smarty_Variable(array());
if ($__section_student_1_total !== 0) {
for ($__section_student_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] = $__section_student_1_start; $__section_student_1_iteration <= $__section_student_1_total; $__section_student_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] -= 1){
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['students']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_student']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] : null)]['name'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['students']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_student']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] : null)]['age'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['students']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_student']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_student']->value['index'] : null)]['sex'];?>
</td>
		</tr>
		<?php
}
}
if ($__section_student_1_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_student'] = $__section_student_1_saved;
}
?>
	</table>

	<table>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['students']->value, 'student');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['student']->value) {
?>
		<tr>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['student']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</td>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</tr>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</table>
	<?php echo $_smarty_tpl->tpl_vars['myobj']->value->name;?>

	<?php echo $_smarty_tpl->tpl_vars['myobj']->value->meth2("这是smarty模板输出的对象信息");?>

	<?php echo $_smarty_tpl->tpl_vars['myobj']->value->meth3(array("php",'java'));?>


</body>
<?php echo '<script'; ?>
>
	// array(
	// 		array("name"=>'Mary','sex'=>'gril','age'=>20),
	// 		array('name'=>'jack','sex'=>'boy','age'=>21),
	// 		array('name'=>'tom','sex'=>'boy','age'=>19)
	// 	)

<?php echo '</script'; ?>
>
</html><?php }
}
