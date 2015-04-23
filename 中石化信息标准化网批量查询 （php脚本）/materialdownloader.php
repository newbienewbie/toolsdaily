<?php
require("sinopecinfoquery.php");

echo "request to :\t".$url."\r\n"."user name is \t".$user."\r\n";

//init
$ch=curl_init($url);
$cj=tempnam(dirname(__FILE__),$cookiefilepre);
curl_setopt($ch,CURLOPT_COOKIEJAR,$cj);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$html=curl_exec($ch);
curl_close($ch);


//login:$url,$user,$pass:define in the config.php
$spec_url="/system/sysUserAction!checkLogin.action";
login($url,$spec_url,$cj,$user,$pass);

$jsonurl="http://10.5.105.211/wlwl/wlwl_queryWlwl.action";
$data=include("pressureData.php");
$materials=queryMaterial($jsonurl,$data,$cj);



//输出爬取结果总数
echo PHP_EOL;
echo "总条数：" ,count($materials),PHP_EOL;

//输出爬取结果明细
ob_start();
foreach($materials as $m){
    echo $m->wlbm,"\t",$m->qc,"\t",$m->jldw,PHP_EOL;
}
$out=ob_get_contents();
ob_end_clean();
echo $out;

//写入txt文件
$file="fuckSinopec.txt";
file_put_contents($file,$out);



function queryMaterial($jsonurl,$data,$cj){
    $page=$data['page'];
    $materials=array();
    while(true){

        $postfields=http_build_query($data);
        echo PHP_EOL;
        echo "[+] post fields:\t",$postfields,PHP_EOL;
        $ch=curl_init($jsonurl);
        curl_setopt_array($ch,array(
            CURLOPT_POSTFIELDS=>$postfields,
            CURLOPT_COOKIEFILE=>$cj,
            CURLOPT_COOKIEJAR=>$cj,
            CURLOPT_RETURNTRANSFER=>true
        ));
        $contents=curl_exec($ch);
        curl_close($ch);

        $rows=json_decode($contents);
        echo "[!] 已经获取第{$page}页;",PHP_EOL;
        $total=$rows->total;
        $totalPage=ceil($total/$data['rows']);
        $rows=$rows->rows;
        foreach($rows as $r){
            $materials[]=$r;
        }
        sleep(2);
        $data['page']=++$page;
        if($totalPage<$page){
            break;
        }
    }
    return $materials;

}





?>
