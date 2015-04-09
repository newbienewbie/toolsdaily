<?php

//functions lib

include_once("config.php");



//echo $html;

function login($base_url,$spec_url,$cj,$user,$pass){

    //login
    $login_url=$base_url.$spec_url;
    $ch=curl_init($login_url);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cj);

    //urlenode the post_array
    $postarray=array(
        'user_account'=>$user,
        'user_psw'=>$pass,
        'sys_language'=>'ch',
    );
    /*
    foreach($postarray as $k=>$v){
        $tmp[]=$k."=".urlencode($v);
    }
    $postfields=implode("&",$tmp);
     */
    $postfields=http_build_query($postarray);

    //set the post option
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    
    $res=curl_exec($ch);
    curl_close($ch);

    echo "the raw login page respose is :\n\r"."\t\t".$res."\n\r";
    return $res;
}




function query($base_url,$spec_url,$postfields,$cj){

    //query
    $query_url=$base_url.$spec_url;
    $ch=curl_init($query_url);

    curl_setopt($ch,CURLOPT_POST,true);

    curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);

    curl_setopt($ch,CURLOPT_COOKIEFILE,$cj);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $res=curl_exec($ch);
    curl_close($ch);
    return $res;
}





function batchquery($base_url,$spec_url,$mat_ids,$cj){


    foreach($mat_ids as $mat_id){
        $postfields="page=1&rows=50&lbbm=&status=&qc=&wlbm=$mat_id&creator_account=&gsdm=&sqrq_start=&sqrq_end=&complexcondition=";
        query($base_url,$spec_url,$postfields,$cj);
    }

}





?>
