<?php
/*-----------------------------------------------------------------------*
	 Copyright (c) 2008 by Dominique Laporte(C-E-D@wanadoo.fr)

	 This file is part of Prométhée.

	 Prométhée is free software: you can redistribute it and/or modify
	 it under the terms of the GNU General Public License as published by
	 the Free Software Foundation, either version 3 of the License, or
	 (at your option) any later version.

	 Prométhée is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU General Public License for more details.

	 You should have received a copy of the GNU General Public License
	 along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
*-----------------------------------------------------------------------*/


/*
 *		module   : config_edt.php
 *		projet   : paramétrage de l'interface intranet
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 24/08/08
 *		modif    :
*/

	// On vérifie que l'on soit bien un super-administrateur
	if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

	$IDconf   = ( @$_POST["IDconf"] )			// ID de la configuration
		? (int) $_POST["IDconf"]
		: (int) @$_GET["IDconf"] ;
	$IDcentre = ( @$_POST["IDcentre"] )			// ID du centre
		? (int) $_POST["IDcentre"]
		: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

	$IDdef   = @$_POST["IDdef"];
	$text    = @$_POST["text"];
	$kwords  = addslashes(trim(@$_POST["kwords"]));

	$submit  = (int) @$_POST["valid_x"];		// bouton de validation

	function lundo($jaro,$semajno){
		$semo=mktime(0,0,0,1,1,$jaro);
		$semajno+=date('W',$semo)=='52' ? 1 :0;
		$semo=strtotime("+$semajno week",$semo);
		while(date('w',$semo)!='1'){$semo=strtotime("-1 day",$semo);}
		return ($semo);
	}
?>




<?php
	if($submit)
	{
		// Traitement semaines
		$tab = array();
		$tab[] = $IDcentre;

		for($i = 1; $i <= 52; $i++)
		{
			$tab[] = @$_POST["num_".$i];
		}

		$json = json_encode($tab);

		$query  = "update config_centre set _semaines = '$json' ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre";

		$result = mysqli_query($mysql_link, $query);

		// Traitement vacances
		$vac = array();
		$vac["start"] = array();
		$vac["end"] = array();

		foreach($_POST as $key => $val)
		{
			if (substr($key, 10) == 'key' || substr($key, 8) == 'key') continue; // On ignore le template

			if(substr($key, 0, 10) == "vac_start_") $vac["start"][] = $val;
			if(substr($key, 0, 8) == "vac_end_") $vac["end"][] = $val;
		}

		$json_vac = json_encode($vac);

		$query  = "update config_centre set _vacances = '$json_vac' ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre";

		$result = mysqli_query($mysql_link, $query);


		// La fonction strtotime() ne fonctionne pas pour une raison inconnue donc on utilise cette fonction
		function transformDateFromFrenchToEn($date)
		{
			$temp = explode('/', $date);
			$newDate = $temp[2]."-".$temp[1]."-".$temp[0];
			return $newDate;
		}

		$periode_date_list = array();
		// Traitement des dates des périodes
		foreach ($_POST as $key => $value) {

			// Si le paramètre testé est bien un param de période
			if (strpos($key, 'periode_start_') !== false)
			{
				$periode_date_list[substr($key, -1, 1)."_start"] = transformDateFromFrenchToEn($value);
			}
			elseif (strpos($key, 'periode_end_') !== false)
			{
				$periode_date_list[substr($key, -1, 1)."_end"] = transformDateFromFrenchToEn($value);
			}
		}
		$periode_date_list = json_encode($periode_date_list);
		setParam('periodeDates', $periode_date_list);
		echo '<script>';
		  echo '$(document).ready(function(){';
		    echo 'Toast.fire({';
		      echo 'icon: \'success\',';
		      echo 'title: \'Enregistré\'';
		    echo '});';
		  echo '});';
		echo '</script>';
	}
?>


<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_EDT); ?></h1>

<?php include("include/config_menu_top.php"); ?>

<form id="formulaire" action="index.php?item=21&amp;cmde=edt&amp;<?php echo $IDconf; ?>" method="post">
	<?php echo centerSelect($IDcentre); ?>
	<div class="card shadow mb-4">
		<div class="card-header">
			<ul class="nav nav-pills card-header-pills" id="pills-tab" role="tablist">
				<li class="nav-item" role="presentation"><a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true"><?php echo $msg->read($CONFIG_CONFVAC); ?></a></li>
				<li class="nav-item" role="presentation"><a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false"><?php echo $msg->read($CONFIG_CONFPERIODES); ?></a></li>
			</ul>
		</div>
		<div class="card-body">
			<?php
				$hidden_form_items = array('item', 'cmde', 'IDconf');
				foreach ($hidden_form_items as $key => $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
			?>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/edt/config_vacances.php'); ?></div>
				<div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/edt/config_periodes.php'); ?></div>
			</div>

			<hr>

			<button name="valid_x" value="1" class="btn btn-success" type="submit"><?php echo $msg->read($CONFIG_INPUTOK); ?></button>
		</div>
	</div>
</form>
