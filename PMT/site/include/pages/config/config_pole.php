<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2005-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)
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
 *		module   : config_pole.php
 *		projet   : paramétrage des poles
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 13/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IDconf    = ( @$_POST["IDconf"] )		// ID de la configuration
	? (int) $_POST["IDconf"]
	: (int) @$_GET["IDconf"] ;
$IDcentre  = ( @$_POST["IDcentre"] )	// ID du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]);

$tablidx   = ( @$_POST["tablidx"] )
	? (int) $_POST["tablidx"]
	: (int) @$_GET["tablidx"] ;
$idrec     = (int) @$_POST["idrec"];
$ident     = addslashes(trim(@$_POST["ident"]));
$identforfait     = addslashes(trim(@$_POST["identforfait"]));
$titre     = addslashes(trim(@$_POST["titre"]));
$color1     = addslashes(trim(@$_POST["color1"]));
$text      = addslashes(trim(@$_POST["text"]));
$code      = addslashes(trim(@$_POST["code"]));
$delay     = ( @$_POST["delay"] ) ? $_POST["delay"] : $ACOUNTIME ;

$show      = (int) @$_GET["show"];
$idcat     = (int) @$_GET["idcat"];
$value     = (int) @$_GET["value"];
$newcentre = (int) @$_GET["newcentre"];
$newgrp    = (int) @$_GET["newgrp"];
$newclass  = (int) @$_GET["newclass"];
$newmat    = (int) @$_GET["newmat"];
$newmotif   = (int) @$_GET["newmotif"];
$newsalle   = (int) @$_GET["newsalle"];
$newforfait   = (int) @$_GET["newforfait"];
$optiona    = @$_POST["option"];
$matclass  = @$_POST["matclass"];
$profclass  = @$_POST["profclass"];
$goclass  = @$_POST["goclass"];
$gosalle  = @$_POST["gosalle"];
$supplogo  = @$_POST["supplogo"];
$forfaitval  = @$_POST["forfaitval"];
$pane    = ( @$_POST["pane"] )
	? $_POST["pane"]
	: @$_GET["pane"] ;
if($optiona == "on")
{
	$option = 1;
}
else
{
	$option = 0;
}

$submit    = ( @$_POST["submit"] )		// bouton de validation
	? $_POST["submit"]
	: (@$_GET["submit"] ? $_GET["submit"] : @$_POST["valid_x"]) ;

$color1 = strtoupper($color1);

?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// initialisation
	$query  = "";

	// création
	if ( $submit == $msg->read($CONFIG_CREAT) )
	{
		switch ( $tablidx ) {
			case 1 :
				if ( $ident != "" ) {
					// mise à jour des mot-clefs
					for ($i = 0; $i < 3; $i++) {
						$query  = "insert into config_def ";
						$query .= "values('', '".sql_getunique("config_centre")."', '$keywords_search[$i]', '$keywords_replace[$i]', '".$_SESSION["lang"]."')";
						@mysqli_query($mysql_link, $query);
						}

					$query  = "insert into config_centre ";
					$query .= "values('".sql_getunique("config_centre")."', '$ident', '', '', '', '', '', 'O', '".$_SESSION["lang"]."', '[1,\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\"]', '{\"start\":[\"\",\"\",\"\"],\"end\":[\"\",\"\",\"\"]}')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 2 :
				if ( $ident != "" ) {
					$query  = "insert into user_group ";
					$query .= "values('".sql_getunique("user_group")."', '$ident', '$delay', '$HDQUOTAS', '1', 'O', '".$_SESSION["lang"]."')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 3 :
				if ( $text != "" )
				{
					$query  = "insert into campus_classe ";
					$query .= "values('', '0', '255', '255', '$IDcentre', '$text', '', '0', '0', 'N', 'N', '', 'O', '$code','".$_SESSION["lang"]."')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					$lastidclass = mysqli_insert_id($mysql_link);

					// les matières des classes

					// Suppression des matières de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRuser = 0 ";
					$query .= "AND _IDRclass = $lastidclass ";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

					for ($i = 0; $i < count($matclass); $i++ )
					{
						$query  = "INSERT INTO class_mat_user ";
						$query .= "VALUE ('$lastidclass', '$matclass[$i]', '0', '') ";
						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}

					$query = "";

					// les profs des classes

					// Suppression des profs de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRmat = 0 ";
					$query .= "AND _IDRclass = $lastidclass ";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

					for ($i = 0; $i < count($profclass); $i++ )
					{
						$query  = "INSERT INTO class_mat_user ";
						$query .= "VALUE ('$lastidclass', '', '$profclass[$i]', '') ";
						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}

					$query = "";

					// Traitement du logo
					// Toujours vérifier si le répertoire logo est existant
					if(!is_dir("download/classe/files/".$lastidclass))
					{
						mkdir("download/classe/files/".$lastidclass, 0777, true);
						fopen("download/classe/files/".$lastidclass."/index.php", "w");
					}
					$content_dir = "download/classe/files/$lastidclass/"; // dossier où sera déplacé le logo

					$tmp_file = $_FILES['logo']['tmp_name'];

					if( is_uploaded_file($tmp_file) )
					{
						// on vérifie maintenant l'extension
						$type_file = $_FILES['logo']['type'];

						if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') && !strstr($type_file, 'png'))
						{
							print("Le logo n'est pas une image");
						}

						// on copie le logo dans le dossier de destination
						//$name_file = $_FILES['logo']['name'];
						$name_file = "logo.png";

						if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
						{
							echo "Impossible de copier le logo dans $content_dir";
						}
					}
				}
				break;
			case 4 :
				if ( $titre != "" ) {
					// on crée une entrée dans les ressources pour la nouvelle matière
					$query  = "insert into resource_data ";
					$query .= "values('', '2', '0', '".$_SESSION["CnxGrp"]."', '255', '$titre', '', '0', '1', 'N', '1', 'O', 'O', '".$_SESSION["lang"]."')";

					// on enregistre le forfait de la matière
					$query  = "insert into mat_forfait ";
					$query .= "values('$forfaitval', '".sql_getunique("resource_data")."')";
					@mysqli_query($mysql_link, $query);

					@mysqli_query($mysql_link, $query);

					$query  = "insert into campus_data ";
					$query .= "values('".sql_getunique("campus_data")."', '0', '".$_SESSION["CnxGrp"]."', '255', '', '$titre', '".$msg->read($CONFIG_WELCOMEPAGE, $titre)."', '10', 'N', 'N', '', 'O', '".$_SESSION["lang"]."', '$color1', $option, '$code')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 5 :
				if ( $ident != "" ) {
					$query  = "insert into absent_data ";
					$query .= "values('".sql_getunique("absent_data")."', '$ident', 'O', '".$_SESSION["lang"]."')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 6 :
				if ( $text != "" ) {
					$query  = "insert into edt_items ";
					$query .= "values('".sql_getunique("edt_items")."', '$IDcentre', '$text', '".$_SESSION["lang"]."')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 7 :
				if ( $identforfait != "" ) {

					$Query  = "select _IDforfait from forfait ";
					$Query .= "order by _IDforfait desc ";
					$Query .= "limit 1";
					$result = mysqli_query($mysql_link, $Query);
					$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
					$newidforfait = $row[0] + 1;

					$query  = "insert into forfait ";
					$query .= "values('$newidforfait', '$identforfait')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			default :
				break;
			}
	}

	// modification
	if ( $submit == $msg->read($CONFIG_MODIFY) )
	{
		switch ( $tablidx )
		{
			case 1 :
				if ( $ident != "" ) {
					$query  = "update config_centre ";
					$query .= "set _ident = '$ident' ";
					$query .= "where _IDcentre = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 2 :
				if ( $ident != "" ) {
					$query  = "update user_group ";
					$query .= "set _ident = '$ident', _delay = '$delay' ";
					$query .= "where _IDgrp = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 3 :
				if ( $text != "" )
				{
					$query  = "update campus_classe ";
					$query .= "set _IDcentre = '$IDcentre', _ident = '$text', _code = '$code' ";
					$query .= "where _IDclass = '$idrec'";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

					// Traitement du logo
					// Toujours vérifier si le répertoire logo est existant
					if(!is_dir("download/classe/files/".$idrec))
					{
						mkdir("download/classe/files/".$idrec, 0777, true);
						fopen("download/classe/files/".$idrec."/index.php", "w");
					}

					$content_dir = "download/classe/files/$idrec/"; // dossier où sera déplacé le logo

					$tmp_file = $_FILES['logo']['tmp_name'];

					// Suppression du logo si la case est coché (pas obligatoire si remplacement de logo)
					if($supplogo == "oui")
					{
						if(file_exists("download/classe/files/$idrec/logo.png"))
						{
							unlink("download/classe/files/$idrec/logo.png");
						}
					}

					if( is_uploaded_file($tmp_file) )
					{
						// on vérifie maintenant l'extension
						$type_file = $_FILES['logo']['type'];

						if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') && !strstr($type_file, 'png'))
						{
							print("Le logo n'est pas une image");
						}

						// on copie le logo dans le dossier de destination
						//$name_file = $_FILES['logo']['name'];
						$name_file = "logo.png";

						if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
						{
							print("Impossible de copier le logo dans $content_dir");
						}
					}
				}
				break;
			case 4 :
				if ( $titre != "" ) {
					// on crée une entrée dans les ressources pour la nouvelle matière
					$query  = "insert into resource_data ";
					$query .= "values('', '2', '0', '".$_SESSION["CnxGrp"]."', '255', '$titre', '', '0', '1', 'N', '1', 'O', 'O', '".$_SESSION["lang"]."')";
					@mysqli_query($mysql_link, $query);

					// on enregistre le forfait de la matière
					$query  = "select _IDforfait from mat_forfait ";
					$query .= "where _IDmat = '$idrec' ";
					$result = mysqli_query($mysql_link, $query);
					$num_rows = mysqli_num_rows($result);
					if ( $num_rows > 0) {
					$query  = "update mat_forfait ";
					$query .= "set _IDforfait = '$forfaitval' ";
					$query .= "where _IDmat = '$idrec' limit 1";
					} else {
					$query  = "insert into mat_forfait ";
					$query .= "values('$forfaitval', '$idrec')";
					}
					@mysqli_query($mysql_link, $query);

					$query  = "update campus_data ";
					$query .= "set _titre = '$titre', _color = '$color1', _option = '$option', _code = '$code' ";
					$query .= "where _IDmat = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 5 :
				if ( $ident != "" ) {
					$query  = "update absent_data ";
					$query .= "set _texte = '$ident' ";
					$query .= "where _IDdata = '$idrec'";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 6 :
				if ( $text != "" ) {
					$query  = "update edt_items ";
					$query .= "set _title = '$text' ";
					$query .= "where _IDitem = '$idrec'";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 7 :
				if ( $identforfait != "" ) {
					$query  = "update forfait ";
					$query .= "set _Nom = '$identforfait' ";
					$query .= "where _IDforfait = '$idrec'";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				default :
				break;
		}

		// les matières des classes

		// Suppression des matières de la classe
		$query  = "DELETE FROM class_mat_user ";
		$query .= "WHERE _IDRuser = 0 ";
		$query .= "AND _IDRclass = $idrec ";
		@mysqli_query($mysql_link, $query);

		for ($i = 0; $i < count($matclass); $i++ )
		{
			$query  = "INSERT INTO class_mat_user ";
			$query .= "VALUE ('$idrec', '$matclass[$i]', '0', '') ";
			mysqli_query($mysql_link, $query);
		}

		$query = "";

		// les profs des classes

		// Suppression des profs de la classe
		$query  = "DELETE FROM class_mat_user ";
		$query .= "WHERE _IDRmat = 0 ";
		$query .= "AND _IDRclass = $idrec ";
		@mysqli_query($mysql_link, $query);

		for ($i = 0; $i < count($profclass); $i++ )
		{
			$query  = "INSERT INTO class_mat_user ";
			$query .= "VALUE ('$idrec', '', '$profclass[$i]', '') ";
			mysqli_query($mysql_link, $query);
		}

		$query = "";
	}

	// droits étendus et catégorie des groupes
	if ( $submit == "toggle" ) {
		$idcat  = ( $idcat ) ? ($idcat % 3) + 1 : 0 ;

		$query  = "update user_group ";
		$query .= ( $idcat ) ? "set _IDcat = '$idcat'" : "" ;
		$query .= " where _IDgrp = '$value' AND _lang = '".$_SESSION["lang"]."' limit 1";
		}

	if ( $query != "" )
		mysqli_query($mysql_link, $query);

	// l'utilisateur a validé
	if ( @$_POST["valid_x"] )
	{
		//---- suppression ----
		$delc = @$_POST["delc"];
		$delg = @$_POST["delg"];
		$delk = @$_POST["delk"];
		$delm = @$_POST["delm"];
		$delmo = @$_POST["delmo"];
		$delsa = @$_POST["delsa"];
		$delforfait = @$_POST["delforfait"];

		// suppression des centres
		for ($i = 0; $i < count($delc); $i++ )
			if ( @$delc[$i] )
			{
				$query  = "delete from config_centre ";
				$query .= "where _IDcentre = '$delc[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

				if ( mysqli_query($mysql_link, $query) )
				{
					// suppression des mot-clefs du centre
					// Que si IDcentre autre que 1
					if($delc[$i] != 1)
					{
						$query  = "delete from config_def ";
						$query .= "where _IDcentre = '$delc[$i]' AND _lang = '".$_SESSION["lang"]."' ";
						@mysqli_query($mysql_link, $query);
					}

					// suppression des classes du centre
					$query  = "delete from campus_classe ";
					$query .= "where _IDcentre = '$delc[$i]' ";
					@mysqli_query($mysql_link, $query);

					// suppression des salles du centre
					$query  = "delete from edt_items ";
					$query .= "where _IDcentre = '$delc[$i]' ";
					@mysqli_query($mysql_link, $query);
				}
			}

		// suppression des groupes
		for ($i = 0; $i < count($delg); $i++ )
			if ( @$delg[$i] ) {
				$query  = "delete from user_group ";
				$query .= "where _IDgrp = '$delg[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

				@mysqli_query($mysql_link, $query);
				}

		// suppression des classes
		for ($i = 0; $i < count($delk); $i++ )
			if ( @$delk[$i] ) {
				$query  = "delete from campus_classe ";
				$query .= "where _IDclass = '$delk[$i]' ";

				@mysqli_query($mysql_link, $query);
				}

		// suppression des matières
		for ($i = 0; $i < count($delm); $i++ )
			if ( @$delm[$i] ) {
				$query  = "delete from campus_data ";
				$query .= "where _IDmat = '$delm[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

				@mysqli_query($mysql_link, $query);
				}

		// suppression des motifs
		for ($i = 0; $i < count($delmo); $i++ )
			if ( @$delmo[$i] ) {
				$query  = "delete from absent_data ";
				$query .= "where _IDdata = '$delmo[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

				@mysqli_query($mysql_link, $query);
				}

		// suppression des salles
		for ($i = 0; $i < count($delsa); $i++ )
			if ( @$delsa[$i] ) {
				$query  = "delete from edt_items ";
				$query .= "where _IDitem = '$delsa[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

				@mysqli_query($mysql_link, $query);
				}

		// suppression des forfaits
		for ($i = 0; $i < count($delforfait); $i++ )
			if ( @$delforfait[$i] ) {
				$query  = "delete from forfait ";
				$query .= "where _IDforfait = '$delforfait[$i]' limit 1";

				@mysqli_query($mysql_link, $query);
				}

		//---- rendre visible/invisible ----
		$isc  = @$_POST["isc"];
		$isg  = @$_POST["isg"];
		$isk  = @$_POST["isk"];
		$ism  = @$_POST["ism"];
		$ismo = @$_POST["ismo"];

		// les centres
		$query  = "update config_centre ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if ( mysqli_query($mysql_link, $query) )
			for ($i = 0; $i < count($isc); $i++ )
				if ( @$isc[$i] ) {
					$query  = "update config_centre ";
					$query .= "set _visible = 'O' ";
					$query .= "where _IDcentre = '$isc[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

					@mysqli_query($mysql_link, $query);
					}

		// les groupes
		$query  = "update user_group ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if ( mysqli_query($mysql_link, $query) )
			for ($i = 0; $i < count($isg); $i++ )
				if ( @$isg[$i] ) {
					$query  = "update user_group ";
					$query .= "set _visible = 'O' ";
					$query .= "where _IDgrp = '$isg[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

					@mysqli_query($mysql_link, $query);
					}

		// les classes
		$query  = "update campus_classe ";
		$query .= "set _visible = 'N' ";
		$query .= "where _IDcentre = '$IDcentre' ";

		if ( mysqli_query($mysql_link, $query) )
			for ($i = 0; $i < count($isk); $i++ )
				if ( @$isk[$i] ) {
					$query  = "update campus_classe ";
					$query .= "set _visible = 'O' ";
					$query .= "where _IDcentre = '$IDcentre' AND _IDclass = '$isk[$i]' limit 1";

					@mysqli_query($mysql_link, $query);
					}

		// les matières
		$query  = "update campus_data ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if ( mysqli_query($mysql_link, $query) )
			for ($i = 0; $i < count($ism); $i++ )
				if ( @$ism[$i] ) {
					$query  = "update campus_data ";
					$query .= "set _visible = 'O' ";
					$query .= "where _IDmat = '$ism[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

					@mysqli_query($mysql_link, $query);
					}

		// les motifs
		$query  = "update absent_data ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if ( mysqli_query($mysql_link, $query) )
			for ($i = 0; $i < count($ismo); $i++ )
				if ( @$ismo[$i] ) {
					$query  = "update absent_data ";
					$query .= "set _visible = 'O' ";
					$query .= "where _IDdata = '$ismo[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

					@mysqli_query($mysql_link, $query);
					}
	}

?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($CONFIG_DBACONFIG)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="index.php" method="post" enctype="multipart/form-data">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDconf\"   value=\"$IDconf\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"goclass\" id=\"goclass\" value=\"$goclass\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"gosalle\" id=\"gosalle\" value=\"$gosalle\" /></p>
			");
	?>

	<?php include("include/config_menu_top.php"); ?>

	<hr/>

	<div class="tabbable"> <!-- Only required for left/right tabs -->
	<table width="100%"><tr><td>

		<ul class="nav nav-tabs" style="height: 37px; margin-bottom: 0px;">
			<li <?php if (($newcentre !="") || ($pane == "panecentre")) echo "class=\"active\""; if (($tablidx =="") && ($idcat =="") && ($goclass != "go") && ($gosalle != "go")) echo "class=\"active\""; ?>>
				<a href="#tab1" data-toggle="tab" onclick="jQuery('#goclass').val(''); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'none');">
					<?php print($msg->read($CONFIG_CENTERS)); ?>
				</a>
			</li>
			<li <?php if (($newgrp !="") || ($idcat !="")) echo "class=\"active\""; ?>>
				<a href="#tab2" data-toggle="tab" onclick="jQuery('#goclass').val(''); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'none');">
					<?php print($msg->read($CONFIG_GROUPS)); ?>
				</a>
			</li>
			<li <?php if (($newclass !="") || ($pane == "paneclass") || ($goclass == "go")) echo "class=\"active\""; ?>>
				<a href="#tab3" data-toggle="tab" onclick="jQuery('#goclass').val('go'); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'block');">
					<?php print($msg->read($CONFIG_CLASS)); ?>
				</a>
			</li>
			<li <?php if (($newmat !="") || ($pane == "panemat")) echo "class=\"active\""; ?>>
				<a href="#tab4" data-toggle="tab" onclick="jQuery('#goclass').val(''); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'none');">
					<?php print($msg->read($CONFIG_MATTER)); ?>
				</a>
			</li>
			<li <?php if (($newmotif !="") || ($pane == "panemotif")) echo "class=\"active\""; ?>>
				<a href="#tab5" data-toggle="tab" onclick="jQuery('#goclass').val(''); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'none');">
					<?php print($msg->read($CONFIG_MOTIF)); ?>
				</a>
			</li>


			<!-- Onglet Pôle -->
			<li <?php if (($newcentre !="") || ($pane == "panepole")) echo "class=\"active\""; if (($tablidx =="") && ($idcat =="") && ($goclass != "go") && ($gosalle != "go")) echo "class=\"active\""; ?>>
				<a href="#tab7" data-toggle="tab" onclick="jQuery('#goclass').val(''); jQuery('#gosalle').val(''); jQuery('#IDcentre').css('display', 'none');">
					<?php print($msg->read($CONFIG_POLE)); ?>
				</a>
			</li>


			<?php if(getParam("FORFAIT") == 1)
			{
				?>
				<li <?php if (($newforfait !="") || ($pane == "paneforfait")) echo "class=\"active\""; ?>><a href="#tab7" data-toggle="tab"><strong>Forfait</strong></a></li>
				<?php
			}
			?>
		</ul>
		</div>

	</td></tr>
	<tr><td>

    <div class="tab-content">

	<?php

	$displaycentre = (($goclass != "go" && $gosalle != "") || ($goclass != "" && $gosalle != "go")) ? "display: block" : "display: none";
	print("<label for=\"IDcentre\" style=\"margin-bottom: 0px;\">");
	print("<select id=\"IDcentre\" name=\"IDcentre\" onchange=\"document.forms.formulaire.submit()\" style=\"margin-bottom: 0px; $displaycentre\">");

	// lecture des centres constitutifs
	$query  = "select _IDcentre, _ident from config_centre ";
	$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
	$query .= "order by _IDcentre";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while ( $row ) {
		if ( $IDcentre == $row[0] )
			print("<option selected=\"selected\" value=\"$row[0]\">$row[1]</option>");
		else
			print("<option value=\"$row[0]\">$row[1]</option>");

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	print("</select>");
	print("</label>");
	?>

    <div class="tab-pane <?php if (($newcentre !="") || ($pane == "panecentre")) echo "active"; if (($tablidx =="") && ($idcat =="") && ($goclass != "go") && ($gosalle != "go")) echo "active"; ?>" id="tab1" style="background-color: #ffffff;">
	<fieldset style="margin-bottom:5px;">

	  <legend><?php print($msg->read($CONFIG_CENTERS)); ?></legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-eye-open icon-white\"></i>
				</td>
				<td style=\"width:1%;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			// initialisation des liens
			$mylink = "index.php?item=$item&amp;cmde=dba&amp;IDconf=$IDconf&amp;IDcentre=$IDcentre";

			// lecture des centres constitutifs
			$query  = "select _IDcentre, _ident, _visible from config_centre ";
			$query .= "where _lang = '".$_SESSION["lang"]."' ";
			$query .= "order by _IDcentre";

			$result = mysqli_query($mysql_link, $query);
			$centre = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $centre ) {
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

					print("<tr class=\"$bgcolor\">");

				$checked = ( $centre[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            	$update  = "<a href=\"$mylink&amp;tablidx=1&amp;newcentre=$centre[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

				print("
					<td class=\"align-center\">
 						<label for=\"isc_$centre[0]\"><input type=\"checkbox\" id=\"isc_$centre[0]\" name=\"isc[]\" value=\"$centre[0]\" $checked /></label>
					</td>
					<td class=\"align-center\">
 						<label for=\"delc_$centre[0]\"><input type=\"checkbox\" id=\"delc_$centre[0]\" name=\"delc[]\" value=\"$centre[0]\" onclick=\"return confirmDelCentre(this, 'ATTENTION: La suppresion du centre entrainera la SUPPRESSION TOTALE ET DEFINITIVE des classes ainsi que des salles qui lui sont rattachées!');\" /></label>
					</td>
					<td>$centre[0]. $centre[1] $update</td>
					");

					print("</tr>");

				$centre = remove_magic_quotes(mysqli_fetch_row($result));
				}

			if ( $i % 2 )
				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

			if ( $newcentre ) {
				$value = ( $newcentre > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				// recherche du centre constitutif
				$result = mysqli_query($mysql_link, "select _ident from config_centre where _IDcentre = '$newcentre' AND _lang = '".$_SESSION["lang"]."' limit 1");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding: 5px;\">
						<label for=\"ident\"><input type=\"text\" id=\"ident\" name=\"ident\" size=\"30\" value=\"$row[0]\" style=\"margin: 0;\" placeholder=\"".$msg->read($CONFIG_NOMCENTRE)."\" /></label>
						<input type=\"submit\" value=\"$value\" name=\"submit\" class=\"btn btn-success\" />
						<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"1\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newcentre\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"pane\"   value=\"panecentre\" /></p>
		            	</td>
			          </tr>
					");
				}
			else
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px;\">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=1&amp;newcentre=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");

		 ?>

		</table>
	</fieldset>

	</div>
    <div class="tab-pane <?php if (($newgrp !="") || ($_GET["idcat"] !="")) echo "active"; ?>" id="tab2" style="background-color: #ffffff;">

	<fieldset style="margin-bottom:5px;">
	  <legend><?php print($msg->read($CONFIG_GROUPS)); ?></legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-eye-open icon-white\"></i>
				</td>
				<td style=\"width:1%;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			// lecture des groupes
			$Query  = "select _IDgrp, _ident, _visible, _IDcat from user_group ";
			$Query .= "where _lang = '".$_SESSION["lang"]."' ";
			$Query .= "order by _IDgrp";

			// affichage des groupes d'utilisateurs
			$result = mysqli_query($mysql_link, $Query);
			$grp    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $grp ) {
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

				// affichage des catégories des groupes
				$return = mysqli_query($mysql_link, "select _ident from user_category where _IDcat = '$grp[3]' AND _lang = '".$_SESSION["lang"]."' limit 1");
				$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

					print("<tr class=\"$bgcolor\">");

				$checked = ( $grp[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            	$update  = "<a href=\"$mylink&amp;tablidx=2&amp;newgrp=$grp[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

	            	$mycat   = "<a href=\"$mylink&amp;idcat=$grp[3]&amp;value=$grp[0]&amp;submit=toggle\"><img src=\"".$_SESSION["ROOTDIR"]."/images/cat-$grp[3].gif\" title=\"$myrow[0]\" alt=\"$myrow[0]\" /></a>";

				print("
					<td class=\"align-center\">
 						<label for=\"isg_$grp[0]\"><input type=\"checkbox\" id=\"isg_$grp[0]\" name=\"isg[]\" value=\"$grp[0]\" $checked /></label>
					</td>
					<td class=\"align-center\">
 						<label for=\"delg_$grp[0]\"><input type=\"checkbox\" id=\"delg_$grp[0]\" name=\"delg[]\" value=\"$grp[0]\" /></label>
					</td>
					<td>
						$grp[0]. $grp[1]
						$update
					</td>
					");

					print("</tr>");

				$grp = remove_magic_quotes(mysqli_fetch_row($result));
				}

			if ( $i % 2 )
				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

			if ( $newgrp ) {
				$value = ( $newgrp > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				// affichage des groupes
				$result = mysqli_query($mysql_link, "select _ident, _delay from user_group where _IDgrp = '$newgrp' AND _lang = '".$_SESSION["lang"]."' limit 1");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding: 5px;\">
						<label for=\"ident\"><input type=\"text\" id=\"ident\" name=\"ident\" size=\"30\" value=\"$row[0]\" style=\"margin: 0;\" placeholder=\"".$msg->read($CONFIG_NOMGROUPE)."\" /> ". $msg->read($CONFIG_ISVALID) ." </label>
						<label for=\"delay\"><input type=\"text\" id=\"delay\" name=\"delay\" size=\"20\" value=\"$row[1]\" style=\"margin: 0;\" placeholder=\"0000-00-00 00:00:00\" /></label> <input type=\"submit\" value=\"$value\" name=\"submit\" class=\"btn btn-success\" />
						<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"2\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newgrp\" /></p>
		            	</td>
			          </tr>
					");
				}
			else
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px;\">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=2&amp;newgrp=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");
		 ?>

		</table>
	</fieldset>

	</div>
    <div class="tab-pane <?php if (($newclass !="") || ($pane == "paneclass") || ($goclass == "go")) echo "active"; ?>" id="tab3" style="background-color: #ffffff;">

	<fieldset style="margin-bottom:5px;">
	  <legend>
		<?php
		print($msg->read($CONFIG_CLASS) ." ");
		?>
	  </legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-eye-open icon-white\"></i>
				</td>
				<td style=\"width:1%;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			// lecture des classes
			$Query  = "select _IDclass, _ident, _visible, _code from campus_classe ";
			$Query .= "where _IDcentre = '$IDcentre' ";
			$Query .= "order by _IDclass";

			// affichage des classes
			$result = mysqli_query($mysql_link, $Query);
			$classe = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $classe ) {
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

					print("<tr class=\"$bgcolor\">");

				$checked = ( $classe[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            	$update  = "<a href=\"$mylink&amp;tablidx=3&amp;newclass=$classe[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

				print("
					<td class=\"align-center\">
 						<label for=\"isk_$classe[0]\"><input type=\"checkbox\" id=\"isk_$classe[0]\" name=\"isk[]\" value=\"$classe[0]\" $checked /></label>
					</td>
					<td class=\"align-center\">
 						<label for=\"delk_$classe[0]\"><input type=\"checkbox\" id=\"delk_$classe[0]\" name=\"delk[]\" value=\"$classe[0]\" /></label>
					</td>
					<td>$classe[0]. $classe[1] [$classe[3]] $update</td>
					");

					print("</tr>");

				$classe = remove_magic_quotes(mysqli_fetch_row($result));
				}

			if ( $i % 2 )
				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

			if ( $newclass )
			{
				$value = ( $newclass > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				// affichage des centres constitutifs
				$result = mysqli_query($mysql_link, "select _ident, _code from campus_classe where _IDclass = '$newclass'");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 30px; padding-top: 10px\">
						<label for=\"code\"><input type=\"text\" id=\"code\" name=\"code\" size=\"10\" value=\"$row[1]\" placeholder=\"".$msg->read($CONFIG_NOMCODE)."\" /></label> <label for=\"texte\"><input type=\"text\" id=\"text\" name=\"text\" size=\"30\" value=\"$row[0]\" placeholder=\"".$msg->read($CONFIG_NOMCLASS)."\" /></label>
						<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"3\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newclass\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"pane\"   value=\"paneclass\" /></p>
		            	</td>
			          </tr>
					");

				/*----------- MATIERES ------------*/
				$tab_matclass = getMatClass($newclass, $_SESSION["lang"]);

				echo "<tr><td colspan=\"6\">";
				echo "<h4 style=\"margin-left: 30px;\">".$msg->read($CONFIG_MATCLASS)."</h4><ul>";
				// lecture des matières
				$Query  = "select _IDmat, _titre, _visible, _option, _code from campus_data ";
				$Query .= "where _lang = '".$_SESSION["lang"]."' ";
				$Query .= "order by _titre";

				// affichage des matières
				$result = mysqli_query($mysql_link, $Query);
				$mat    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				$i = 0;
				while ( $mat )
				{
					$option = ($mat[3] == 1) ? "btn-primary" : "";
					$checked = (array_key_exists($mat[0], $tab_matclass)) ? "checked" : "";
					echo "<li style=\"list-style: none; display: inline-block; margin: 5px; background-color: #cccccc; border-radius: 4px; padding: 3px;\" class=\"$option\"><input type=\"checkbox\" id=\"matclassid_$mat[0]\" name=\"matclass[]\" value=\"$mat[0]\" style=\"margin-top: -2px\" $checked/> <label for=\"matclassid_$mat[0]\"> $mat[1]</label></li>";
					$mat = remove_magic_quotes(mysqli_fetch_row($result));
				}
				echo "</ul>";
				echo "</td></tr>";
				?>
				<tr>
					<td colspan="6" style="border-top: #cccccc solid 1px; padding-left: 30px; padding-top: 10px">
						<h4>Logo (Optionnel)</h4>
						<input type="file" name="logo" size="30">
						<?php
						if(file_exists("download/classe/files/$newclass/logo.png"))
						{
							?>
							<img src="download/classe/files/<?php echo $newclass; ?>/logo.png" />
							<br /><input type="checkbox" name="supplogo" id="supplogo" value="oui" /> <label for="supplogo">Supprimer le logo</label>
							<?php
						}
						?>
				</tr>

				<?php
				/*----------- PROFS ------------
				$tab_profclass = getClasseProf($newclass);

				echo "<tr><td colspan=\"6\"><h4 style=\"margin-left: 30px;\">".$msg->read($CONFIG_MATPROF)."</h4><ul>";
				// lecture des profs
				$Query  = "select _ID, _name, _fname from user_id ";
				$Query .= "where _IDgrp = 2 ";
				$Query .= "order by _ID";

				// affichage des matières
				$result = mysqli_query($mysql_link, $Query);
				$prof    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				$i = 0;
				while ( $prof )
				{
					$checked = (array_key_exists($prof[0], $tab_profclass)) ? "checked" : "";
					echo "<li style=\"list-style: none; display: inline-block; margin: 5px; background-color: #cccccc; border-radius: 4px; padding: 3px;\"><input type=\"checkbox\" id=\"profclassid_$prof[0]\" name=\"profclass[]\" value=\"$prof[0]\" style=\"margin-top: -2px\" $checked/> <label for=\"profclassid_$prof[0]\"> $prof[1] $prof[2]</label></li>";
					$prof = remove_magic_quotes(mysqli_fetch_row($result));
				}
				echo "</ul></td></tr>";
				*/
			}
			else
			{
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px;\">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=3&amp;newclass=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");
			}

		 ?>

		</table>
		<?php
		if ( $newclass )
		{
			?>
			<hr />
			<input type="submit" value="<?php echo $value; ?>" style="margin-left: 30px" class="btn btn-success" name="submit" />
			<br /><br />
			<?php
		}
		?>
	</fieldset>
	</div>

	<div class="tab-pane <?php if (($newmat !="") || ($pane == "panemat")) echo "active"; ?>" id="tab4" style="background-color: #ffffff;">

	<fieldset style="margin-bottom:5px;">
	  <legend><?php print($msg->read($CONFIG_MATTER)); ?></legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-eye-open icon-white\"></i>
				</td>
				<td style=\"width:1%;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			if ( $newmat ) {
				$value = ( $newmat > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				$result = mysqli_query($mysql_link, "select _titre, _color, _option, _code from campus_data where _IDmat = '$newmat' AND _lang = '".$_SESSION["lang"]."' limit 1");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				$color1 = $row[1];
				switch ($color1) {
					case "0" :
						$color1 = "AAAAAA";
					break;
					case "1" :
						$color1 = "D96666";
					break;
					case "2" :
						$color1 = "E67399";
					break;
					case "3" :
						$color1 = "B373B3";
					break;
					case "4" :
						$color1 = "8C66D9";
					break;
					case "5" :
						$color1 = "668CB3";
					break;
					case "6" :
						$color1 = "668CD9";
					break;
					case "7" :
						$color1 = "59BFB3";
					break;
					case "8" :
						$color1 = "65AD89";
					break;
					case "9" :
						$color1 = "4CB052";
					break;
					case "10" :
						$color1 = "8CBF40";
					break;
					case "11" :
						$color1 = "BFBF4D";
					break;
					case "12" :
						$color1 = "E0C240";
					break;
					case "13" :
						$color1 = "F2A640";
					break;
					case "14" :
						$color1 = "E6804D";
					break;
					case "15" :
						$color1 = "BE9494";
					break;
					case "16" :
						$color1 = "A992A9";
					break;
					case "17" :
						$color1 = "8997A5";
					break;
					case "18" :
						$color1 = "94A2BE";
					break;
					case "19" :
						$color1 = "85AAA5";
					break;
					default :
						$color1 = "AAAAAA";
					break;
				}
				$color1 = strtolower($color1);

				if(getParam("FORFAIT") == 1)
				{
					$resulta = mysqli_query($mysql_link, "SELECT _IDforfait FROM mat_forfait WHERE _IDmat = $newmat ");
					$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;

						// select des forfaits
						$querylf  = "select _IDforfait, _Nom from forfait ";

						$resultlf = mysqli_query($mysql_link, $querylf);
						$rowlf    = ( $resultlf ) ? remove_magic_quotes(mysqli_fetch_row($resultlf)) : 0 ;
						$forfaitval = "<label for=\"forfaitval\">";
						$forfaitval .= "<select id=\"forfaitval\" name=\"forfaitval\" ><option value=\"\" ></option>";

						while ( $rowlf ) {
						$selected = "";
						if ($rowa[0] == $rowlf[0]) $selected = "selected=\"selected\"";
						$forfaitval .= "<option value=\"$rowlf[0]\" $selected >$rowlf[1]</option>";
						$rowlf = remove_magic_quotes(mysqli_fetch_row($resultlf));
						}
						$forfaitval .= "</select>";
				}

				print("
					  <tr>
						<td colspan=\"6\">");
				?>

				<table border="0" width="100%">
					<tr>
						<td colspan="2" style="border-top: 1px solid #CCCCCC; padding: 5px;">
							<?php
								echo "<label for=\"code\"><input required=\"required\" type=\"text\" id=\"code\" name=\"code\" size=\"10\" value=\"".$row[3]."\" style=\"margin: 0;\" placeholder=\"".$msg->read($CONFIG_NOMCODE)."\" /></label> <label for=\"titre\"><input type=\"text\" id=\"titre\" name=\"titre\" size=\"30\" value=\"$row[0]\" style=\"margin: 0;\" placeholder=\"".$msg->read($CONFIG_NOMMATIERE)."\" /></label>";
							?>
						</td>
					</tr>
					<?php
					if(getParam("FORFAIT") == 1)
					{
						?>
						<tr>
							<td colspan="2">
								<?php echo $forfaitval; ?><br />

							</td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td style="width: 100px;">
							<div class="controlset"><strong><?php echo $msg->read($CONFIG_NOMCOULEUR); ?></strong> <input id="color1" type="text" name="color1" /></div>
							<script>
							jQuery('#color1').colorPicker({pickerDefault: "<?php echo $color1; ?>", colors: ["AAAAAA", "D96666", "E67399", "B373B3", "8C66D9", "668CB3", "668CD9", "59BFB3", "65AD89", "4CB052", "8CBF40", "BFBF4D", "E0C240", "F2A640", "E6804D", "BE9494", "A992A9", "8997A5", "94A2BE", "85AAA5"], transparency: true});;
							</script>
						</td>
						<td>
							<input type="submit" value="<?php echo $value; ?>" name="submit" class="btn btn-success" />
						</td>
					</tr>
				</table>

				<?php
				print("
			          <tr>
		            	<td colspan=\"6\">
					  <p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"4\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newmat\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"pane\"   value=\"panemat\" /></p>
		            	</td>
			          </tr>
					");
				}
			else
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px;\">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=4&amp;newmat=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");


			// lecture des matières
			$Query  = "select _IDmat, _titre, _visible, _option, _code, _color from campus_data ";
			$Query .= "where _lang = '".$_SESSION["lang"]."' ";
			$Query .= "order by _titre";

			// affichage des matières
			$result = mysqli_query($mysql_link, $Query);
			$mat    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $mat ) {
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

				print("<tr class=\"$bgcolor\">");

				$checked = ( $mat[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            $update  = "<a href=\"$mylink&amp;tablidx=4&amp;newmat=$mat[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

				if(getParam("FORFAIT") == 1)
				{
					$valforfait ="";
					$rowa = "";
					$resulta = mysqli_query($mysql_link, "SELECT _IDforfait FROM mat_forfait WHERE _IDmat = $mat[0] ");
					$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;

					if ($rowa[0] != "")
					{
						$resulta = mysqli_query($mysql_link, "SELECT _Nom FROM forfait WHERE _IDforfait = $rowa[0] ");
						$rowb    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;
						$valforfait = " (".$rowb[0].")";
					}
				}

				print("
					<td class=\"align-center\">
 						<label for=\"ism_$mat[0]\"><input type=\"checkbox\" id=\"ism_$mat[0]\" name=\"ism[]\" value=\"$mat[0]\" $checked /></label>
					</td>
					<td class=\"align-center\">
 						<label for=\"delm_$mat[0]\"><input type=\"checkbox\" id=\"delm_$mat[0]\" name=\"delm[]\" value=\"$mat[0]\" /></label>
					</td>
					<td><div style=\"display: inline-block; width: 15px; height: 15px; margin-top: 7px; background-color: ".$mat[5]."\"></div> $mat[0]. $mat[1] [$mat[4]] $valforfait $update</td>
					");

					print("</tr>");

				$mat = remove_magic_quotes(mysqli_fetch_row($result));
				}

				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

?>

		</table>
	</fieldset>
	</div>

    <div class="tab-pane <?php if (($newmotif !="") || ($pane == "panemotif")) echo "active"; ?>" id="tab5" style="background-color: #ffffff;">

	<fieldset style="margin-bottom:5px;">
	  <legend><?php print($msg->read($CONFIG_MOTIF)); ?></legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-eye-open icon-white\"></i>
				</td>
				<td style=\"width:1%;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			// lecture des motifs
			$Query  = "select _IDdata, _texte, _visible from absent_data ";
			$Query .= "where _lang = '".$_SESSION["lang"]."' ";
			$Query .= "order by _IDdata";

			// affichage des groupes d'utilisateurs
			$result = mysqli_query($mysql_link, $Query);
			$data    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $data ) {
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

					print("<tr class=\"$bgcolor\">");

				$checked = ( $data[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            $update  = "<a href=\"$mylink&amp;tablidx=5&amp;newmotif=$data[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

				print("
					<td class=\"align-center\">
 						<label for=\"ismo_$data[0]\"><input type=\"checkbox\" id=\"ismo_$data[0]\" name=\"ismo[]\" value=\"$data[0]\" $checked /></label>
					</td>
					<td class=\"align-center\">
 						<label for=\"delmo_$data[0]\"><input type=\"checkbox\" id=\"delmo_$data[0]\" name=\"delmo[]\" value=\"$data[0]\" /></label>
					</td>
					<td>
						$data[0]. $data[1]
						$update
					</td>
					");

					print("</tr>");

				$data = remove_magic_quotes(mysqli_fetch_row($result));
				}

			if ( $i % 2 )
				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

			if ( $newmotif ) {
				$value = ( $newmotif > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				// affichage des groupes
				$result = mysqli_query($mysql_link, "select _texte from absent_data where _IDdata = '$newmotif' AND _lang = '".$_SESSION["lang"]."' limit 1");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding: 5px;\">
						<input type=\"text\" id=\"ident\" name=\"ident\" size=\"30\" value=\"$row[0]\" style=\"margin: 0;\" placeholder=\"".$msg->read($CONFIG_NOMMOTIF)."\" />
						<input type=\"submit\" value=\"$value\" name=\"submit\" class=\"btn btn-success\" />
						<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"5\" /></p>
						<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newmotif\" /></p>");

				print("
		            	</td>
			          </tr>
					");
				}
			else
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px; \">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=5&amp;newmotif=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");
		 ?>

		</table>
	</fieldset>

	</div>

	<!-- Div salles -->
    <div class="tab-pane <?php if (($newsalle !="") || ($pane == "panesalle") || ($gosalle == "go")) echo "active"; ?>" id="tab6" style="background-color: #ffffff;">

	<fieldset style="margin-bottom:5px;">
	  <legend>
		<?php
		print($msg->read($CONFIG_SALLES)." ");
		?>
	  </legend>

		<table class="width100">

		<?php
			print("
		          <tr class=\"btn-primary\">
				<td style=\"width:1%; padding: 10px;\" class=\"align-center\">
					<i class=\"icon-trash icon-white\"></i>
				</td>
				<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td></tr>");

			// lecture des salles
			$Query  = "SELECT _IDitem, _title ";
			$Query .= "FROM edt_items ";
			$Query .= "WHERE _IDcentre = '$IDcentre' ";
			$Query .= "ORDER BY _IDitem";

			// affichage des classes
			$result = mysqli_query($mysql_link, $Query);
			$salle = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$i = 0;
			while ( $salle )
			{
				$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

					print("<tr class=\"$bgcolor\">");

				$checked = ( $salle[2] == "O" ) ? "checked=\"checked\"" : "" ;

	            $update  = "<a href=\"$mylink&amp;tablidx=6&amp;newsalle=$salle[0]\">";
				$update .= "<i class=\"icon-edit\" title=\"". $msg->read($CONFIG_MODIFY) ."\"></i>";
				$update .= "</a>";

				print("
					<td class=\"align-center\">
 						<label for=\"delsa_$salle[0]\"><input type=\"checkbox\" id=\"delsa_$salle[0]\" name=\"delsa[]\" value=\"$salle[0]\" onchange=\"jQuery('#gosalle').val('go');\" /></label>
					</td>
					<td>$salle[0]. $salle[1] $update</td>
					");

					print("</tr>");

				$salle = remove_magic_quotes(mysqli_fetch_row($result));
			}

			if ( $i % 2 )
				print("
					  <td></td>
					  <td></td>
					  <td></td>
					</tr>
					");

			if ( $newsalle )
			{
				$value = ( $newsalle > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

				// affichage des centres constitutifs
				$result = mysqli_query($mysql_link, "select _title from edt_items where _IDitem = '$newsalle'");
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("
				  <tr>
					<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 30px; padding-top: 10px\">
					<label for=\"texte\"><input type=\"text\" id=\"text\" name=\"text\" size=\"30\" value=\"$row[0]\" placeholder=\"".$msg->read($CONFIG_NOMSALLE)."\" /></label>
					<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"6\" /></p>
					<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newsalle\" /></p>
					<p class=\"hidden\"><input type=\"hidden\" name=\"pane\"   value=\"panesalle\" /></p>
					</td>
				  </tr>
				");
			}
			else
			{
				print("
			          <tr>
		            	<td colspan=\"6\" style=\"border: #cccccc solid 1px; padding-left: 16px; padding-top: 5px; padding-bottom: 5px;\">
						<i class=\"icon-plus-sign\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\"></i>
						<a href=\"$mylink&amp;tablidx=6&amp;newsalle=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
		            	</td>
			          </tr>
					");
			}

		 ?>

		</table>
		<?php
		if ( $newsalle )
		{
			?>
			<hr />
			<input type="submit" value="<?php echo $value; ?>" style="margin-left: 30px" class="btn btn-success" name="submit" />
			<br /><br />
			<?php
		}
		?>
	</fieldset>

	</div>
	<!-- FIN Div salles -->

	<?php
	if(getParam("FORFAIT") == 1)
	{
	?>
		<div class="tab-pane <?php if (($newforfait !="") || ($pane == "paneforfait")) echo "active"; ?>" id="tab7" style="background-color: #ffffff;">

		<fieldset style="margin-bottom:5px;">
		  <legend><strong>Forfait</strong></legend>

			<table class="width100">

			<?php
				print("
					  <tr style=\"background-color:#c0c0c0;\">
					<td style=\"width:1%;\" class=\"align-center\">

					</td>
					<td style=\"width:1%;\" class=\"align-center\">
						<img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"". $msg->read($CONFIG_DELETE) ."\" alt=\"". $msg->read($CONFIG_DELETE) ."\" />
					</td>
					<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td>
					<td style=\"width:1%;\" class=\"align-center\">
					</td>
					<td style=\"width:1%;\" class=\"align-center\">
						<img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"". $msg->read($CONFIG_DELETE) ."\" alt=\"". $msg->read($CONFIG_DELETE) ."\" />
					</td>
					<td style=\"width:48%;\">". $msg->read($CONFIG_IDENT) ."</td>
					  </tr>");

				// lecture des forfait
				$Query  = "select _IDforfait, _Nom from forfait ";
				$Query .= "order by _IDforfait";

				// affichage des forfait
				$result = mysqli_query($mysql_link, $Query);
				$data    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				$i = 0;
				while ( $data ) {
					$bgcolor = ( $i % 4 ) ? "item" : "menu" ;

					if ( $i++ % 2 == 0 )
						print("<tr class=\"$bgcolor\">");

					$checked = ( $data[2] == "O" ) ? "checked=\"checked\"" : "" ;

					$update  = "<a href=\"$mylink&amp;tablidx=7&amp;newforfait=$data[0]\">";
					$update .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"". $msg->read($CONFIG_MODIFY) ."\" alt=\"". $msg->read($CONFIG_MODIFY) ."\" />";
					$update .= "</a>";

					print("
						<td class=\"align-center\">

						</td>
						<td class=\"align-center\">
							<label for=\"delforfait_$data[0]\"><input type=\"checkbox\" id=\"delforfait_$data[0]\" name=\"delforfait[]\" value=\"$data[0]\" /></label>
						</td>
						<td>
							$data[0]. $data[1]
							$update
						</td>
						");

					if ( $i % 2 == 0 )
						print("</tr>");

					$data = remove_magic_quotes(mysqli_fetch_row($result));
					}

				if ( $i % 2 )
					print("
						  <td></td>
						  <td></td>
						  <td></td>
						</tr>
						");

				if ( $newforfait ) {
					$value = ( $newforfait > 0 ) ? $msg->read($CONFIG_MODIFY) : $msg->read($CONFIG_CREAT) ;

					// affichage des groupes
					$result = mysqli_query($mysql_link, "select _Nom from forfait where _IDforfait = '$newforfait' limit 1");
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					print("
						  <tr>
							<td colspan=\"6\" style=\"border: #cccccc solid 1px;\">
							<input type=\"text\" id=\"identforfait\" name=\"identforfait\" size=\"30\" value=\"$row[0]\" />
							<input type=\"submit\" value=\"$value\" name=\"submit\" class=\"btn btn-success\" />
							<p class=\"hidden\"><input type=\"hidden\" name=\"tablidx\" value=\"7\" /></p>
							<p class=\"hidden\"><input type=\"hidden\" name=\"idrec\"   value=\"$newforfait\" />
							<input type=\"hidden\" name=\"pane\" value=\"paneforfait\"></p>");


					print("
							</td>
						  </tr>
						");
					}
				else
					print("
						  <tr>
							<td colspan=\"6\" style=\"border: #cccccc solid 1px;\">
							<img src=\"".$_SESSION["ROOTDIR"]."/images/ajouter.gif\" title=\"". $msg->read($CONFIG_ADDRECORD) ."\" alt=\"". $msg->read($CONFIG_ADDRECORD) ."\" />
							<a href=\"$mylink&amp;tablidx=7&amp;newforfait=-1\">". $msg->read($CONFIG_ADDRECORD) ."</a>.
							</td>
						  </tr>
						");
			 ?>

			</table>
		</fieldset>
		</div>

		<!-- FIN Div Forfait -->
		<?php
		}
	?>

	</div>

	</td></tr></table>

	<hr/>

		        <table class="width100">
          <tr>
            <td style="width:10%;" class="valign-middle">
			<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CONFIG_INPUTOK)."\" />"); ?>
		</td>
            <td class="valign-middle">
			<?php print($msg->read($CONFIG_VALIDATE)); ?>
		</td>
          </tr>

          <tr>
            <td class="valign-middle">
			<a href="index.php"><?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($CONFIG_INPUTCANCEL)."\" />"); ?></a>
		</td>
            <td class="valign-middle">
			<?php print($msg->read($CONFIG_GOHOME)); ?>
		</td>
          </tr>
        </table>

	</div>




    </form>
</div>

<script>
jQuery(document).ready(function() {
    jQuery("input[id^='delg_']").each(function( index ) {
		jQuery(this).change(function() {
			if(this.checked) {
				alert("Attention : en cochant cette case vous allez effectuer une suppression !");
			}
		});
	});

    jQuery("input[id^='delk_']").each(function( index ) {
		jQuery(this).change(function() {
			if(this.checked) {
				alert("Attention : en cochant cette case vous allez effectuer une suppression !");
			}
		});
	});

    jQuery("input[id^='delm_']").each(function( index ) {
		jQuery(this).change(function() {
			if(this.checked) {
				alert("Attention : en cochant cette case vous allez effectuer une suppression !");
			}
		});
	});

    jQuery("input[id^='delmo_']").each(function( index ) {
		jQuery(this).change(function() {
			if(this.checked) {
				alert("Attention : en cochant cette case vous allez effectuer une suppression !");
			}
		});
	});

    jQuery("input[id^='delsa_']").each(function( index ) {
		jQuery(this).change(function() {
			if(this.checked) {
				alert("Attention : en cochant cette case vous allez effectuer une suppression !");
			}
		});
	});
});
</script>
