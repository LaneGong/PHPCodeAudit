<?php
  function deldot($s){
    for($i = strlen($s)-1;$i>0;$i--){
      $c = substr($s,$i,1);
      if($i == strlen($s)-1 and $c != '.'){
        return $s;
      }
      
      if($c != '.'){
        return substr($s,0,$i+1);
      }
    }
  }
  if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
  else{
    $deny_ext = array('.php');
    $file_name = trim($_FILES["file"]['name']);
    $file_name = deldot($file_name);//删除文件名末尾的点
    $file_ext = strrchr($file_name, '.');
    $file_ext = str_ireplace('::$DATA', '', $file_ext);//去除字符串::$DATA
    $file_ext = trim($file_ext); //收尾去空
    if(!in_array($file_ext, $deny_ext)&&($_FILES["file"]["type"] == "image/jpeg" ||  $_FILES["file"]["type"] == "image/png")) {
      // Can we move the file to the upload folder?
      if( !move_uploaded_file($_FILES[ 'file' ][ 'tmp_name' ],"upload/".$_FILES["file"]["name"])){
        echo "Failure!";
      }
      echo "Upload: " . $_FILES["file"]["name"]."<br />";
      echo "Type: " . $_FILES["file"]["type"]."<br />";
      echo "Size: " . ($_FILES["file"]["size"] / 1024)." Kb<br />";    }
    else{
      echo "Invalid file";
    }
  }
?>

