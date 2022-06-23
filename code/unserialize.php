<?php
	$class2 = 'O:4:"test":1:{s:4:"test";s:3:"123";}';
	$class2_unser = unserialize($class2);
	print_r($class2_unser);
?>