<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse"	data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
			<span class="icon-bar"></span> <span class="icon-bar"></span>
		</button>
	</div>
	<!-- Top Menu Items -->
	<ul class="nav navbar-right top-nav">	
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>
				<?php if ($this->ion_auth->logged_in()) print $this->live_user->first_name.' '.$this->live_user->last_name ?> <b class="caret"></b>
			</a>
			<ul class="dropdown-menu">				
				<li><a href="<?php print base_url('users/settings')?>"><i class="fa fa-fw fa-gear"></i> Settings</a></li>
				<li class="divider"></li>
				<li><a href="<?php print base_url('auth/logout')?>"><i class="fa fa-fw fa-power-off"></i> Log Out</a></li>
			</ul>
		</li>
	</ul>
	<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav side-nav">
			<li	class="<?php print $this->controller == 'invoices' ? 'active' : ''?>">
				<a href="<?php print base_url("invoices")?>"><i	class="fa fa-fw fa-usd"></i> <?php print lang("invoices")?></a>
			</li>
			<li	class="<?php print $this->controller == 'estimates' ? 'active' : ''?>">
				<a href="<?php print base_url("estimates")?>"><i class="fa fa-fw fa-file-text"></i> <?php print lang("estimates")?></a>
			</li>
			<li	class="<?php print $this->controller == 'items' ? 'active' : ''?>">
				<a href="<?php print base_url("items")?>"><i class="fa fa-fw fa-check-square"></i> <?php print lang("items")?></a>
			</li>
			<li	class="<?php print $this->controller == 'clients' ? 'active' : ''?>">
				<a href="<?php print base_url("clients")?>"><i class="fa fa-fw fa-users"></i> <?php print lang("clients")?></a>
			</li>
			<?php if ($this->ion_auth->is_admin()) {?>
	            <li	class="<?php print $this->controller == 'users' ? 'active' : ''?>">
					<a href="<?php print base_url("users")?>"><i class="fa fa-fw fa-user"></i> <?php print lang("users")?></a>
				</li>
            <?php }?>
		</ul>
	</div>
	<!-- /.navbar-collapse -->
</nav>
<script >
	var base_url = "<?php print base_url(); ?>";
</script>