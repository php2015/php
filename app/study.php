<?php
    // 输出
    echo "hello word";
    //函数
    function interest(){
        return "返回函数";
    }
    echo interest();
    //换行
    echo '<br>';
    // 内置数组方法
    $array = array(0 => 100, "color" => "red");
    print_r(array_values($array));
    //变量
    $x = 90.8;
//返回数据类型和值
    var_dump($x);
//字符串长度
    echo strlen('xiyanghong');
    echo '<br>';
    //查找yang的初始下标为2
    echo strpos('xiyanghong','yang');
    
    echo '<br>';
    //定义变量
    define('GRV','定义的值');
    echo GRV;
    //加减乘除
    echo '<br>';
    $y = 2;
    echo ($y*$x);
    echo '<br>';
    //if 和 else if
    if(true){
        echo 'one-if';
    }else if(false){
        echo 'two-if';
    }
    //switch
    echo '<br>';
    switch(2){
        case 2: 
        echo 'one-switch';
        break;
            case 2:
            echo 'two-switch';
            break;
    }
    //while 至少执行一次的循环
    $k = 15;
    echo '<br>';
    do{
        $k++;
        echo 'while-num';
        echo '<br>';
    }while($k == 16);
    //for
    for($f=0;$f<10;$f++){
        echo 'i';
    }
    //foreach
    $colors = array('red','green','blue');
//    echo $colors;
    echo '<br>';
    foreach($colors as $value){
        echo "$value <br>";
    }
    //函数
    function payMoney($value){
        echo "$value <br>";
    }
    payMoney("现金");
    payMoney("微信");
    payMoney("支付宝");
    //array
    $cars = array("HFU");
    $cars[1] = "RYX";
    echo "I like ".$cars[0]." and ".$cars[1].";";
    //获取数组的长度
    echo count($cars);
    //遍历关联数组
    // echo "<br>";
    $age = array("bill"=>"111","bingo"=>"222");
    foreach($age as $nini=>$ta){
        echo "<br>";
        echo $nini." == ".$ta;
    }
    //按照字母升序
    echo "<br>";
    sort($colors);
    $clength = count($colors);
    for($x=0;$x<$clength;$x++){
        echo $colors[$x];
        echo "<br>";
    }
    //按照数字升序
    $numbers = array(19,28,2,8);
    sort($numbers);
    $arrlength = count($numbers);
    for($x=0;$x<$arrlength;$x++){
        echo $numbers[$x];
        echo "<br>";
    }
    //全局变量
    $ying = 80;
    $cin = 90;
    function addition(){
        $GLOBALS['fing'] = $GLOBALS['ying'] + $GLOBALS['cin'];
    }
    addition();
    echo $fing;
    
?>