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


$IDcentre = ( @$_POST["IDcentre"] )				// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
$IDgroup  = ( @$_POST["IDgroup"] )			// ID du e-groupe
	? (int) $_POST["IDgroup"]
	: (int) @$_GET["IDgroup"] ;
$IDclass  = ( @$_POST["IDclass"] )			// sélection de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDmat    = ( @$_POST["IDmat"] )			// ID de la matière
	? (int) $_POST["IDmat"]
	: (int) @$_GET["IDmat"] ;
$IDitem   = ( @$_POST["IDitem"] )			// ID de l'item du ctn
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$copy     = ( @$_POST["copy"] )			// ID de la classe à dupliquer
	? (int) $_POST["copy"]
	: (int) @$_GET["copy"] ;
$type     = ( @$_POST["type"] )
	? (int) $_POST["type"]
	: (int) @$_GET["type"] ;

$IDpj     = (int) @$_GET["IDpj"];
$IDabsent = (int) @$_GET["IDabsent"];

$mdate    = ( @$_POST["mdate"] ) ? $_POST["mdate"] : date("Y-m-d") ;
$mtime    = @$_POST["mtime"];
$delay    = @$_POST["delay"];
$title    = addslashes(trim(@$_POST["title"]));
$texte_ckeditor    = addslashes(trim(@$_POST["texte_ckeditor"]));
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


<!-- <script src="script/ckeditor/ckeditor.js"></script> -->

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
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

		if ( $submit AND ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $auth[0]) OR $_SESSION["CnxGrp"] == 2)
		{

		}
		else
		{
			exit(1);
		}

		print($msg->read($CTN_MYCTN, $auth[0]) ."<br/>". $msg->read($CTN_FORMFEED));
	?>
	</div>
</div>

<div class="maincontent">

<?php
	require_once "include/agenda.php";

	// on revient sur le cahier de texte numérique
	$retour  = myurlencode("index.php?item=$item&IDcentre=$IDcentre&IDmat=$IDmat&IDgroup=$IDgroup&IDclass=$IDclass&salon=") . $_SESSION["CampusName"];

	$error_title = $error_date = 0;

	// commande de l'utilisateur
	switch ( $submit ) {
		case "Valider" :	// l'utilisateur a posté son message
			// test de la saisie
			$error_title = ( strlen(trim($title)) ) ? 0 : 1 ;

			//----- copie par mél -----//
			// lecture de la classe
			$return      = mysqli_query($mysql_link, "select _ident from campus_classe where _IDclass = '$IDclass' limit 1");
			$classe      = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			// lecture de la matière
			$return     = mysqli_query($mysql_link, "select _titre from campus_data where _IDmat = '$IDmat' AND _lang = '".$_SESSION["lang"]."' limit 1");
			$mat        = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

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
					$Query .= "set _IDmat = '$IDmat', _title = '$title', _texte = '$texte_ckeditor', _date = '$mdate $mtime', _delay = '$delay', _note = '$note', _type = '$type' ";
					$Query .= "where _IDitem = '$IDitem' ";
					$Query .= "limit 1";
					}
				else {
					$return = mysqli_query($mysql_link, "select _IDctn from ctn where _IDgroup = '$IDgroup' AND _IDclass = '$idclass' limit 1");
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

					$Query  = "insert into ctn_items ";
					$Query .= "values('', '$myrow[0]', '$IDmat', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$delay', '$title', '$texte_ckeditor', '$raw', '$note', 'O', '$type', '', '')";
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
					$query  = "select _diary from ctn ";
					$query .= "where _IDclass = '$IDclass' ";
					$query .= "limit 1";

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
						<p class=\"center\">
      						". $msg->read($CTN_RECORD) ."<br/>
      						". $msg->read($CTN_THANX) ."<br/>
						      [<a href=\"$retour\">". $msg->read($CTN_BACK) ."</a>]
						</p>
						");
					}
				}
			break;

		case "del" :
		case "delpj" :
			$query  = "delete from " . (( $submit == "del" ) ? "absent_items " : "ctn_pj ") ;
			$query .= ( $submit == "del" ) ? "where _IDitem = '$IDabsent' " : "where _IDpj = '$IDpj' " ;
			$query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _ID = '".$_SESSION["CnxID"]."' " ;
			$query .= "limit 1";

			if ( mysqli_query($mysql_link, $query) AND $submit == "delpj" )
				@unlink("$DOWNLOAD/ctn/$IDpj.*");
			// on enchaine...

		default :
			// initialisation des champs de saisie
			$query   = "select _date, _title, _texte, _delay, _raw, _note, _type from ctn_items ";
			$query  .= "where _IDitem = '$IDitem' ";
			$query  .= "limit 1";

			$result  = mysqli_query($mysql_link, $query);
			$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			list($mdate, $mtime) = ( $row )
				? explode(" ", $row[0])
				: explode(" ", date("Y-m-d H:i:s")) ;
			$title   = ( $row ) ? $row[1] : $msg->read($CTN_CHAPTER) ;
			$texte_ckeditor   = ( $row ) ? $row[2] : "" ;
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
		<form id=\"formulaire\" name=\"formulaire\" action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDcentre\" value=\"$IDcentre\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDmat\"    value=\"$IDmat\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDgroup\"  value=\"$IDgroup\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDclass\"  value=\"$IDclass\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDitem\"   value=\"$IDitem\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"type\"     value=\"$type\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"edit\"     value=\"$edit\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"post\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"submit\"   value=\"Valider\" /></p>

		<table class=\"width100\">
		  <tr>
		    <td style=\"width:19%;\" class=\"valign-middle align-right\">". $msg->read($CTN_GETMATTER) ."</td>
		    <td style=\"width:41%;\" class=\"valign-middle\">
			  <label for=\"ctn_IDmat\">
				<select id=\"ctn_IDmat\" name=\"IDmat\">");

				$query  = "select _IDmat, _titre from campus_data ";
				$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
				$query .= "order by _titre";

				$return = mysqli_query($mysql_link, $query);
				$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

				while ( $myrow ) {
					$select = ( $myrow[0] == $IDmat ) ? "selected=\"selected\"" : "" ;

					if ( !strlen(trim($perm[0])) OR in_array($myrow[0], explode(" ", $perm[0])) )
						print("<option value=\"$myrow[0]\" $select>$myrow[1]</option>");

				$myrow = remove_magic_quotes(mysqli_fetch_row($return));
				}

		$check = ( $$auth[5] == "O" ) ? "checked" : "" ;

		print("
				</select>
			  </label>
		    </td>
		    <td class=\"align-right\">
			". $msg->read($CTN_SNDMAIL) ."
           		<label for=\"sndmail\">
				<input type=\"checkbox\" id=\"sndmail\" name=\"sndmail\" checked=\"$check\" value=\"O\" />
			</label>
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
		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_MYDATE) ."
		      </td>
		      <td colspan=\"2\">
		        <p style=\"margin:0px;\">
			  <label for=\"is_mdate\"><input type=\"text\" id=\"is_mdate\" name=\"mdate\" size=\"10\" value=\"$mdate\" /> ". $msg->read($CTN_AT) ."</label>
			  <label for=\"is_mtime\"><input type=\"text\" id=\"is_mtime\" name=\"mtime\" size=\"10\" value=\"$mtime\" /></label>");

		// calendrier surgissant
		CalendarPopup("id1", "document.formulaire.mdate");

		print("
		        </p>
	      	</td>
		    </tr>

		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_MYDELAY) ."
		      </td>
		      <td colspan=\"2\">
			  <label for=\"delay\">
				<select id=\"delay\" name=\"delay\">");

				$query  = "select _horaire from ctn ";
				$query .= "where _IDcentre = '$IDcentre' AND _IDgroup = '$IDgroup' AND _IDclass = '$IDclass' ";
				$query .= "limit 1";

				$return = mysqli_query($mysql_link, $query);
				$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

				$list   = explode(",", $myrow[0]);

				for ($i = 0; $i < count($list); $i++) {
					$select = ( $list[$i] == $delay ) ? "selected=\"selected\"" : "" ;

					print("<option value=\"$list[$i]\" $select>". str_replace(":", $msg->read($CTN_HOUR), "$list[$i]") ."</option>");
					}

		print("
				</select>
			  </label>
			</td>
	      	</td>
		    </tr>
		    	");

		// saisie de l'intitulé
		if ( $error_title )
			print("
			    <tr>
			   	   <td class=\"valign-middle align-justify\" colspan=\"3\">
			   	     <span style=\"color:#FF0000;\">". $msg->read($CTN_ERRIDENT) ."</span>
			      </td>
			    </tr>
			    	");

		print("
		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_IDENT) ."
		      </td>
		      <td colspan=\"2\">
			  <label for=\"title\"><input type=\"text\" id=\"title\" name=\"title\" size=\"40\" value=\"$title\" /></label>
	      	</td>
		    </tr>
		    	");

		print("
		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_TYPE) ." :
		      </td>
		      <td colspan=\"2\">
			  <label for=\"type\">");
			  ?>
					<select name="type">
						<option value="0" <?php if($type == "0") { echo "selected"; } ?>>Contenu du cours</option>
						<option value="1" <?php if($type == "1") { echo "selected"; } ?>>A faire</option>
					</select>
			  <?php
		print("	  </label>
	      	</td>
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

		print("
		    <tr>
		      <td class=\"align-right valign-top\">
		        ". $msg->read($CTN_TEXT) ."
			  <a href=\"#\" onclick=\"popWin('".$_SESSION["ROOTDIR"]."/spip_typo.php?lang=".$_SESSION["lang"]."', '450', '350'); return false;\">
	      	  <img src=\"".$_SESSION["ROOTDIR"]."/images/spip/aide.gif\" title=\"".$msg->read($CTN_HELP)."\" alt=\"".$msg->read($CTN_HELP)."\" /></a> :<br/>
			  [$editor]
		      </td>
		      <td colspan=\"2\">
		    	");

		if ( $edit ) {
			/*$oFCKeditor           = new FCKeditor("texte") ;
			$oFCKeditor->BasePath = "./script/fckeditor/";
			$oFCKeditor->Height   = 300;
			$oFCKeditor->Value    = htmlspecialchars($texte_ckeditor);
			$oFCKeditor->Create();*/
			?>
				<textarea name="texte_ckeditor" id="texte_ckeditor"><?php echo $texte_ckeditor; ?></textarea>
				<script>
				CKEDITOR.replace('texte_ckeditor');
				</script>
			<?php
			}
		else
			print("<label for=\"texte_ckeditor\"><textarea id=\"texte_ckeditor\" name=\"texte_ckeditor\" rows=\"10\" cols=\"40\">$texte_ckeditor</textarea></label>");

		print("
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
		if ( $auth[4] == "O" ) {
		    	print("
			    <tr>
			      <td class=\"align-right valign-top\">
					". $msg->read($CTN_ATTACHED) ."
					<span style=\"cursor: pointer;\" onclick=\"$('PJ')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"+\" alt=\"+\" /></span>
			      </td>
			      <td colspan=\"2\">");

			// lecture des PJ
			$res = mysqli_query($mysql_link, "select _IDpj, _title, _ext, _size from ctn_pj where _IDitem = '$IDitem' AND _type = '0'");
			$doc = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

			if ( $doc )
				while ( $doc ) {
					// suppression la pièce jointe
					$req   = $msg->read($CTN_DELATTACH);
					$del   = ( $_SESSION["CnxAdm"] == 255 OR $row[4] == $_SESSION["CnxID"] )
						? "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&IDitem=$IDitem&IDpj=$doc[0]&IDmat=$IDmat&IDclass=$IDclass&submit=delpj")."\" onclick=\"return confirmLink(this, '$req');\">
						   <img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"$req\" alt=\"$req\" />
						   </a>"
						: "" ;

					print("
				    		<img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$doc[2].gif\" title=\"\" alt=\"\" />
				    		". $msg->read($CTN_DOCUMENT) ." $del<br/>
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
						<span style=\"cursor: pointer;\" onclick=\"$('uploadpj')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/max.gif\" title=\"+\" alt=\"+\" /></span>
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

		// Devoir Maison / controles
		$list = explode(",", $msg->read($CTN_TODO));

		//---- affichage des devoirs en PJ
		$res  = mysqli_query($mysql_link, "select _todo, _text from ctn_data where _IDitem = '$IDitem' AND _type = '1' limit 1");
		$doc  = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

		@list($todo, $todoh) = explode(" ", $doc[0]);

	    	print("
		    <tr>
		      <td class=\"align-right valign-top\">
				<strong>$list[0] :</strong>
				<span style=\"cursor: pointer;\" onclick=\"$('todo')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"+\" alt=\"+\" /></span>
		      </td>
		      <td colspan=\"2\">
		        <p style=\"margin:0px;\">
			  <label for=\"is_todo\"><input type=\"text\" id=\"is_todo\" name=\"todo\" size=\"10\" value=\"$todo\" /> ". $msg->read($CTN_AT) ."</label>
			  <label for=\"is_todoh\"><input type=\"text\" id=\"is_todoh\" name=\"todoh\" size=\"10\" value=\"$todoh\" /></label>");

		// calendrier surgissant
		CalendarPopup("id2", "document.formulaire.todo");

		print("
			  </p>
	      	</td>
		    </tr>");

		// lecture des devoirs en PJ
		$res  = mysqli_query($mysql_link, "select _IDpj, _title, _ext, _size from ctn_pj where _IDitem = '$IDitem' AND _type = '1'");
		$mypj = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

		$file = "";
		if ( $mypj )
			while ( $mypj ) {
				// suppression des pièces jointes des devoirs
				$req   = $msg->read($CTN_DELATTACH);
				$del   = ( $_SESSION["CnxAdm"] == 255 OR $row[4] == $_SESSION["CnxID"] )
					? "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&IDitem=$IDitem&IDpj=$mypj[0]&IDmat=$IDmat&IDclass=$IDclass&submit=delpj")."\" onclick=\"return confirmLink(this, '$req');\">
					   <img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"$req\" alt=\"$req\" />
					   </a>"
					: "" ;

				$file .= "
					    <img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$mypj[2].gif\" title=\"\" alt=\"\" />
					    ". $msg->read($CTN_DOCUMENT) ." $del<br/>";

				$mypj  = remove_magic_quotes(mysqli_fetch_row($res));
				}

		print("
		    <tr>
		      <td></td>
		      <td colspan=\"2\">
				<div id=\"todo\" style=\"display:none; border:#cccccc solid 1px; padding:5px;\">
					$file

					<p class=\"hidden\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" /></p>
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile1[]\" size=\"50\" style=\"font-size:9px;\" />
						<span style=\"cursor: pointer;\" onclick=\"$('UploadFile1')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/max.gif\" title=\"+\" alt=\"+\" /></span>
					</p>

					<div id=\"UploadFile1\" style=\"display:none;\">
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile1[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile1[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>
					</div>

					<label for=\"text1\"><textarea id=\"text1\" name=\"text1\" rows=\"3\" cols=\"40\">$doc[1]</textarea>
					". $msg->read($CTN_NOTES) ."</label>
				</div>
		      </td>
		    </tr>
		    ");

		//---- affichage des controles en PJ
		$res  = mysqli_query($mysql_link, "select _todo, _text from ctn_data where _IDitem = '$IDitem' AND _type = '2' limit 1");
		$doc  = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

		@list($ctrl, $ctrlh) = explode(" ", $doc[0]);

	    	print("
		    <tr>
		      <td class=\"align-right valign-top\">
				<strong>$list[1] :</strong>
				<span style=\"cursor: pointer;\" onclick=\"$('ctrl')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"+\" alt=\"+\" /></span>
		      </td>
		      <td colspan=\"2\">
		        <p style=\"margin:0px;\">
			  <label for=\"is_ctrl\"><input type=\"text\" id=\"is_ctrl\" name=\"ctrl\" size=\"10\" value=\"$ctrl\" /> ". $msg->read($CTN_AT) ."</label>
			  <label for=\"is_ctrlh\"><input type=\"text\" id=\"is_ctrlh\" name=\"ctrlh\" size=\"10\" value=\"$ctrlh\" /></label>");

		// calendrier surgissant
		CalendarPopup("id3", "document.formulaire.ctrl");

		print("
			  </p>
	      	</td>
		    </tr>");

		// lecture des controles en PJ
		$res  = mysqli_query($mysql_link, "select _IDpj, _title, _ext, _size from ctn_pj where _IDitem = '$IDitem' AND _type = '2'");
		$mypj = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;

		$file = "";
		if ( $mypj )
			while ( $mypj ) {
				// suppression des pièces jointes des devoirs
				$req   = $msg->read($CTN_DELATTACH);
				$del   = ( $_SESSION["CnxAdm"] == 255 OR $row[4] == $_SESSION["CnxID"] )
					? "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&IDitem=$IDitem&IDpj=$mypj[0]&IDmat=$IDmat&IDclass=$IDclass&submit=delpj")."\" onclick=\"return confirmLink(this, '$req');\">
					   <img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"$req\" alt=\"$req\" />
					   </a>"
					: "" ;

				$file .= "
					    <img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$mypj[2].gif\" title=\"\" alt=\"\" />
					    ". $msg->read($CTN_DOCUMENT) ." $del<br/>";

				$mypj  = remove_magic_quotes(mysqli_fetch_row($res));
				}

		print("
		    <tr>
		      <td></td>
		      <td colspan=\"2\">
				<div id=\"ctrl\" style=\"display:none; border:#cccccc solid 1px; padding:5px;\">
					$file

					<p class=\"hidden\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" /></p>
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile2[]\" size=\"50\" style=\"font-size:9px;\" />
						<span style=\"cursor: pointer;\" onclick=\"$('UploadFile2')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/max.gif\" title=\"+\" alt=\"+\" /></span>
					</p>

					<div id=\"UploadFile2\" style=\"display:none;\">
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile2[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>
					<p style=\"margin-top:0px; margin-bottom:5px;\">
						<input type=\"file\" name=\"UploadFile2[]\" size=\"50\" style=\"font-size:9px;\" />
					</p>
					</div>

					<label for=\"text2\"><textarea id=\"text2\" name=\"text2\" rows=\"3\" cols=\"40\">$doc[1]</textarea>
					". $msg->read($CTN_NOTES) ."</label>
				</div>
		      </td>
		    </tr>
		    ");

		// gestion des absences
	    	print("
		    <tr>
		      <td class=\"align-right valign-top\">
				". $msg->read($CTN_ABSENT) ."
				<span style=\"cursor: pointer;\" onclick=\"$('absent')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"+\" alt=\"+\" /></span>
		      </td>
		      <td colspan=\"2\">
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
			while ( $row ) {
				// suppression de l'absence
				$req    = $msg->read($CTN_DELABSENT);
				$delete = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $row[4] )
					? "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&IDitem=$IDitem&IDmat=$IDmat&IDclass=$IDclass&IDabsent=$row[2]&submit=del")."\" onclick=\"return confirmLink(this, '$req');\">
					   <img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"$req\" alt=\"$req\" />
					   </a>"
					: "" ;

				$index  = $row[3] - 1;

				print("$delete $row[0] $row[1] (".@$motif[1][$index].")<br/>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
		else
			print("...");

		// affichage des élèves
		$query  = "select _ID, _name, _fname from user_id ";
		$query .= "where _visible = 'O' AND _IDclass = '$IDclass' AND _IDgrp = '1' ";
		$query .= "order by _name, _fname";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		print("
			<div id=\"absent\" style=\"display:none; border:#cccccc solid 1px; padding:5px;\">
				<table>");

		$isok   = ( $copy ) ? "disabled=\"disabled\"" : "" ;

		while ( $row ) {
			print("<tr><td><label for=\"IDeleve_$row[0]\"><input type=\"checkbox\" id=\"IDeleve_$row[0]\" name=\"IDeleve[]\" value=\"$row[0]\" $isok /></label> $row[1] $row[2]</td>");
			print("<td> ".$msg->read($CTN_MOTIF)." ");

			print("<label for=\"IDmotif_$row[0]\">");
			print("<select id=\"IDmotif_$row[0]\" name=\"IDmotif_$row[0]\">");

			for ($i = 0; $i < count($motif[0]); $i++)
				print("<option value=\"".$motif[0][$i]."\">".$motif[1][$i]."</option>");

			print("</select>");
			print("</label></td></tr>");

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

	    	print("
				  <tr>
				    <td colspan=\"3\">
					<p style=\"margin-top:5px; margin-bottom:0px;\">
						<label for=\"note\"><textarea id=\"note\" name=\"note\" rows=\"3\" cols=\"40\">$note</textarea>
						". $msg->read($CTN_NOTES) ."
						</label>
					</p>
				    </td>
				  </tr>
	      		</table>
	      	  </div>
	      	</td>
		    </tr>
		  </table>
		    	");

	    	print("
			<hr style=\"width:80%;\" />

		         <table class=\"width100\">
		           <tr>
		              <td style=\"width:10%;\" class=\"valign-middle align-center\">
		              	<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CTN_INPUTOK)."\" />
		              </td>
		              <td class= \"valign-middle\">". $msg->read($CTN_VALIDINPUT) ."</td>
		           </tr>
		           <tr>
		              <td class=\"valign-middle align-center\">
		              	<a href=\"$retour\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($CTN_INPUTCANCEL)."\" /></a>
		              </td>
		              <td class= \"valign-middle\">". $msg->read($CTN_VISUALIZE) ."</td>
		           </tr>
		         </table>

			</form>
			");
		}
?>

</div>
