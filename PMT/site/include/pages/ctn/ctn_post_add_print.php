<?php
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

require_once "page_session.php";
require "msg/ctn.php";
require_once "include/TMessage.php";
require_once "include/calendar_tools.php";

$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/ctn.php");
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;
require "page_banner.php";
include_once("php/functions.php");

if (!$submit)
{
	// Item correspondant
	$sql2 = "select * from ctn_items where _type = 0 and _nosemaine = ".date("W", js2PhpTime($sd))." and _IDcours = ".$IDx." LIMIT 1";
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
$array_class = @split(";", $IDclass);
$ck_groupe = $row3[17];

$IDpj     = (int) @$_GET["IDpj"];
$IDabsent = (int) @$_GET["IDabsent"];

$mdate    = ( @$_POST["mdate"] ) ? $_POST["mdate"] : date("Y-m-d") ;
$mtime    = @$_POST["mtime"];
$delay    = @$_POST["delay"];
$title    = addslashes(trim(@$_POST["title"]));
$texte    = addslashes(trim(@$_POST["texte"]));
$devoirs    = addslashes(trim(@$_POST["devoirs"]));
$note     = addslashes(trim(@$_POST["note"]));
$edit     = ( strlen(@$_POST["edit"]) )		// mode d'édition : basique ou avancé
	? (int) $_POST["edit"]
	: (int) (strlen(@$_GET["edit"]) ? $_GET["edit"] : $WYSIWYG) ;
$sndmail  = ( @$_POST["sndmail"] ) ? "O" : "N" ;

$submit   = ( @$_POST["submit"] )			// bouton de validation
	? $_POST["submit"]
	: @$_GET["submit"] ;

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
//---------------------------------------------------------------------------
?>

<style>
body {
	padding-top: 0px;
}
</style>

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

<div class="btn-primary">
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
</div>

<div class="maincontent" style="margin-top: 0px;">

<?php
	require_once "include/agenda.php";

	// on revient sur le cahier de texte numérique
	$retour  = myurlencode("index.php?item=$item&IDcentre=$IDcentre&IDmat=$IDmat&IDgroup=$IDgroup&IDclass=$IDclass&salon=") . $_SESSION["CampusName"];

	$error_title = $error_date = 0;

	// lecture de la matière
	$return     = mysqli_query($mysql_link, "select _titre from campus_data where _IDmat = '$IDmat' AND _lang = '".$_SESSION["lang"]."' limit 1");
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
			$copymail   .= $title ."\n". $texte;

			if ( !$error_title AND !$error_date ) {
				$list    = explode(",", $msg->read($CTN_TODO));
				$raw     = ( $edit ) ? "N" : "O" ;				// mode SPIP ou WYSIWYG
				$date    = ( $auth[1] == "O" ) ? "$mdate " . date("H:i:s") : "$mdate $mtime" ;
				$idclass = ( $copy ) ? $copy : $IDclass ;

				if ( $IDitem AND $copy == 0 ) {
					$Query  = "update ctn_items ";
					$Query .= "set _IDmat = '$IDmat', _title = '$title', _texte = '$texte', _devoirs = '$devoirs', _date = '$mdate $mtime', _delay = '$delay', _note = '$note', _type = '$type' ";
					$Query .= "where _IDitem = '$IDitem' ";
					$Query .= "limit 1";
					}
				else {
					$return = mysqli_query($mysql_link, "select _IDctn from ctn where _IDgroup = '$IDgroup' AND _IDclass = '$idclass' limit 1");
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

					$Query  = "insert into ctn_items ";
					$Query .= "values('', '$myrow[0]', '$IDmat', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$delay', '$title', '$texte', '$raw', '$note', 'O', '$type', '$IDx', '".date("W", js2PhpTime($sd))."', '$devoirs')";
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

					for ($i = 0; $i < count($IDeleve); $i++)
						if ( @$IDeleve[$i] ) {
							$index   = $IDeleve[$i];
							$IDmotif = @$_POST["IDmotif_$index"];

							$Query   = "insert into absent_items ";
							$Query  .= "values('', '$IDmotif', '$parent', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '1', '$index', '$start', '$end', '$note', '', 'O', 'N', '0', '', '', '0', '', 'O', '', '')";

							mysqli_query($mysql_link, $Query);
							}

					//---- transfert d'une Pièce Jointe (item)
					$file = @$_FILES["UploadPJ"]["tmp_name"];

					for ($i = 0; $i < count($file) && @$file[$i]; $i++)
						if ( authfile(@$_FILES["UploadPJ"]["name"][$i]) ) {
							// fichier destination
							$ext    = extension(@$_FILES["UploadPJ"]["name"][$i]);
							$size   = (int) @$_FILES["UploadPJ"]["size"][$i];

							$PJdesc = @$_POST["PJdesc"];

							$Query  = "insert into ctn_pj ";
							$Query .= "values('', '$parent', '".addslashes(trim($PJdesc[$i]))."', '$ext', '$size', '0')";

							if ( mysqli_query($mysql_link, $Query) )
								// copie du fichier temporaire -> répertoire de stockage
								if ( move_uploaded_file($file[$i], "$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext") )
									mychmod("$DOWNLOAD/ctn/". mysqli_insert_id($mysql_link) .".$ext", $CHMOD);
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
						$return = mysqli_query($mysql_link, "select _ID from user_id where _adm AND _IDclass = '$IDclass' AND _visible = 'O'");
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
						$return = mysqli_query($mysql_link, "select _ID from user_id where _adm AND _IDclass = '$IDclass' AND _visible = 'O'");
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

						$Query  = "select _email from user_id ";
						$Query .= "where _ID = '".$_SESSION["CnxID"]."'";

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

						print("
							<script type=\"text/javascript\">
							self.close();
							</script>");
					}
				}
			break;

		case "del" :
		case "delpj" :
			$query  = "delete from " . (( $submit == "del" ) ? "absent_items " : "ctn_pj ") ;
			$query .= ( $submit == "del" ) ? "where _IDitem = '$IDabsent' " : "where _IDpj = '$IDpj' " ;
			$query .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 ) ? "" : "AND _ID = '".$_SESSION["CnxID"]."' " ;
			$query .= "limit 1";

			if ( mysqli_query($mysql_link, $query) AND $submit == "delpj" )
				@unlink("$DOWNLOAD/ctn/$IDpj.*");
			// on enchaine...

		default :
			// initialisation des champs de saisie
			$query   = "select _date, _title, _texte, _delay, _raw, _note, _type, _devoirs from ctn_items ";
			$query  .= "where _IDitem = '$IDitem' ";
			$query  .= "limit 1";

			$result  = mysqli_query($mysql_link, $query);
			$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			list($mdate, $mtime) = ( $row )
				? explode(" ", $row[0])
				: explode(" ", date("Y-m-d H:i:s")) ;
			$title   = ( $row ) ? $row[1] : $msg->read($CTN_CHAPTER) ;
			$texte   = ( $row ) ? $row[2] : "" ;
			$devoirs   = $row[7];
			$delay   = $row[3];
			$raw     = $row[4];
			$PJdesc  = "";
			$note    = $row[5];
			$type    = $row[6];
			break;
		}

	// saisie du formulaire si
	// - l'utilisateur n'a pas validé OU
	// - il y a une erreur de saisie => on redemande de compléter le formulaire
	if ( $submit != "Valider" OR $error_title OR $error_date ) {

		// les matières enseignées
		$query  = "select _IDmat from user_id ";
		$query .= "where _ID = '".$_SESSION["CnxID"]."' ";
		$query .= "limit 1";

		$return = mysqli_query($mysql_link, $query);
		$perm   = ( $return ) ? mysqli_fetch_row($return ) : 0 ;

		print("
		<form id=\"formulaire\" name=\"formulaire\" action=\"ctn_post_add.php\" method=\"post\" enctype=\"multipart/form-data\">
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDcentre\" value=\"$IDcentre\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDmat\"    value=\"$IDmat\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDx\"    value=\"$IDx\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"sd\"    value=\"$sd\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDgroup\"  value=\"$IDgroup\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDclass\"  value=\"$IDclass\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDitem\"   value=\"$IDitem\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"type\"     value=\"0\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"edit\"     value=\"$edit\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"post\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"submit\"   value=\"Valider\" /></p>

		<a style=\"float: right\" href=\"#\" onclick=\"javascript:window.print()\"><i class=\"fa fa-print\" style=\"color: black\"></i></a>
		
		<table class=\"width100\" style=\"font-size: 11px;\">
		  <tr>
		    <td style=\"width:19%;\" class=\"valign-middle align-right\">". $msg->read($CTN_GETMATTER) ."</td>
		    <td style=\"width:41%;\" class=\"valign-middle\">".$mat[0]."</td>
		    <td class=\"align-right\">
		    </td>
		  </tr>");

		print("
		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_MYDATE) ."
		      </td>
		      <td colspan=\"2\">
		        <p style=\"margin:0px;\">");
		
		echo date('d/m/Y', js2PhpTime($sd))." de $st à $et";
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
			$editor  = "<a href=\"index.php?item=$item&amp;cmde=$cmde&amp;IDmat=$IDmat&amp;IDclass=$IDclass&amp;edit=$toggle\">-> ";
			$editor .= ( $edit ) ? $msg->read($CTN_BASIC) : $msg->read($CTN_ADVANCED) ;
			$editor .= "</a>";
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

		$i = 0;
		while ( $row ) {
			$motif[0][$i]   = $row[0];
			$motif[1][$i++] = $row[1];

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

		// lecture des absences
		$query  = "select user_id._name, user_id._fname, absent_items._IDitem, absent_items._IDdata, absent_items._ID ";
		$query .= "from user_id, absent_items ";
		$query .= "where absent_items._IDctn = '$IDitem' ";
		$query .= "AND user_id._ID = absent_items._IDabs ";
		$query .= "AND absent_items._IDctn";

		$result = mysqli_query($mysql_link, $query);
		$row = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		if ( $row )
		{
			while ( $row ) {
				// suppression de l'absence
				$req    = $msg->read($CTN_DELABSENT);
				$delete = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION["CnxID"] == $row[4] )
					? "<a href=\"".myurlencode("ctn_post_add.php?IDx=$IDx&item=$item&cmde=$cmde&IDitem=$IDitem&IDmat=$IDmat&IDclass=$IDclass&IDabsent=$row[2]&submit=del")."\" onclick=\"return confirmLink(this, '$req');\">
					   <img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"$req\" alt=\"$req\" />
					   </a>"
					: "" ;

				$index  = $row[3] - 1;

				print("$delete $row[0] $row[1] (".@$motif[1][$index].")<br/>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
		}			

		print("
			<div id=\"absent\" style=\"display:block; border:#cccccc solid 1px; padding:5px;\">");

		foreach($array_class as $val) {
			if($val != "")
			{
				print("<table width=\"100%\" style=\"font-size: 11px;\">");	
				
				if(!$ck_groupe)
				{
					// affichage des élèves
					$query  = "select _ID, _name, _fname, _IDmat, _lang from user_id ";
					$query .= "where _visible = 'O' AND _IDclass = '$val' AND _IDclass != '0' AND _IDgrp = '1' ";
					$query .= ($IDgroup == 1) ? "AND _lang = 'fr' " : "";
					$query .= ($IDgroup == 2) ? "AND _lang = 'de' " : "";
					$query .= "order by _name, _fname";
				}
				else
				{					// affichage des élèves					$query  = "select user_id._ID, user_id._name, user_id._fname, user_id._IDmat, user_id._lang from user_id, groupe ";					$query .= "where user_id._visible = 'O' AND user_id._IDclass = '$val' ";					$query .= " AND user_id._IDclass != '0' AND user_id._IDgrp = '1' ";					$query .= " AND user_id._ID = groupe._IDeleve ";					$query .= " AND groupe._IDprof = $row3[6] ";					$query .= " AND groupe._IDmat = $IDmat ";					$query .= ($IDgroup == 1) ? "AND user_id._lang = 'fr' " : "";					$query .= ($IDgroup == 2) ? "AND user_id._lang = 'de' " : "";					$query .= "order by user_id._name, user_id._fname";
				}

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;


				$isok   = ( $copy ) ? "disabled=\"disabled\"" : "" ;
				
				$u = 0;
				echo "<tr>";
				while ( $row ) {
			
					if($u % 4 == 0 && $u != 0)
					{
						echo "</td></tr><tr><td style=\"padding-left: 3px\">";
					}
					else
					{
						echo "</td><td style=\"padding-left: 3px\">";
					}
				
					print("<label for=\"IDeleve_$row[0]\"><input type=\"checkbox\" id=\"IDeleve_$row[0]\" name=\"IDeleve[]\" value=\"$row[0]\" $isok /></label> $row[1] $row[2]");
					
					print("</td><td><label for=\"IDmotif_$row[0]\">");
					print("<input type=\"text\" size=\"30\" />");
					
					$u++;
					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}

					echo "</td></tr>";
					
					print("</table><hr />");
			}
		}
			print("
			</div>
	      	</td>
		    </tr>
		  </table>
		    	");

	    	print("
			<hr style=\"width:80%;\" />
			</form>
			");
		}
?>

</div>