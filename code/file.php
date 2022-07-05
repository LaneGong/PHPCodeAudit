<?php
  if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
  else{
    #文件类型校验
    if($_FILES["file"]["type"] == "image/jpeg" ||  $_FILES["file"]["type"] == "image/png"){
      if( !move_uploaded_file($_FILES[ 'file' ][ 'tmp_name' ],"upload/".$_FILES["file"]["name"])){
        echo "Failure!";
      }
      #打印相关文件上传信息
      echo "Upload: " . $_FILES["file"]["name"]."<br />";
      echo "Type: " . $_FILES["file"]["type"]."<br />";
      echo "Size: " . ($_FILES["file"]["size"] / 1024)." Kb<br />";    }
    else{#不符合文件类型报错
      echo "Invalid file";
    }
  }
?>