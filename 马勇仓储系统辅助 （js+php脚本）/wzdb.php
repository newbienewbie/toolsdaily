<html>
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>


<?php 
echo "<br>";
echo "<br>";

$rows=iconv("gb2312","utf-8//IGNORE",$_POST["rows"]);
$gi_date=iconv("gb2312","utf-8//IGNORE",$_POST["gi_date"]);
$gi_id=iconv("gb2312","utf-8//IGNORE",$_POST["gi_id"]);
$gi_where=iconv("gb2312","utf-8//IGNORE",$_POST["gi_where"]);
$gi_cuoshileixing=iconv("gb2312","utf-8//IGNORE",$_POST["gi_cuoshileixing"]);
$gi_who_unit=iconv("gb2312","utf-8//IGNORE",$_POST["gi_who_unit"]);
$gi_who_person=iconv("gb2312","utf-8//IGNORE",$_POST["gi_who_person"]);


function dec($c){return iconv('utf-8','gb2312//IGNORE',$c);}
function unesc($c){
    $temp=str_replace("&quot;",'"',$c);
    $temp=str_replace("&nbsp;"," ",$temp);
    $temp=str_replace("&#39;","'",$temp);
    $temp=str_replace("&lt;","<",$temp);
    $temp=str_replace("&gt;",">",$temp);
    return $temp;
}



$rowsArray=json_decode($rows);
$error=json_last_error();
if($error!=0){
    echo "json转换错误！，当前errorNo:$error<br>";
    var_dump($rows);
    echo "<hr>";
    var_dump($rowsArray);
}else{
    echo "json 转换成功<br>";
}



echo "<hr>";
$driver="Driver={Microsoft Access Driver (*.mdb, *.accdb)}";    
//$dbq="Dbq=".getcwd()."/testacc.accdb";
$dbq="Dbq="."I://LingLiaoXinXiTaiZhang.accdb";
$odbc="odbc:$driver;$dbq;charset=UTF-8";
echo $odbc."<br>";
try{
    $pdh=new \PDO($odbc);
    $pdh->exec("set names utf8");
    echo "连接到Access数据库成功<br>";
}catch(Exception $e){
    echo $e->getMessage();     
}

echo "<hr>";
try{
    $sql="insert into goodsissue 
        (gi_id,gi_date,gi_where,gi_cuoshileixing,gi_what,gi_spec,gi_num,gi_bun,gi_state,gi_place,gi_note,gi_who_unit,gi_who_person)
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ";
    $stmt=$pdh->prepare($sql);



    foreach($rowsArray as $r){



        $stmt->execute(array(
            dec(unesc($gi_id)),
            dec(unesc($gi_date)),
            dec(unesc($gi_where)),
            dec(unesc($gi_cuoshileixing)),
            dec(unesc($r[0])),  //gi_what
            dec(unesc($r[1])),  //gi_spec
            intVal(dec(unesc($r[2]))),  //gi_num
            dec(unesc($r[3])),  //gi_bun
            dec(unesc($r[4])),  //gi_state
            dec(unesc($r[5])),  //gi_place
            dec(unesc($r[6])),  //gi_note
            dec(unesc($gi_who_unit)),
            dec(unesc($gi_who_person))
        ));
    }

    echo $stmt->errorCode();
    echo "<br>已保存到MS-Access数据库";
}

catch(Exception $e){
    echo $e->getMessage();
}







?>

    </body>
</html>







