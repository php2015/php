<!-- 请始终向 HTML 文档添加 <!DOCTYPE> 声明，这样浏览器才能获知文档类型。 -->
<!DOCTYPE HTML> 
<html>
<head>
        <!-- “utf-8”是一种字符编码。charset=”utf-8”是告知浏览器此页面属于什么字符编码格式，下一步浏览器做好“翻译”工作 -->
        <!-- 常见的字符编码有:
        gbk：国家标准扩展版
        unicode：万国码
        **utf-8：**unicode的升级版。 -->
    <meta charset="utf-8">
</head>
<body> 

<?php
// define variables and set to empty values
$name = $email = $gender = $comment = $website = "";
// 检查表单是否使用 $_SERVER["REQUEST_METHOD"] 进行提交
// 如果 REQUEST_METHOD 是 POST，那么表单已被提交 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $name = test_input($_POST["name"]);
   $email = test_input($_POST["email"]);
   $website = test_input($_POST["website"]);
   $comment = test_input($_POST["comment"]);
   $gender = test_input($_POST["gender"]);
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<h2>PHP 验证实例</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   姓名：<input type="text" name="name">
   <br><br>
   电邮：<input type="text" name="email">
   <br><br>
   网址：<input type="text" name="website">
   <br><br>
   评论：<textarea name="comment" rows="5" cols="40"></textarea>
   <br><br>
   性别：
   <input type="radio" name="gender" value="female">女性
   <input type="radio" name="gender" value="male">男性
   <br><br>
   <input type="submit" name="submit" value="提交"> 
</form>

<?php
echo "<h2>您的输入：</h2>";
// $_SERVER["PHP_SELF"]超全局变量,返回当前文件名
echo $_SERVER["PHP_SELF"];
echo "<br>";
// htmlspecialchars() 函数把特殊字符转换为 HTML 实体,防止攻击
echo htmlspecialchars($_SERVER["PHP_SELF"]);
echo "<br>";
echo $name;
echo "<br>";
echo $email;
echo "<br>";
echo $website;
echo "<br>";
echo $comment;
echo "<br>";
echo $gender;
?>

</body>
</html>