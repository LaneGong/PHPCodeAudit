<?php
	$name=$_GET['name'];
	$age=$_GET['age'];
	echo "My name is ".htmlspecialchars($name).".My age is ".htmlspecialchars($age).".";
?>
