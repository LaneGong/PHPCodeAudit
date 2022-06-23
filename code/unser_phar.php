<?php
	//EXP2
	class test{
		var $test = '123';
		function __wakeup(){
			$fp = fopen("shell.php","w") ;
			fwrite($fp,$this->test);
			fclose($fp);
		}
	}
	$filename=$_GET['filename'];
	file_get_contents($filename);
	require "shell.php";
?>