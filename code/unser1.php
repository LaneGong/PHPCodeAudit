<?php
	//魔术方法
class test{
	var $test = '123';
	function __wakeup(){
		echo "__wakeup";
		echo "</br>";
	}
	function __construct(){
		echo "__construct";
		echo "</br>";
	}
	function __destruct(){
		echo "__destruct";
		echo "</br>";
	}
}
$class2 = 'O:4:"test":1:{s:4:"test";s:3:"123";}';
$class2_unser = unserialize($class2);
print_r($class2_unser);
echo "</br>";
?>