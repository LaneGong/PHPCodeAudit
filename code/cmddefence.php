<?php	$file = $_GET['file'];	$catfile = "ls ".escapeshellarg($file);
	var_dump($catfile);	exec($catfile,$array);
	print("<pre></pre>");	print_r($array);?>