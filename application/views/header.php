<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	
	// Loading all css files from $this->data['css'][] = name
	if (isset ( $css )) {
		foreach ( $css as $key => $val ) {
			print "<link rel='stylesheet' type='text/css' href='" . base_url () . 'assets/css/' . $val . '?q=1' . "'  />";
		}
	}
	
	?>

	<title><?php if (isset($title)) print $title?></title>

</head>