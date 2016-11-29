<!DOCTYPE html>
<html>
    <?php if (isset($header)) print $header; ?>
	<body>	
		<?php 
			if ($this->ion_auth->logged_in())
			{
				print '<div id="wrapper">';
					print $header_menu;
					print $content;
				print '</div>';
			}
			else
			{
				print $content;
			}
			
			if (isset($footer)) print $footer; ?>	
	</body>
</html>