<?php
  if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
  else{
    if($_FILES["file"]["type"] == "image/jpeg" ||  $_FILES["file"]["type"] == "image/png"){
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