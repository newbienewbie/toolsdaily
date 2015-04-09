<?php

include_once("sinopecinfoquery.php");    //func_lib
include_once("Classes/PHPExcel.php");    //PHPExcel

function excel_query($objPHPExcel,$strSheetName,$srcCol,$dstCol,$url,$spec_url,$cj){

    $objSheet=$objPHPExcel->getSheetByName($strSheetName);

    $highestRow=$objSheet->getHighestRow();
    echo "highestRow is : \t $highestRow\r\n";

    for($rowNo=2;$rowNo<=$highestRow;++$rowNo){
        //get the mat_id
        $cellCoord=$srcCol.$rowNo;
        echo "current cellCoord: $cellCoord\t(Total:$highestRow)\t";
        $srcCellValue=$objSheet->getCell($cellCoord)->getValue();
        echo "the mat_id is :\t$srcCellValue\t";

        //query
        
        $postfields="page=1&rows=50&lbbm=&status=&qc=&wlbm=$srcCellValue&creator_account=&gsdm=&sqrq_start=&sqrq_end=&complexcondition=";
        $res=query($url,$spec_url,$postfields,$cj);
        if($res=="{\"rows\":[],\"total\":0}"){
            echo "not active\r\n";
        }else{
            echo "active \r\n";
        }

        //write the res
        $cellCoord=$dstCol.$rowNo;
        $objSheet->setCellValue($cellCoord,$res);
    }


}


$fname="MRP.xlsx";

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




$objPHPExcel=PHPExcel_IOFactory::load($fname);
$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");


$spec_url="/wlwl/wlwl_queryWlwl.action";
excel_query($objPHPExcel,"Sheet1","G","T",$url,$spec_url,$cj);
$newExcelName="new_".$fname;
$objWriter->save($newExcelName);







?>
