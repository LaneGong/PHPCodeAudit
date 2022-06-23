<?php
	class test{
		var $test = '123';
	}
	$class1 = new test;
	$class1_ser = serialize($class1);
	print_r($class1_ser);
?>
