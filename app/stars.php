<?php
$con = mysql_connect("localhost:3306","root","");
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }else{
    // die('success');
  }

//创建数据库
  if (mysql_query("CREATE DATABASE my_db",$con)){
    echo "Database created";
  }else{
    echo "Error creating database: " . mysql_error();
  }


  //创建表
  mysql_select_db("my_db", $con);
  $sql = "CREATE TABLE Persons 
  (
  FirstName varchar(15),
  LastName varchar(15),
  Age int
  )";
  mysql_query($sql,$con);


//insert into插入数据
mysql_query("INSERT INTO Persons (FirstName, LastName, Age) 
VALUES ('Peter', 'Griffin', '35')");
mysql_query("INSERT INTO Persons (FirstName, LastName, Age) 
VALUES ('Glenn', 'Quagmire', '33')");


//where条件选择
$result = mysql_query("SELECT * FROM Persons
WHERE FirstName='Peter'");
while($row = mysql_fetch_array($result))
  {
  echo $row['FirstName'] . " " . $row['LastName'];
  echo "<br />";
  }


// 排序order by
  $result = mysql_query("SELECT * FROM Persons ORDER BY age");
while($row = mysql_fetch_array($result))
  {
  echo $row['FirstName'];
  echo " " . $row['LastName'];
  echo " " . $row['Age'];
  echo "<br />";
  }


  //update修改
  mysql_query("UPDATE Persons SET Age = '36'
WHERE FirstName = 'Peter' AND LastName = 'Griffin'");

//delete删除数据
// mysql_query("DELETE FROM Persons WHERE LastName='Griffin'");

  


  mysql_close($con);

?>