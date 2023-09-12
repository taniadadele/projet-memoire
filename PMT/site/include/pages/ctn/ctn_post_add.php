<?php
// error_reporting(E_ALL);
//    ini_set("display_errors", 1);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR" );

/*-----------------------------------------------------------------------*
   Copyright (c) 2005-2009 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : ctn_post.php
 *		projet   : la page de saisie du cahier de texte numérique
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 22/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

session_start();
$IDgroup  = ( @$_POST["IDgroup"] )			// ID du e-groupe
	? (int) $_POST["IDgroup"]
	: (int) @$_GET["IDgroup"] ;
$IDclass  = ( @$_POST["IDclass"] )			// sélection de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDitem   = ( @$_POST["IDitem"] )			// ID de l'item du ctn
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$copy     = ( @$_POST["copy"] )			// ID de la classe à dupliquer
	? (int) $_POST["copy"]
	: (int) @$_GET["copy"] ;
$type     = ( @$_POST["type"] )
	? (int) $_POST["type"]
	: (int) @$_GET["type"] ;
$sd     = ( @$_POST["sd"] )
	? $_POST["sd"]
	: @$_GET["sd"] ;
$st     = ( @$_POST["st"] )
	? $_POST["st"]
	: @$_GET["st"] ;
$et     = ( @$_POST["et"] )
	? $_POST["et"]
	: @$_GET["et"] ;
$IDx     = ( @$_POST["IDx"] )
	? (int) $_POST["IDx"]
	: (int) @$_GET["IDx"] ;
$nextID     = ( @$_POST["nextID"] )
	? (int) $_POST["nextID"]
	: (int) @$_GET["nextID"] ;


require_once "page_session.php";
require "msg/ctn.php";
require_once "include/TMessage.php";
require_once "include/calendar_tools.php";

require_once "include/fonction/auth_tools.php";

$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/ctn.php");
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;
require "page_banner.php";
include_once("php/functions.php");

$tmpmdate = $sd;
$m = substr($sd, 0, strpos($sd, "/"));
$d = substr($sd, strpos($sd, "/")+1);
$d = substr($d, 0, strpos($d, "/"));
$y = substr($sd, strpos($sd, "/")+1);
$y = substr($y, strpos($y, "/")+1);
$y = str_replace("/", "", $y);
$tmpmdate = "$y-$m-$d";
$mdate    = ( @$_POST["mdate"] ) ? $_POST["mdate"] : $tmpmdate ;
if (!isset($submit) || !$submit)
{
	// Item correspondant
	$sql2 = "select * from ctn_items where _type = 0 and _IDcours = ".$IDx." LIMIT 1";
	$handle2 = mysqli_query($mysql_link, $sql2);
	while ($row2 = mysqli_fetch_object($handle2))
	{
		$IDitem = $row2->_IDitem;
	}
}


// Recherche cours
$sql  = "select * ";
$sql .= "from edt_data ";
$sql .= "where _IDx = '$IDx'";
$handle3 = mysqli_query($mysql_link, $sql);
$row3    = ( $handle3 ) ? remove_magic_quotes(mysqli_fetch_row($handle3)) : 0 ;
$IDmat = $row3[3];
$IDclass = $row3[4];
$IDgrp = (int)$row3[8];
$array_class = explode(";", $IDclass);
$ck_groupe = $row3[17];
$plus = $row3[20];

$IDpj     = (int) @$_GET["IDpj"];
$IDabsent = (int) @$_GET["IDabsent"];

$mtime    = @$_POST["mtime"];
$delay    = @$_POST["delay"];
$title    = addslashes(trim(@$_POST["title"]));
$texte_ckeditor    = addslashes(trim(@$_POST["texte_ckeditor"]));
$devoirs    = addslashes(trim(@$_POST["devoirs"]));
$observ    = addslashes(trim(@$_POST["observ"]));
$note     = addslashes(trim(@$_POST["note"]));
$edit     = ( strlen(@$_POST["edit"]) )		// mode d'édition : basique ou avancé
	? (int) $_POST["edit"]
	: (int) (strlen(@$_GET["edit"]) ? $_GET["edit"] : $WYSIWYG) ;
$sndmail  = ( @$_POST["sndmail"] ) ? "O" : "N" ;

$submit   = ( @$_POST["submitx"] )			// bouton de validation
	? $_POST["submitx"]
	: @$_GET["submitx"] ;

//---------------------------------------------------------------------------
function isValidEmail($address)
{
/*
 * fonction :	test si une adresse email est valide ou non
 * in :		$address : adresse email
 * out :		true si valide, false sinon
 */

	if ( mb_ereg(".*<(.+)>", $address, $regs) )
		$address = $regs[1];

 	return ( mb_ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$", $address) )
 		? true
	 	: false ;
}

function nettoyerChaine($chaine)
{
	$caracteres = array(
		'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
		'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '' => 'e',
		'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
		'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
		'' => 'oe', '' => 'oe',
		'$' => 's');

	$chaine = strtr($chaine, $caracteres);
	$chaine = preg_replace('#[^A-Za-z0-9]+#', '-', $chaine);
	$chaine = trim($chaine, '-');
	$chaine = strtolower($chaine);

	return $chaine;
}
//---------------------------------------------------------------------------
$affiche = false;

if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
{
	$affiche = true;
}
else if($_SESSION["CnxID"] == $row3[6] || $_SESSION["CnxID"] == $row3[18])
{
	$affiche = true;
}
else
{
	$sqla  = "select * ";
	$sqla .= "from edt_data ";
	$sqla .= "where _IDclass = '$row3[4]' ";
	$sqla .= "and _debut = '$row3[10]' ";
	$sqla .= "and _fin = '$row3[11]' ";
	$sqla .= "and _nosemaine = $row3[13] ";
	$sqla .= "and _annee = $row3[16] ";
	$sqla .= "and _IDmat = $row3[3] ";
	$sqla .= "and _ID = ".$_SESSION["CnxID"]." ";
	$handla = mysqli_query($mysql_link, $sqla);
	$num_rows = mysqli_num_rows($handla);

	if($num_rows > 0)
	{
		$affiche = true;
	}
}

if(!$affiche)
{
	echo "<center><span style=\"background-color: #b94a48; border-radius: 3px;color: #ffffff; display: inline-block; font-size: 14px; font-weight: bold; line-height: 14px; padding: 2px 4px; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); text-align: center; margin-top: 50px;\">".$msg->read($CTN_NONAUT)."</span></center>";
	exit(1);
}
?>


<!-- <script src="script/ckeditor/ckeditor.js"></script> -->
<script>
function CocheTout(ref, name) {
	var form = ref;

	while (form.parentNode && form.nodeName.toLowerCase() != 'form'){
		form = form.parentNode;
	}

	var elements = form.getElementsByTagName('input');

	for (var i = 0; i < elements.length; i++) {
		if (elements[i].type == 'checkbox' && elements[i].name == name) {
			elements[i].checked = ref.checked;
		}
	}
}
</script>

<style>
.btnnext {
    clear: both;
    color: #333333;
    display: block;
    font-weight: normal;
    line-height: 20px;
    padding: 3px 20px;
    white-space: nowrap;
	background-color: white;
	border: none;
}
.btnnext:hover {
	background-color: #0081c2;
	color: white;
}

body {
	padding-top: 0px;
}
</style>

<!-- <div class="btn-primary">
	<div style="text-align: center;">
	<?php
		// lecture de la classe
		$query  = "select distinctrow ";
		$query .= "campus_classe._ident, ctn._currdate, ctn._IDmod, ctn._IDgrpwr, ctn._PJ, ctn._sndmail ";
		$query .= "from campus_classe, ctn ";
		$query .= "where campus_classe._IDclass = '$IDclass' ";
		$query .= "AND campus_classe._IDclass = ctn._IDclass ";
		$query .= "limit 1";

		$return = mysqli_query($mysql_link, $query);
		$auth   = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

		// vérification des autorisations
		//verifySessionAccess($auth[2], $auth[3]);

		print($msg->read($CTN_MYCTN, $auth[0]) ."<br/>". $msg->read($CTN_FORMFEED));
	?>
	</div>
</div> -->

<div class="maincontent" style="margin-top: 0px;">

<?php
	require_once "include/agenda.php";

	// on revient sur le cahier de texte numérique
	$retour  = myurlencode("index.php?item=".(@$item)."&IDcentre=".(@$IDcentre)."&IDmat=$IDmat&IDgroup=$IDgroup&IDclass=$IDclass&salon=") . $_SESSION["CampusName"];

	$error_title = $error_date = 0;

	// lecture de la matière
	$return     = mysqli_query($mysql_link, "select _titre, _option, _IDmat from campus_data where _IDmat = '$IDmat' AND _lang = '".$_SESSION["lang"]."' limit 1");
	$mat        = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

	// commande de l'utilisateur
	switch ( $submit ) {
		case "Valider" :	// l'utilisateur a posté son message
			// test de la saisie
			$error_title = ( strlen(trim($title)) ) ? 0 : 1 ;

			//----- copie par mél -----//
			// lecture de la classe
			$return      = mysqli_query($mysql_link, "select _ident from campus_classe where _IDclass = '$IDclass' limit 1");
			$classe      = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			$copymail    = $msg->read($CTN_GETCLASS)  ." ". $classe[0] ."\n";
	            $copymail   .= $msg->read($CTN_GETMATTER) ." ". $mat[0] ."\n";
	            $copymail   .= $msg->read($CTN_AUTHOR)    ." ". getUserNameByID($_SESSION["CnxID"]) ." ". _getHostName($_SESSION["CnxIP"] ) ."\n";
			$copymail   .= date2longfmt("$mdate $mtime") ." (". str_replace(":", $msg->read($CTN_HOUR), $delay) .")\n";
			$copymail   .= $title ."\n". $texte_ckeditor;

			if ( !$error_title AND !$error_date ) {
				$list    = explode(",", $msg->read($CTN_TODO));
				$raw     = ( $edit ) ? "N" : "O" ;				// mode SPIP ou WYSIWYG
				$date    = ( $auth[1] == "O" ) ? "$mdate " . date("H:i:s") : "$mdate $mtime" ;
				$idclass = ( $copy ) ? $copy : $IDclass ;

				if ( $IDitem AND $copy == 0 ) {
					$Query  = "update ctn_items ";
					$Query .= "set _IDmat = '$IDmat', _title = '$title', _texte = '$texte_ckeditor', _devoirs = '$devoirs', _date = '$mdate $mtime', _delay = '$delay', _note = '$note', _type = '$type', _observ = '$observ' ";
					$Query .= "where _IDitem = '$IDitem' ";
					$Query .= "limit 1";
					}
				else {
					$return = mysqli_query($mysql_link, "select _IDctn from ctn where _IDgroup = '$IDgroup' AND _IDclass = '$idclass' limit 1");
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

					$Query  = "insert into ctn_items ";
					$Query .= "values(NULL, '$IDclass', '$IDmat', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$delay', '$title', '$texte_ckeditor', '$raw', '$note', 'O', '$type', '$IDx', '".date("W", strtotime($mdate))."', '$devoirs', '$observ')";
					}

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				else {
					$parent = ( $IDitem AND $copy == 0 ) ? $IDitem : mysqli_insert_id($mysql_link) ;

					//---- copie des PJ
					if ( $copy ) {
						$nbdev = $nbctrl = 0;

						$res   = mysqli_query($mysql_link, "select _title, _ext, _size, _type, _IDpj from ctn_pj where _IDitem = '$IDitem'");
						$mypj  = ( $res ) ? mysqli_fetch_row($res) : 0 ;

						while ( $mypj ) {
							// on compte les devoirs et les controles
							if ( $mypj[3] == 1 )
								$nbdev++;
							else
								if ( $mypj[3] == 2 )
									$nbctrl++;

							if ( mysqli_query($mysql_link, "insert into ctn_pj values('', '$parent', '$mypj[0]', '$mypj[1]', '$mypj[2]', '$mypj[3]')") )
								if ( copy("$DOWNLOAD/ctn/$mypj[4].$mypj[1]", "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$mypj[1]") )
									mychmod("$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$mypj[1]", $CHMOD);

							$mypj  = mysqli_fetch_row($res);
							}

						if ( $nbdev )
							mysqli_query($mysql_link, "insert into ctn_data values('', '$parent', '1', '$date', '')");

						if ( $nbctrl )
							mysqli_query($mysql_link, "insert into ctn_data values('', '$parent', '2', '$date', '')");
						}

					// durée de l'absence
					list($h, $m) = sscanf($delay, "%d%*s%d");
					$start  = $date;
					$end    = $date;

					// autorisation
					// $query  = "select _diary from ctn ";
					// $query .= "where _IDclass = '$IDclass' ";
					// $query .= "limit 1";

					$result = mysqli_query($mysql_link, $query);
					$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

					//---- enregistrement des retards
					$IDeleve = @$_POST["IDeleve"];
					if (isset($IDeleve) && $IDeleve != '') {
						for ($i = 0; $i < count($IDeleve); $i++)
							if ( @$IDeleve[$i] ) {
								$index   = $IDeleve[$i];
								$IDmotif = @$_POST["IDmotif_$index"];
								$textautre = @$_POST["textautre_$index"];

								// Recherche si absence prévisionnelle pour l'élève
								$query  = "select distinctrow ";
								$query .= "user_id._name, user_id._fname, user_id._ID, user_id._IDclass, user_id._email, user_id._tel, ";
								$query .= "absent_items._email, absent_items._sms, absent_items._ID, absent_items._IP, absent_items._IDdata, absent_items._start, absent_items._IDctn, absent_items._texte, absent_items._IDitem, absent_items._IDmod, absent_items._isok, ";
								$query .= "campus_classe._ident, absent_items._end, absent_items._date, absent_items._valid ";
								$query .= "from user_id, absent_items, campus_classe ";
								$query .= "where (absent_items._start <= '$mdate $st' AND absent_items._end >= '$mdate $et') ";
								$query .= "AND user_id._ID = absent_items._IDabs ";
								$query .= "AND absent_items._IDctn ";
								$query .= "AND user_id._IDclass = campus_classe._IDclass ";
								$query .= "AND user_id._ID = '$index' ";
                $query .= "AND user_id._adm >= 1 ";

								$resultx = mysqli_query($mysql_link, $query);
								$rowx    = ( $resultx ) ? remove_magic_quotes(mysqli_fetch_row($resultx)) : 0 ;
								// détermination du nombre d'absences
								$nbabs   = ( $resultx ) ? mysqli_affected_rows($mysql_link) : 0 ;
								$motifabs = ( $nbabs ) ? $rowx[10] : 0;
								$textabs = ( $nbabs ) ? $rowx[13] : "";
								$isokabs = ( $nbabs ) ? $rowx[20] : "N";

								date_default_timezone_set('Europe/Paris');
								setlocale(LC_TIME, "fr_FR" );
								$good_date = date("Y-m-d H:i:s", time());

								if($nbabs > 0)
								{
									$Query   = "insert into absent_items ";
									$Query  .= "values('', '$motifabs', '$parent', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$good_date', '1', '$index', '$mdate $st', '$mdate $et', '".addslashes($textautre)."', '', 'O', 'N', '0', '', '', '0', '', 'O', '$isokabs', '')";
								}
								else
								{
									$Query   = "insert into absent_items ";
									$Query  .= "values('', '$IDmotif', '$parent', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$good_date', '1', '$index', '$mdate $st', '$mdate $et', '".addslashes($textautre)."', '', 'O', 'N', '0', '', '', '0', '', 'O', 'N', '')";
								}
								mysqli_query($mysql_link, $Query);
							}
					}



					//---- transfert d'une Pièce Jointe (item)
					$file = @$_FILES["UploadPJ"]["tmp_name"];

					for ($i = 0; $i < count($file) && @$file[$i]; $i++)
						if ( true ) {
							// fichier destination
							$ext    = extension(@$_FILES["UploadPJ"]["name"][$i]);
							$size   = (int) @$_FILES["UploadPJ"]["size"][$i];

							$PJdesc = @$_POST["PJdesc"];

							$Query  = "insert into ctn_pj ";
							$Query .= "values('', '$parent', '".addslashes(nettoyerChaine($PJdesc[$i]))."', '$ext', '$size', '0', '".nettoyerChaine($_FILES["UploadPJ"]["name"][$i])."')";

							if ( mysqli_query($mysql_link, $Query) )
								// copie du fichier temporaire -> répertoire de stockage
								if ( move_uploaded_file($file[$i], "$DOWNLOAD/ctn/".mysqli_insert_id($mysql_link)."_".nettoyerChaine(trim($_FILES["UploadPJ"]["name"][$i]))) )
									mychmod("$DOWNLOAD/ctn/".mysqli_insert_id($mysql_link)."_".nettoyerChaine(trim($_FILES["UploadPJ"]["name"][$i]))."_".mysqli_insert_id($mysql_link).".$ext", $CHMOD);
							}

					//---- transfert d'une Pièce Jointe des devoirs
					$file  = @$_FILES["UploadFile1"]["tmp_name"];
					$files = Array();

					if ( @$file[0] ) {
						for ($i = 0; $i < count($file) && @$file[$i]; $i++)
							if ( authfile(@$_FILES["UploadFile1"]["name"][$i]) ) {
								// fichier destination
								$ext    = extension(@$_FILES["UploadFile1"]["name"][$i]);
								$size   = (int) @$_FILES["UploadFile1"]["size"][$i];

								$Query  = "insert into ctn_pj ";
								$Query .= "values('', '$parent', '', '$ext', '$size', '1')";

								if ( mysqli_query($mysql_link, $Query) )
									// copie du fichier temporaire -> répertoire de stockage
									if ( move_uploaded_file($file[$i], "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext") ) {
										mychmod("$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext", $CHMOD);
										array_push($files, "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext");
										}
								}
						}

					// les commentaires associés
					$text   = addslashes(trim(@$_POST["text1"]));
					$todo   = ( @$_POST["todo"] )
						? $_POST["todo"]
						: (strlen($text) ? date("Y-m-d") : "") ;
					$todoh  = ( @$_POST["todoh"] ) ? $_POST["todoh"] : date("H:i:s") ;

					$Query  = "update ctn_data ";
					$Query .= "set _todo = '$todo $todoh', _text = '$text' ";
					$Query .= "where _IDitem = '$parent' AND _type = '1' ";
					$Query .= "limit 1";

					mysqli_query($mysql_link, $Query);

					// il n'y a peut être pas de PJ
					if ( !mysqli_affected_rows($mysql_link) AND strlen($todo) )
						mysqli_query($mysql_link, "insert into ctn_data values('', '$parent', '1', '$todo $todoh', '$text')");

					// copie dans les agendas
					if ( $auth[0] == "O" ) {
						$ag   = new diary();
						$text = trim(@$_POST["text1"]);

						for ($i = 0; $i < count($files); $i++)
							$text .= "<br/>[[->$files[$i]]]";

						// lecture des élèves
						$return = mysqli_query($mysql_link, "select _ID from user_id where _adm AND _IDclass = '$IDclass' AND _visible = 'O' AND _IDgrp > 1");
						$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

						while ( $myrow ) {
							// si l'agenda perso n'existe pas, on le crée
							if ( !$ag->exist($_SESSION["CnxID"], $_SESSION["CampusName"]) )
								if ( $ag->createUserDiary($row[0]) )
									$ag->perms($row[0]);

							$ag->write($list[0], "$todo $todoh", "$todo $todoh", $text);

							$myrow  = mysqli_fetch_row($return);
							}
						}

					unset($files);

					//---- transfert d'une Pièce Jointe des controles
					$file  = @$_FILES["UploadFile2"]["tmp_name"];
					$files = Array();

					if ( @$file[0] ) {
						for ($i = 0; $i < count($file) && @$file[$i]; $i++)
							if ( authfile(@$_FILES["UploadFile2"]["name"][$i]) ) {
								// fichier destination
								$ext    = extension(@$_FILES["UploadFile2"]["name"][$i]);
								$size   = (int) @$_FILES["UploadFile2"]["size"][$i];

								$Query  = "insert into ctn_pj ";
								$Query .= "values('', '$parent', '', '$ext', '$size', '2')";

								if ( mysqli_query($mysql_link, $Query) )
									// copie du fichier temporaire -> répertoire de stockage
									if ( move_uploaded_file($file[$i], "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext") ) {
										mychmod("$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext", $CHMOD);
										array_push($files, "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext");
										}
								}
						}

					// les commentaires associés
					$text   = addslashes(trim(@$_POST["text2"]));
					$todo   = ( @$_POST["ctrl"] )
						? $_POST["ctrl"]
						: (strlen($text) ? date("Y-m-d") : "") ;
					$todoh  = ( @$_POST["ctrlh"] ) ? $_POST["ctrlh"] : date("H:i:s") ;

					$Query  = "update ctn_data ";
					$Query .= "set _todo = '$todo $todoh', _text = '$text' ";
					$Query .= "where _IDitem = '$parent' AND _type = '2' ";
					$Query .= "limit 1";

					mysqli_query($mysql_link, $Query);

					// il n'y a peut être pas de PJ
					if ( !mysqli_affected_rows($mysql_link) AND strlen($todo) )
						mysqli_query($mysql_link, "insert into ctn_data values('', '$parent', '2', '$todo $todoh', '$text')");

					// copie dans les agendas
					if ( $auth[0] == "O" ) {
						$ag   = new diary();
						$text = trim(@$_POST["text2"]);

						for ($i = 0; $i < count($files); $i++)
							$text .= "<br/>[[->$files[$i]]]";

						// lecture des élèves
						$return = mysqli_query($mysql_link, "select _ID from user_id where _adm AND _IDclass = '$IDclass' AND _visible = 'O' AND _adm >= 1 ");
						$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

						while ( $myrow ) {
							// si l'agenda perso n'existe pas, on le crée
							if ( !$ag->exist($_SESSION["CnxID"], $_SESSION["CampusName"]) )
								if ( $ag->createUserDiary($row[0]) )
									$ag->perms($row[0]);

							$ag->write($list[1], "$todo $todoh", "$todo $todoh", $text, "H");

							$myrow  = mysqli_fetch_row($return);
							}
						}

					// copie par mél
					if ( $sndmail == "O" ) {
						require_once "lib/libmail.php";

						$Query  = "select _email from user_id where _ID = '".$_SESSION["CnxID"]."' AND _adm >= 1 ";

						$result = mysqli_query($mysql_link, $Query);
						$myrow  = ( $result ) ? mysqli_fetch_row($result) : 0 ;

						$mymail = new Mail(); 		// create the mail

						if ( isValidEmail(trim($myrow[0])) ) {
							// entête du corps du message
							$body  = stripslashes($copymail);

							// pied de page du corps du message
							$body .= "\n--\n";
							$body .= $_SESSION["CfgAdr"] . "\n";
							$body .= $_SESSION["CfgWeb"];

							$mymail->From("noreply@".$_SESSION["CfgWeb"]);
							$mymail->To(trim($myrow[0]));
							$mymail->Subject(stripslashes($subject));
							$mymail->Body($body, $CHARSET);

							$mymail->Send();		// send the mail
							}
						}

						if($nextID != 0)
						{
							print("
								<script type=\"text/javascript\">
								window.location = 'ctn_post_add.php?IDx=$nextID&sd=$sd&st=$st&et=$et&generique=off';
								</script>");
						}
						else
						{
							print("
								<script type=\"text/javascript\">
								self.close();
								</script>");
						}
					}
				}
			$closeModal = true;
			break;

		case "del" :
		case "delpj" :
			$query  = "delete from " . (( $submit == "del" ) ? "absent_items " : "ctn_pj ") ;
			$query .= ( $submit == "del" ) ? "where _IDitem = '$IDabsent' " : "where _IDpj = '$IDpj' " ;
			$query .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 ) ? "" : "AND _ID = '".$_SESSION["CnxID"]."' " ;
			$query .= "limit 1";

			if ( mysqli_query($mysql_link, $query) AND $submit == "delpj" )
				@unlink("$DOWNLOAD/ctn/$IDpj_*");
			$closeModal = true;
			// on enchaine...

		default :
			// initialisation des champs de saisie
			$query   = "select _date, _title, _texte, _delay, _raw, _note, _type, _devoirs, _observ from ctn_items ";
			$query  .= "where _IDitem = '$IDitem' ";
			$query  .= "limit 1";

			$result  = mysqli_query($mysql_link, $query);
			$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			//list($mdate, $mtime) = ( $row )
				//? explode(" ", $row[0])
				//: explode(" ", date("Y-m-d H:i:s")) ;
			$title   = ( $row ) ? $row[1] : $msg->read($CTN_CHAPTER) ;
			$texte_ckeditor   = ( $row ) ? $row[2] : "" ;
			$devoirs   = $row[7];
			$observ   = $row[8];
			$delay   = $row[3];
			$raw     = $row[4];
			$PJdesc  = "";
			$note    = $row[5];
			$type    = $row[6];
			break;
		}

		// On ferme le modal dans lequel cette iframe apparait
		if (isset($closeModal) && $closeModal) {
			echo '<script>
				$(document).ready(function(){
					var modal_id = $("body", window.parent.document).attr("modal_popup_number");
					$("#closePopupModal_" + modal_id, window.parent.document).click();
				});
			</script>';
		}

	// saisie du formulaire si
	// - l'utilisateur n'a pas validé OU
	// - il y a une erreur de saisie => on redemande de compléter le formulaire
	if ( $submit != "Valider" OR $error_title OR $error_date ) {

		// les matières enseignées
		$query  = "select _IDmat from user_id where _ID = '".$_SESSION["CnxID"]."' AND _adm >= 1 limit 1 ";

		$return = mysqli_query($mysql_link, $query);
		$perm   = ( $return ) ? mysqli_fetch_row($return ) : 0 ;

		print("
		<form id=\"formulaire\" name=\"formulaire\" action=\"ctn_post_add.php\" method=\"post\" enctype=\"multipart/form-data\">
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"".(@$item)."\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDcentre\" value=\"".(@$IDcentre)."\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDmat\"    value=\"$IDmat\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDx\"    value=\"$IDx\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"sd\"    value=\"$sd\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"st\"    value=\"$st\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"et\"    value=\"$et\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDgroup\"  value=\"$IDgroup\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDclass\"  value=\"$IDclass\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDitem\"   value=\"$IDitem\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"type\"     value=\"0\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"edit\"     value=\"$edit\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"post\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"submitx\"   value=\"Valider\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"nextID\" id=\"nextID\"   value=\"0\" /></p>

		<a style=\"float: right\" href=\"ctn_post_add_print.php?IDx=$IDx&sd=$sd&st=$st&et=$et\"><i class=\"fa fa-print\" style=\"color: black\"></i></a>

		<table class=\"width100\">
		  <tr style=\"border-bottom: 10px solid transparent;\">
		    <td style=\"width:19%;\" class=\"valign-middle align-right\">". $msg->read($CTN_GETMATTER) ."</td>
		    <td style=\"width:41%;\" class=\"valign-middle\">".$mat[0]."</td>
		    <td class=\"align-right\">
		    </td>
		  </tr>");

		// saisie de la classe pour duplication
		if ( $copy ) {
			print("
			    <tr>
			      <td class=\"valign-middle align-right\">". $msg->read($CTN_GETCLASS) ."</td>
			      <td class=\"valign-middle\" colspan=\"2\">
				  <label for=\"copy\">
					<select id=\"copy\" name=\"copy\">");

				// affichage des classes
				$query  = "select distinctrow ctn._IDclass, campus_classe._ident from ctn, campus_classe ";
				$query .= "where campus_classe._visible = 'O' ";
				$query .= "AND campus_classe._IDcentre = '$IDcentre' ";
				$query .= "AND campus_classe._IDclass = ctn._IDclass ";
				$query .= "AND ctn._IDgroup = '$IDgroup' ";

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					printf("<option value=\"$row[0]\" %s>$row[1]</option>", $copy == $row[0] ? "selected=\"selected\"" : "");

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}

			print("
					</select>
				  </label>
			      </td>
			    </tr>");
			}

		print("
		    <tr style=\"border-bottom: 10px solid transparent;\">
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_MYDATE) ."
		      </td>
		      <td colspan=\"2\">
		        <p style=\"margin:0px;\">");

		echo date('d/m/Y', strtotime($mdate))." de $st &agrave; $et";
		echo "<input type=\"hidden\" id=\"title\" name=\"title\" size=\"40\" value=\"$title\" />";
		print("
		        </p>
	      	</td>
		    </tr>
		    </tr>
		    	");

		// une fois un texte saisi dans un certain mode, il n'est plus possible d'en changer
		if ( $raw ) {
			$edit    = ( $raw == "O" ) ? 0 : 1 ;
			$editor  = ( $raw == "O" ) ? $msg->read($CTN_BASIC) : $msg->read($CTN_ADVANCED) ;
			}
		else {
			$toggle  = (int) !$edit;
			$editor  = "<a href=\"index.php?item=".(@$item)."&amp;cmde=".(@$cmde)."&amp;IDmat=$IDmat&amp;IDclass=$IDclass&amp;edit=$toggle\">-> ";
			$editor .= ( $edit ) ? $msg->read($CTN_BASIC) : $msg->read($CTN_ADVANCED) ;
			$editor .= "</a>";
			}

		print("
		    <tr style=\"border-bottom: 10px solid transparent;\">
		      <td class=\"align-right valign-top\">
		        <strong>".$msg->read($CTN_CONTENU)." : </strong>
		      </td>
		      <td colspan=\"2\">
		    	");

		if ( $edit ) {
			?>
				<textarea name="texte_ckeditor" id="texte_ckeditor"><?php echo $texte_ckeditor; ?></textarea>
				<script>
				$(document).ready(function(){
					CKEDITOR.replace('texte_ckeditor', {height: '150px'});
				})
				</script>
			<?php
			}
		else
			print("<label for=\"texte_ckeditor\"><textarea id=\"texte_ckeditor\" name=\"texte_ckeditor\" rows=\"10\" cols=\"40\">$texte_ckeditor</textarea></label>");

		print("
	      	</td>
		    </tr>
		    	");

				print("
		    <tr style=\"border-bottom: 10px solid transparent;\">
		      <td class=\"align-right valign-top\">
		        <strong>".$msg->read($CTN_DEVOIRS)." : </strong>
		      </td>
		      <td colspan=\"2\" style=\"margin-bottom: 100px;\">
		    	");

		if ( $edit ) {
			?>
				<textarea name="devoirs" id="devoirs"><?php echo $devoirs; ?></textarea>
				<script>
				$(document).ready(function(){
					CKEDITOR.replace('devoirs', {height: '150px'});
				})

				</script>
			<?php
			}
		else
			print("<label for=\"devoirs\"><textarea id=\"devoirs\" name=\"devoirs\" rows=\"10\" cols=\"40\">$devoirs</textarea></label>");

		print("
	      	</td>
		    </tr>
		    	");

				print("
		    <tr style=\"display: none;\">
		      <td class=\"align-right valign-top\">
		        <strong>".$msg->read($CTN_OBSERV)." : </strong>
			  	<span style=\"cursor: pointer;\" onclick=\"$('obs')._display.toggle(); return false;\"><i class=\"fa fa-sort\"></i></span>
		      </td>
		      <td colspan=\"2\">
				...
				<div id=\"obs\" style=\"display:none;\">
		    	");

		if ( $edit ) {
			?>
				<textarea name="observ" id="observ"><?php echo $observ; ?></textarea>
				<script>
					$(document).ready(function(){
						CKEDITOR.replace('observ', {height: '150px'});
					})
				</script>
			<?php
			}
		else
			print("<label for=\"observ\"><textarea id=\"observ\" name=\"observ\" rows=\"10\" cols=\"40\">$observ</textarea></label>");

		print("
				</div>
	      	</td>
		    </tr>
		    	");

		// affichage des smileys d'édition
		if ( !$edit ) {
		    	print("
			    <tr>
			      <td class=\"valign-middle align-right\"><strong>Smileys :</strong></td>
			      <td colspan=\"2\">
			    	");

			$res   = mysqli_query($mysql_link, "select _code, _ident from smileys where _type = 'T'");
			$smile = ( $res ) ? mysqli_fetch_row($res) : 0 ;

			while ( $smile ) {
				print("
					<img src=\"".$_SESSION["ROOTDIR"]."/images/smiley/forum/$smile[1].gif\" title=\" code: $smile[0] \" alt=\" code: $smile[0] \"
					onclick=\"Javacript:ajoutsmile('$smile[0]')\" style=\"cursor: hand;\" />
					");

				$smile = ( $res ) ? mysqli_fetch_row($res) : 0 ;
				}

		    	print("
			      </td>
			    </tr>
			    	");
			}

		// ajout Pièce Jointe
		if ( true ) {
		    	print("
			    <tr style=\"display: none;\">
			      <td class=\"align-right valign-top\">
					". $msg->read($CTN_ATTACHED) ."
					<span style=\"cursor: pointer;\" onclick=\"$('PJ')._display.toggle(); return false;\"><i class=\"fa fa-sort\"></i></span>
			      </td>
			      <td colspan=\"2\">");

			// lecture des PJ
			$res = mysqli_query($mysql_link, "select _IDpj, _title, _ext, _size, _name from ctn_pj where _IDitem = '$IDitem' AND _type = '0'");
			$doc = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

			if ( $doc )
				while ( $doc ) {
					// suppression la pièce jointe
					$req   = $msg->read($CTN_DELATTACH);
					$del   = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $row[4] == $_SESSION["CnxID"] )
						? "<a href=\"".myurlencode("ctn_post_add.php?IDx=$IDx&item=$item&cmde=$cmde&IDitem=$IDitem&IDpj=$doc[0]&IDmat=$IDmat&IDclass=$IDclass&submitx=delpj")."\" onclick=\"return confirmLink(this, '$req');\">
						   <i class=\"fa fa-trash\" title=\"$req\"></i>
						   </a>"
						: "" ;

					print("
				    		<a target=\"_blank\" href=\"index.php?file=download/ctn/$doc[0]_$doc[4]\"><img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$doc[2].gif\" title=\"\" alt=\"\" /> $doc[1]</a> $del<br/>
						");

					$texte_ckeditor = "";
					$doc   = remove_magic_quotes(mysqli_fetch_row($res));
					}
			else
				print("...");

			print("
				<div id=\"PJ\" style=\"display:none; border:#cccccc solid 1px; padding:5px;\">
					<p class=\"hidden\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" /></p>
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadPJ[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>

					<label for=\"PJdesc_0\"><input type=\"text\" id=\"PJdesc_0\" name=\"PJdesc[]\" size=\"50\" /></label>
					". $msg->read($CTN_DESCRIPTION) ."

					<div id=\"uploadpj\" style=\"display:none;\">
					<p style=\"margin-top:5px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadPJ[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>

					<label for=\"PJdesc_1\"><input type=\"text\" id=\"PJdesc_1\" name=\"PJdesc[]\" size=\"50\" /></label>
					". $msg->read($CTN_DESCRIPTION) ."

					<p style=\"margin-top:5px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadPJ[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>

					<label for=\"PJdesc_2\"><input type=\"text\" id=\"PJdesc_2\" name=\"PJdesc[]\" size=\"50\" /></label>
					". $msg->read($CTN_DESCRIPTION) ."
					</div>

				</div>
			</td>
		    </tr>
		    ");
			}

		// gestion des absences
	    	print("
			<tr>
		      <td colspan=\"3\">
		    	");

		// lecture des motifs
		$query  = "select _IDdata, _texte from absent_data ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
		$query .= "order by _IDdata";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		$i = 1;
		while ( $row ) {
			$motif[0][$i]   = $row[0];
			$motif[1][$i++] = $row[1];
			$motif[2][$row[0]] = $row[1];

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

		// lecture des absences
		$tab_abs = Array();
		$query  = "select user_id._name, user_id._fname, absent_items._IDitem, absent_items._IDdata, absent_items._ID, user_id._ID ";
		$query .= "from user_id, absent_items ";
		$query .= "where absent_items._IDctn = '$IDitem' ";
		$query .= "AND user_id._ID = absent_items._IDabs ";
		$query .= "AND absent_items._IDctn";
    $query .= "AND user_id._adm >= 1 ";

		$result = mysqli_query($mysql_link, $query);
		$row = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		if ( $row )
		{
			while ( $row ) {
				// suppression de l'absence
				$req    = $msg->read($CTN_DELABSENT);
				$delete = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION["CnxID"] == $row[4] )
					? "<a href=\"".myurlencode("ctn_post_add.php?IDx=$IDx&item=$item&cmde=$cmde&IDitem=$IDitem&IDmat=$IDmat&IDclass=$IDclass&IDabsent=$row[2]&sd=$sd&st=$st&et=$et&submitx=del")."\" onclick=\"return confirmLink(this, '$req');\">
					   <i class=\"fa fa-trash\" title=\"$req\"></i>
					   </a>"
					: "" ;

				$index  = $row[3];

				print("$delete $row[0] $row[1] (".@$motif[2][$index].")<br/>");
				$tab_abs[] = $row[5];

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
		}


		print("
			<div id=\"absent\" style=\"border:#cccccc solid 1px; padding:5px;\">
		<input onclick=\"CocheTout(this, 'IDeleve[]');\" type=\"checkbox\" style=\"margin-left: 10px\"> <strong>". $msg->read($CTN_COCHEDECOCHE) ."</strong><br /><br/>");

		if($plus != 0)	// ***** Cours individuel *****//
		{
			if(!$ck_groupe)
			{
				// affichage des élèves
				$query  = "select _ID, _name, _fname, _IDmat, _lang from user_id ";
				$query .= "where _visible = 'O' AND _ID = '$plus' AND _IDclass != '0' AND _IDgrp = '1' AND _adm >= 1 ";
				$query .= ($IDgroup == 1) ? "AND _lang = 'fr' " : "";
				$query .= ($IDgroup == 2) ? "AND _lang = 'de' " : "";
				$query .= "order by _name, _fname";
			}
			else
			{
				// affichage des élèves
				$query  = "select user_id._ID, user_id._name, user_id._fname, user_id._IDmat, user_id._lang from user_id, groupe ";
				$query .= "where user_id._visible = 'O' AND user_id._IDclass = '$val' ";
				$query .= " AND user_id._IDclass != '0' AND user_id._IDgrp = '1' AND _adm >= 1 ";
				$query .= " AND user_id._ID = groupe._IDeleve ";
				$query .= " AND groupe._IDprof = $row3[6] ";
				$query .= " AND groupe._IDmat = $IDmat ";
				$query .= ($IDgroup == 1) ? "AND user_id._lang = 'fr' " : "";
				$query .= ($IDgroup == 2) ? "AND user_id._lang = 'de' " : "";
				$query .= "order by user_id._name, user_id._fname";
			}

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$isok   = ( $copy ) ? "disabled=\"disabled\"" : "" ;

			$u = 0;

			// Classe
			$querycl  = "select _ident from campus_classe ";
			$querycl .= "where _IDclass = $val ";

			$resultcl = mysqli_query($mysql_link, $querycl);
			$rowcl    = ( $resultcl ) ? remove_magic_quotes(mysqli_fetch_row($resultcl)) : 0 ;

			print("
						<div style=\"margin-top: -10px; font-weight: bold; margin-left: 10px; margin-bottom: 10px;\">$rowcl[0]</div>
				<table style=\"width: 100%;\">");
			echo "<tr>";

			while ( $row )
			{

				$aff = false;
				if($mat[1] == 1)
				{
					$list_mat = explode(" ", $row[3]);
					if(in_array($mat[2], $list_mat))
					{
						$aff = true;
					}
					else
					{
						$aff = false;
					}
				}
				else
				{
					$aff = true;
				}

				if(!in_array($row[0], $tab_abs) && $aff)
				{
					// Recherche si absence prévisionnelle pour l'élève
					$query  = "select distinctrow ";
					$query .= "user_id._name, user_id._fname, user_id._ID, user_id._IDclass, user_id._email, user_id._tel, ";
					$query .= "absent_items._email, absent_items._sms, absent_items._ID, absent_items._IP, absent_items._IDdata, absent_items._start, absent_items._IDctn, absent_items._texte, absent_items._IDitem, absent_items._IDmod, absent_items._isok, ";
					$query .= "campus_classe._ident, absent_items._end, absent_items._date, absent_items._valid ";
					$query .= "from user_id, absent_items, campus_classe ";
					$query .= "where (absent_items._start <= '$mdate $st' AND absent_items._end >= '$mdate $et') ";
					$query .= "AND user_id._ID = absent_items._IDabs ";
					$query .= "AND absent_items._IDctn ";
					$query .= "AND user_id._IDclass = campus_classe._IDclass ";
					$query .= "AND user_id._ID = '$row[0]' ";
          $query .= "AND user_id._adm >= 1 ";

					$resultx = mysqli_query($mysql_link, $query);
					$rowx    = ( $resultx ) ? remove_magic_quotes(mysqli_fetch_row($resultx)) : 0 ;

					// détermination du nombre d'absences
					$nbabs   = ( $resultx ) ? mysqli_affected_rows($mysql_link) : 0 ;
					$checked = ( $nbabs ) ? "checked=\"checked\"" : "";
					$textabs = ( $nbabs ) ? $rowx[13] : "";

					// Recherche si absence dans la journée
					$queryabs  = "SELECT DISTINCT absent_items._start, absent_items._end, campus_data._titre FROM absent_items, ctn_items, campus_data, absent_data WHERE absent_items._IDabs = $row[0] ";
					$queryabs .= "AND absent_data._IDdata = absent_items._IDdata ";
					$queryabs .= "AND absent_data._texte NOT LIKE '%[NC]%' ";
					$queryabs .= "AND absent_items._start >= '$mdate 00:00:00' ";
					$queryabs .= "AND absent_items._end <= '$mdate 23:59:59' ";
					$queryabs .= "AND absent_items._IDctn = ctn_items._IDitem ";
					$queryabs .= "AND ctn_items._IDmat = campus_data._IDmat ";
					$queryabs .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";

					$resultabs = mysqli_query($mysql_link, $queryabs);
					$rowabs    = ( $resultabs ) ? remove_magic_quotes(mysqli_fetch_row($resultabs)) : 0 ;

					if($u % 3 == 0 && $u != 0)
					{
						echo "</td></tr><tr><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
					}
					else
					{
						echo "</td><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
					}

					print("<label for=\"IDeleve_$row[0]\"><input type=\"checkbox\" id=\"IDeleve_$row[0]\" name=\"IDeleve[]\" value=\"$row[0]\" $isok $checked /></label>");

					if($rowabs)
					{
						$txt_abs = "";
						while ($rowabs)
						{
							$txt_abs .= "$rowabs[2] : ".substr($rowabs[0], strpos($rowabs[0], " ")+1)." - ". substr($rowabs[1], strpos($rowabs[1], " ")+1)."<br />";
							$rowabs = remove_magic_quotes(mysqli_fetch_row($resultabs));
						}
						print(" <a class=\"overlib\"><font style=\"display: inline-block; background-color: #cccccc; border-radius: 4px; padding: 3px;\"><strong>$row[1] $row[2]</strong></font><span>$txt_abs</span></a>");
					}
					else
					{
						print(" $row[1] $row[2]");
					}

					print("</td><td style=\"border-right: 1px solid #AAAAAA; width: 60px; vertical-align: top;\"><label for=\"IDmotif_$row[0]\">");
					print("<select id=\"IDmotif_$row[0]\" name=\"IDmotif_$row[0]\" class=\"selectmotif\" style=\"width: 170px\">");

					for ($i = 1; $i < count($motif[0]); $i++)
					{
						if($rowx[10] == $motif[0][$i])
						{
							print("<option selected value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
						}
						else
						{
							print("<option value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
						}
					}

					print("</select>");
					print("</label>
								<textarea name=\"textautre_$row[0]\" id=\"textautre_$row[0]\" style=\"display: none\">$textabs</textarea>");

					$u++;
				}
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

			echo "</td></tr>";
			print ("</table>");
		}
		else if($IDgrp != 0)	// ***** Cours groupe *****//
		{
			// affichage des élèves
			$query  = "SELECT u._ID, u._name, u._fname, u._IDmat, u._lang ";
			$query .= "FROM user_id u, groupe g ";
			$query .= "WHERE g._IDeleve = u._ID AND _adm >= 1 ";
			$query .= "AND g._IDgrp = $IDgrp ";
			$query .= "ORDER BY u._name, u._fname";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			print("<hr style=\"margin-top: 10px; margin-bottom: 15px; margin-left: 40px; border-bottom: 1px solid #E5E5E5\" />
						<div style=\"margin-top: -25px; font-weight: bold; margin-left: 10px; margin-bottom: 10px;\">".getNomByIDgrp($IDgrp)."</div>
				<table width=\"1150\" style=\"width: 100%;\">");
			echo "<tr>";

			while ( $row )
			{

				$aff = false;
				if($mat[1] == 1)
				{
					$list_mat = explode(" ", $row[3]);
					if(in_array($mat[2], $list_mat))
					{
						$aff = true;
					}
					else
					{
						$aff = false;
					}
				}
				else
				{
					$aff = true;
				}

				if(!in_array($row[0], $tab_abs) && $aff)
				{
					// Recherche si absence prévisionnelle pour l'élève
					$query  = "select distinctrow ";
					$query .= "user_id._name, user_id._fname, user_id._ID, user_id._IDclass, user_id._email, user_id._tel, ";
					$query .= "absent_items._email, absent_items._sms, absent_items._ID, absent_items._IP, absent_items._IDdata, absent_items._start, absent_items._IDctn, absent_items._texte, absent_items._IDitem, absent_items._IDmod, absent_items._isok, ";
					$query .= "campus_classe._ident, absent_items._end, absent_items._date, absent_items._valid ";
					$query .= "from user_id, absent_items, campus_classe ";
					$query .= "where (absent_items._start <= '$mdate $st' AND absent_items._end >= '$mdate $et') ";
					$query .= "AND user_id._ID = absent_items._IDabs ";
					$query .= "AND absent_items._IDctn ";
					$query .= "AND user_id._IDclass = campus_classe._IDclass ";
					$query .= "AND user_id._ID = '$row[0]' ";
          $query .= "AND user_id._adm >= 1 ";

					$resultx = mysqli_query($mysql_link, $query);
					$rowx    = ( $resultx ) ? remove_magic_quotes(mysqli_fetch_row($resultx)) : 0 ;

					// détermination du nombre d'absences
					$nbabs   = ( $resultx ) ? mysqli_affected_rows($mysql_link) : 0 ;
					$checked = ( $nbabs ) ? "checked=\"checked\"" : "";
					$textabs = ( $nbabs ) ? $rowx[13] : "";

					// Recherche si absence dans la journée
					$queryabs  = "SELECT DISTINCT absent_items._start, absent_items._end, campus_data._titre FROM absent_items, ctn_items, campus_data, absent_data WHERE absent_items._IDabs = $row[0] ";
					$queryabs .= "AND absent_data._IDdata = absent_items._IDdata ";
					$queryabs .= "AND absent_data._texte NOT LIKE '%[NC]%' ";
					$queryabs .= "AND absent_items._start >= '$mdate 00:00:00' ";
					$queryabs .= "AND absent_items._end <= '$mdate 23:59:59' ";
					$queryabs .= "AND absent_items._IDctn = ctn_items._IDitem ";
					$queryabs .= "AND ctn_items._IDmat = campus_data._IDmat ";
					$queryabs .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";

					$resultabs = mysqli_query($mysql_link, $queryabs);
					$rowabs    = ( $resultabs ) ? remove_magic_quotes(mysqli_fetch_row($resultabs)) : 0 ;

					if($u % 3 == 0 && $u != 0)
					{
						echo "</td></tr><tr><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
					}
					else
					{
						echo "</td><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
					}

					print("<label for=\"IDeleve_$row[0]\"><input type=\"checkbox\" id=\"IDeleve_$row[0]\" name=\"IDeleve[]\" value=\"$row[0]\" $isok $checked /></label>");

					if($rowabs)
					{
						$txt_abs = "";
						while ($rowabs)
						{
							$txt_abs .= "$rowabs[2] : ".substr($rowabs[0], strpos($rowabs[0], " ")+1)." - ". substr($rowabs[1], strpos($rowabs[1], " ")+1)."<br />";
							$rowabs = remove_magic_quotes(mysqli_fetch_row($resultabs));
						}
						print(" <a class=\"overlib\"><font style=\"display: inline-block; background-color: #cccccc; border-radius: 4px; padding: 3px;\"><strong>$row[1] $row[2]</strong></font><span>$txt_abs</span></a>");
					}
					else
					{
						print(" $row[1] $row[2]");
					}

					print("</td><td style=\"border-right: 1px solid #AAAAAA; width: 60px; vertical-align: top;\"><label for=\"IDmotif_$row[0]\">");
					print("<select id=\"IDmotif_$row[0]\" name=\"IDmotif_$row[0]\" class=\"selectmotif\" style=\"width: 170px\">");

					for ($i = 1; $i < count($motif[0]); $i++)
					{
						if($rowx[10] == $motif[0][$i])
						{
							print("<option selected value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
						}
						else
						{
							print("<option value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
						}
					}

					print("</select>");
					print("</label>
								<textarea name=\"textautre_$row[0]\" id=\"textautre_$row[0]\" style=\"display: none\">$textabs</textarea>");

					$u++;
				}
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

			echo "</td></tr>";
			print ("</table>");
		}
		else	// ***** Cours classe *****//
		{
			foreach($array_class as $val)
			{
				if($val != "")
				{
					// affichage des élèves de la classe
					$query  = "select _ID, _name, _fname, _IDmat, _lang from user_id ";
					$query .= "where _visible = 'O' AND _IDclass = '$val' AND _IDclass != '0' AND _IDgrp = '1' AND _adm >= 1 ";
					$query .= ($IDgroup == 1) ? "AND _lang = 'fr' " : "";
					$query .= ($IDgroup == 2) ? "AND _lang = 'de' " : "";
					$query .= "order by _name, _fname";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					$isok   = ( $copy ) ? "disabled=\"disabled\"" : "" ;

					$u = 0;

					// Classe
					$querycl  = "select _ident from campus_classe ";
					$querycl .= "where _IDclass = $val ";

					$resultcl = mysqli_query($mysql_link, $querycl);
					$rowcl    = ( $resultcl ) ? remove_magic_quotes(mysqli_fetch_row($resultcl)) : 0 ;

					print("<hr style=\"margin-top: 10px; margin-bottom: 15px; margin-left: 40px; border-bottom: 1px solid #E5E5E5\" />
								<div style=\"margin-top: -25px; font-weight: bold; margin-left: 10px; margin-bottom: 10px;\">$rowcl[0]</div>
						<table width=\"1150\" style=\"width: 100%;\">");
					echo "<tr>";

					while ( $row )
					{

						$aff = false;
						if($mat[1] == 1)
						{
							$list_mat = explode(" ", $row[3]);
							if(in_array($mat[2], $list_mat))
							{
								$aff = true;
							}
							else
							{
								$aff = false;
							}
						}
						else
						{
							$aff = true;
						}

						if(!in_array($row[0], $tab_abs) && $aff)
						{
							// Recherche si absence prévisionnelle pour l'élève
							$query  = "select distinctrow ";
							$query .= "user_id._name, user_id._fname, user_id._ID, user_id._IDclass, user_id._email, user_id._tel, ";
							$query .= "absent_items._email, absent_items._sms, absent_items._ID, absent_items._IP, absent_items._IDdata, absent_items._start, absent_items._IDctn, absent_items._texte, absent_items._IDitem, absent_items._IDmod, absent_items._isok, ";
							$query .= "campus_classe._ident, absent_items._end, absent_items._date, absent_items._valid ";
							$query .= "from user_id, absent_items, campus_classe ";
							$query .= "where (absent_items._start <= '$mdate $st' AND absent_items._end >= '$mdate $et') ";
							$query .= "AND user_id._ID = absent_items._IDabs ";
							$query .= "AND absent_items._IDctn ";
							$query .= "AND user_id._IDclass = campus_classe._IDclass ";
							$query .= "AND user_id._ID = '$row[0]' ";
              $query .= "AND user_id._adm >= 1 ";

							$resultx = mysqli_query($mysql_link, $query);
							$rowx    = ( $resultx ) ? remove_magic_quotes(mysqli_fetch_row($resultx)) : 0 ;

							// détermination du nombre d'absences
							$nbabs   = ( $resultx ) ? mysqli_affected_rows($mysql_link) : 0 ;
							$checked = ( $nbabs ) ? "checked=\"checked\"" : "";
							$textabs = ( $nbabs ) ? $rowx[13] : "";

							// Recherche si absence dans la journée
							$queryabs  = "SELECT DISTINCT absent_items._start, absent_items._end, campus_data._titre FROM absent_items, ctn_items, campus_data, absent_data WHERE absent_items._IDabs = $row[0] ";
							$queryabs .= "AND absent_data._IDdata = absent_items._IDdata ";
							$queryabs .= "AND absent_data._texte NOT LIKE '%[NC]%' ";
							$queryabs .= "AND absent_items._start >= '$mdate 00:00:00' ";
							$queryabs .= "AND absent_items._end <= '$mdate 23:59:59' ";
							$queryabs .= "AND absent_items._IDctn = ctn_items._IDitem ";
							$queryabs .= "AND ctn_items._IDmat = campus_data._IDmat ";
							$queryabs .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";

							$resultabs = mysqli_query($mysql_link, $queryabs);
							$rowabs    = ( $resultabs ) ? remove_magic_quotes(mysqli_fetch_row($resultabs)) : 0 ;

							if($u % 3 == 0 && $u != 0)
							{
								echo "</td></tr><tr><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
							}
							else
							{
								echo "</td><td style=\"padding-left: 3px; width: 170px; vertical-align: top; padding-left: 10px\">";
							}

							print("<label for=\"IDeleve_$row[0]\"><input type=\"checkbox\" id=\"IDeleve_$row[0]\" name=\"IDeleve[]\" value=\"$row[0]\" $isok $checked /></label>");

							if($rowabs)
							{
								$txt_abs = "";
								while ($rowabs)
								{
									$txt_abs .= "$rowabs[2] : ".substr($rowabs[0], strpos($rowabs[0], " ")+1)." - ". substr($rowabs[1], strpos($rowabs[1], " ")+1)."<br />";
									$rowabs = remove_magic_quotes(mysqli_fetch_row($resultabs));
								}
								print(" <a class=\"overlib\"><font style=\"display: inline-block; background-color: #cccccc; border-radius: 4px; padding: 3px;\"><strong>$row[1] $row[2]</strong></font><span>$txt_abs</span></a>");
							}
							else
							{
								print(" $row[1] $row[2]");
							}

							print("</td><td style=\"border-right: 1px solid #AAAAAA; width: 60px; vertical-align: top;\"><label for=\"IDmotif_$row[0]\">");
							print("<select id=\"IDmotif_$row[0]\" name=\"IDmotif_$row[0]\" class=\"selectmotif\" style=\"width: 170px\">");

							for ($i = 1; $i < count($motif[0]); $i++)
							{
								if($rowx[10] == $motif[0][$i])
								{
									print("<option selected value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
								}
								else
								{
									print("<option value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");
								}
							}

							print("</select>");
							print("</label>
										<textarea name=\"textautre_$row[0]\" id=\"textautre_$row[0]\" style=\"display: none\">$textabs</textarea>");

							$u++;
						}
						$row = remove_magic_quotes(mysqli_fetch_row($result));
					}

					echo "</td></tr>";
					print ("</table>");
				}
			}
		}
			print("
			</div>");

		print("
	      	</td>
		    </tr>
		  </table>
		    	");

	    	print("
					<hr style=\"width:80%;\" />

					<table class=\"width100\">
						<tr>
							<td class=\"valign-middle align-center\" style=\"width: 19%; padding-right: 10px;\">
								<!-- <a href=\"javascript: self.close();\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($CTN_INPUTCANCEL)."\" /></a> -->");

						/*$sqla  = "select edt_data._IDx, user_id._name, user_id._fname ";
						$sqla .= "from edt_data, user_id ";
						$sqla .= "where edt_data._IDclass = '$row3[4]' ";
						$sqla .= "and edt_data._debut = '$row3[10]' ";
						$sqla .= "and edt_data._fin = '$row3[11]' ";
						$sqla .= "and edt_data._nosemaine = $row3[13] ";
						$sqla .= "and edt_data._annee = $row3[16] ";
						$sqla .= "and edt_data._IDmat = $row3[3] ";
						$sqla .= "and edt_data._IDx != $IDx ";
						$sqla .= "and edt_data._ID = user_id._ID ";
						$handla = mysqli_query($mysql_link, $sqla);
						$num_rows = mysqli_num_rows($handla);
						$rowa    = ( $handla ) ? remove_magic_quotes(mysqli_fetch_row($handla)) : 0 ;

						if($num_rows > 0 && $ck_groupe)
						{
							?>
							    <div class="btn-group">
									<button class="btn btn-success" type="submit"><?php echo $msg->read($CTN_INPUTOK); ?></button>
									<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#" style="height: 20px;"><span class="caret"></span></a>
									<ul class="dropdown-menu">
										<?php
										while($rowa)
										{
											echo "<li style=\"text-align: left;\"><button onclick=\"jQuery('#nextID').val('$rowa[0]')\" type=\"submit\" class=\"btnnext\"><strong>".$msg->read($CTN_INPUTOK)." & ".$msg->read($CTN_MODIFY)."</strong> $rowa[1] $rowa[2]</a></li>";
											$rowa = remove_magic_quotes(mysqli_fetch_row($handla));
										}
										?>
									</ul>
								</div>
							<?php
						}*/
						// else
						// {
							echo "<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CTN_INPUTOK)."\" style=\"margin-left: 30px;\" />";
						// }

						echo '<button type="valid" class="btn btn-success">Valider</button>';

			print("
		              </td>
		           </tr>
		         </table>

			</form>
			<br />
			<br />
			<br />
			<br />
			<br />
			");
		}
?>

<script>
jQuery(document).ready(function()
{
	jQuery(".selectmotif").each(function()
	{
		this_parent = this;
		jQuery(this).change(function()
		{
			if(jQuery(this).val() == 16)
			{
				jQuery(this).parent().parent().find("textarea").css("display", "block");
			}
			else
			{
				jQuery(this).parent().parent().find("textarea").css("display", "none");
			}
		});
	});
});
</script>

</div>

<!-- CKEditor -->
<script type="text/javascript" src="js/ckeditor/ckeditor4/ckeditor.js"></script>
