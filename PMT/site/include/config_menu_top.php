<ul class="nav nav-pills justify-content-center mb-4">

  <!-- <li class="nav-item">
    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
  </li> -->

	<?php
	$temp = array(
		$msg->read($CONFIG_OPTION) => array('icon' => 'fas fa-cogs', 'link' => 'index.php?item='.$item, 'active' => ''),
		$msg->read($CONFIG_TUNE) => array('icon' => 'fas fa-wrench', 'link' => 'index.php?item='.$item.'&cmde=skin', 'active' => 'skin'),
		$msg->read($CONFIG_DATABASE) => array('icon' => 'fas fa-database', 'link' => 'index.php?item='.$item.'&cmde=dba', 'active' => 'dba'),
		// $msg->read($CONFIG_KEYWORDS) => array('icon' => 'fas fa-file', 'link' => 'index.php?item='.$item.'&cmde=kwords', 'active' => 'kwords'),
		$msg->read($CONFIG_MENU) => array('icon' => 'fas fa-bars', 'link' => 'index.php?item='.$item.'&cmde=menu', 'active' => 'menu'),
		'Emploi du temps' => array('icon' => 'fas fa-wrench', 'link' => 'index.php?item='.$item.'&cmde=edt', 'active' => 'edt')
	);

	foreach ($temp as $key => $value) {
		if ($cmde == $value['active'] || ($cmde == '' && $value['active'] == '')) $active = 'active';
		else $active = '';
		echo '<li class="nav-item '.$active.'">';
	    echo '<a class="nav-link '.$active.'" href="'.$value['link'].'"><i class="'.$value['icon'].'"></i>&nbsp;'.$key.'</a>';
	  echo '</li>';
	}

	?>
</ul>
