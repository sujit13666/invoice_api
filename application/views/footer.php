<?php
// Loading all js files from $this->data['js'][] = name
if (isset($js))
{
	foreach ($js as $key=>$val) {
		print  "<script type='text/javascript' src='".base_url().'assets/js/'.$val.'?q=1'."'>  </script>";
	}
}