<?php
	$filename = $_GET['file'];	if(isset($_GET['file'])){		echo file_get_contents("$filename");	}
?>