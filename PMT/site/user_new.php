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
 *		module   : user_new.php
 *		projet   : la page de création/modification d'un compte utilisateur
 *
 *		version  : 1.2
 *		auteur   : laporte
 *		creation : 16/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *		           7/01/07 - D. Laporte
 * 	                 durée d'inscription limitée
 */

//!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
 if ( is_file($_SESSION["ROOTDIR"]."/msg/mail.php") )
   require "msg/mail.php";
 $msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
 $msg_mail->msg_mail_search  = $keywords_search;
 $msg_mail->msg_mail_replace = $keywords_replace;

$ID       = ( @$_POST["ID"] )			// ID de l'utilisateur
	? (int) $_POST["ID"]
	: (int) @$_GET["ID"] ;
$Litem      = @$_POST["Litem"]			// Liste d'origine
	? (int) $_POST["Litem"]
	: (int) @$_GET["Litem"] ;
$IDeleve  = ( @$_POST["IDeleve"] )		// ID de l'élève
	? (int) $_POST["IDeleve"]
	: (int) @$_GET["IDeleve"] ;
$Litem      = @$_POST["Litem"]		// liste d'origine
	? (int) $_POST["Litem"]
	: (int) @$_GET["Litem"] ;
$authuser = ( @$_POST["authuser"] )		// mode validation de compte
	? (int) $_POST["authuser"]
	: (int) @$_GET["authuser"] ;
$visu     = ( @$_POST["visu"] )		// mode de visualisation
	? (int) $_POST["visu"]
	: (int) @$_GET["visu"] ;
$sort     = ( @$_POST["sort"] )		// mode de tri
	? (int) $_POST["sort"]
	: (int) @$_GET["sort"] ;
$IDsel  = ( @$_POST["IDsel"] )		// groupe utilisateur
	? $_POST["IDsel"]
	: @$_GET["IDsel"] ;
$IDalpha  = ( @$_POST["IDalpha"] )		// ordre alphabétique
	? $_POST["IDalpha"]
	: @$_GET["IDalpha"] ;
$more     = ( @$_POST["more"] )		// mode centre annexes
	? (bool) $_POST["more"]
	: (bool) @$_GET["more"] ;

$name     = ucwords(strtolower(trim(@$_POST["name"])));
$fname    = ucwords(strtolower(trim(@$_POST["fname"])));
$code     = trim(@$_POST["code"]);
$IDcentre = (int) @$_POST["IDcentre"];
$IDgrp    = @$_POST["IDgrp"];
$ident    = trim(@$_POST["ident"]);
$pwd      = trim(@$_POST["pwd"]);
$email    = trim(@$_POST["email"]);
$sexe     = @$_POST["sexe"];
$titre    = trim(@$_POST["titre"]);
$fonction = trim(@$_POST["fonction"]);
$cb       = @$_POST["cb"];
$cbox     = @$_POST["cbox"];
$rb       = @$_POST["rb"];
$delay    = ( @$_POST["delay"] ) ? $_POST["delay"] : $ACOUNTIME ;
$mylang   = ( @$_POST["mylang"] ) ? $_POST["mylang"] : $_SESSION["lang"] ;
$born     = @$_POST["born"];
$adr1     = addslashes(trim(@$_POST["adr1"]));
$adr2     = addslashes(trim(@$_POST["adr2"]));
$cp       = trim(@$_POST["cp"]);
$ville    = addslashes(trim(@$_POST["ville"]));
$tel      = trim(@$_POST["tel"]);
$mobile      = trim(@$_POST["mobile"]);

$submit   = ( @$_POST["valid_x"] )		// bouton de validation
	? "Valider"
	: "" ;

if ((substr(@$_POST["IDgrp"],0,9) == "grchange ") && ( $ID == 0))
	{
	$newpage = "index.php?item=38&cmde=account&IDcentre=1&visu=O&IDclass=".substr(@$_POST["IDgrp"],9);
	echo "<SCRIPT language=javascript>document.write(window.location.href='$newpage');</SCRIPT>";
	}

if ($born != "") {
	$tabDate = explode('/' , $born);
	$born  = $tabDate[2].'-'.$tabDate[1].'-'.$tabDate[0];
	}


?>


<?php
//---------------------------------------------------------------------------
function isRegistered($ident)
{
/*
 * fonction :	test si un user ID est enregistré
 * in :			$ident, le user ID
 * out :		false si pas enregistré, true sinon
 */
	require "globals.php";

	$query  = "select _ID from user_id ";
	$query .= "where _ident = '$ident' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);

	return ( $result )
		? (bool) mysqli_affected_rows($mysql_link)
		: false ;
}
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


<?php
	// initialisation
	$warning = "";
	$mysel   = $mycentre = 0;
	$errcat  = $errname = $errident = $erremail = "";
	$statut  = ( $ID ) ? $msg->read($USER_MODIFICATION) : $msg->read($USER_NEWRECORD) ;

	// traitement commande
	if ( $submit == "Valider" )
	{
		// Le traitement des champs perso se fait plus tard
		// // Traitement champs perso
		// foreach($_POST as $key => $val)
		// {
		// 	if(strpos($key, "rubperso_") !== false)
		// 	{
		// 		// Cette appel ne fonctionne pas car on a pas encore l'id de l'utilisateur
		// 		setRubriqueVal("user", intval(substr($key, strpos($key, "rubperso_")+9)), $val, $ID, $_SESSION["lang"]);
		// 	}
		// }

		// vérification saisie
		// $errcat   = ( $IDgrp == 0 ) ? $msg->read($USER_ERRINPUT) : "" ;
		$errname  = ( $name == "" ) ? $msg->read($USER_ERRINPUT) : "" ;
		$errident = ( $ident == "" ) ? $msg->read($USER_ERRINPUT) : "" ;
		$erremail = ( $email != "" AND !isValidEmail($email) )
			? $msg->read($USER_BADEMAIL)
			: "" ;

  	// pour éviter les injections SQL
  	$pwd      = str_replace(" ", "-", trim($pwd));
	//
		$fonction = str_replace("\n", "<br/>", $fonction);

		// permet à l'administrateur de valider les comptes ultérieurement lors de la création par les utilisateurs
		// sauf si validation automatique autorisée (attention à la suppression automatique des comptes)
		$date     = ( @$_SESSION["CnxAdm"] == 255 OR $AUTOVAL ) ? date("Y-m-d H:i:s") : "" ;

		// les comptes créés par les utilisateurs doivent être validés par l'administrateur
		// sauf si validation automatique autorisée
		// $valid    = ( @$_SESSION["CnxAdm"] == 255 OR $AUTOVAL ) ? 1 : 0 ;

		// Les nouveaux comptes doivent valider leurs mails
		$valid = -1;

		// création de compte par les utilisateurs authorisée ?
		list($iscreat, $nil) = explode(":", $AUTHUSER);

		if ( @$_SESSION["CnxAdm"] == 255 OR $iscreat ) {
			// attention aux id purement numérique
			if ( is_numeric(substr($name, 0, 1)) )
				$name = "X" . $name;

			$name     = addslashes($name);
			$fname    = addslashes($fname);
			$code    = addslashes($code);
			$ident    = addslashes($ident);
			$pwd      = addslashes($pwd);
			$titre    = addslashes($titre);
			$fonction = addslashes($fonction);
			$pwd      = ( $ID AND @$_POST["newpwd"] == $msg->read($USER_GENERATE) AND strlen($AUTOPASSWD) )
				? getUserPassword($ID)
				: $pwd ;

			// les autres centres affectés
			$centre   = 0;
			for ($i = 0; $i < count($cb); $i++)
				$centre += ( @$cb[$i] )  ? @$cb[$i]  : 0 ;

			// les matières enseignées
			$idmat    = " ";
			for ($i = 0; $i < count($cbox); $i++)
				$idmat .= ( @$cbox[$i] )  ? "$cbox[$i] " : "" ;

			$go_prof = false;
			$go_student = false;
			// classe de l'élève
			//////////////////////////////////////////////////////////////////////////////////////////



			// classID
			//
			// if (strpos($IDgrp, "grchange ") !== false)
			// {
			// 	$IDclass = str_replace("grchange ", "", $IDgrp);
      //
			// 	$tab_grchange = explode("-", $IDclass);
			// 	$IDgrp   = $tab_grchange[0];
			// 	$IDclass   = $tab_grchange[1];
			// 	$go_student = true;
			// }
			// else
			// {
			// 	$IDclass = 0;
			// 	$go_prof = true;
			// }

      $IDclass = $_POST['classID'];
      if ($IDclass == "") $IDclass = 0;

if ($_POST['selectType'] == "user_new_eleve") $IDgrp = 1;
elseif ($_POST['selectType'] == "user_new_formateur") $IDgrp = 2;
if ($IDgrp != 2 && $IDgrp != 1) $IDgrp = 1;



			// seul l'administrateur peut modifier un compte
			if ( @$_SESSION["CnxAdm"] == 255 AND $ID ) {
				$Query  = "update user_id ";
				if($pwd == "*****")
				{
					$Query .= "set _name = '$name', _fname = '$fname', _ident = '$ident', _IDcentre = '$IDcentre', _IDgrp = '$IDgrp', _lang = '$mylang', ";
          if (!$IDclass) $Query .= "_IDclass = NULL, ";
          else $Query .= "_IDclass = '$IDclass', ";
				}
				else
				{
					$Query .= "set _name = '$name', _fname = '$fname', _ident = '$ident', _passwd = '".md5($pwd)."', _IDcentre = '$IDcentre', _IDgrp = '$IDgrp', _lang = '$mylang', ";
          if (!$IDclass) $Query .= "_IDclass = NULL, ";
          else $Query .= "_IDclass = '$IDclass', ";
				}
				$Query .= "_sexe = '$sexe', _title = '$titre', _fonction = '$fonction', _email = '$email', _chs = '$rb', _delay = NULL, _centre = '$centre', _IDmat = '$idmat', _code = '$code', ";
				$Query .= "_born= '$born', _adr1 = '$adr1', _adr2 = '$adr2', _cp = '$cp', _city = '$ville', _tel = '$tel', _mobile = '$mobile' ";
				$Query .= "where _ID = '$ID' ";
				$Query .= "limit 1";
			}
			else {
				$Query  = "insert into user_id ";
				$Query .= "values(NULL, '$IDgrp', '$IDcentre', ";
        if ($IDclass == 0) $Query .= "NULL, ";
        else $Query .= "'$IDclass', ";
        $Query .= "'', NOW(), NOW(), NOW(), '$valid', '$ident', '".md5($pwd)."', '$name', '$fname', '$sexe', '$titre', '$fonction', '$email', '', '', '', '$born', '', '', '', '', '', '0', '0', '0', '0', '0', 'N', 'N', 'E', 'N', 'N', 'O', NULL, '$centre', '$idmat', '$mylang', '0', '0', '$code')";
			}


		    	// on teste si l'utilisateur est déjà enregistré
			if( !$ID AND isRegistered($ident) )
				$errident = $msg->read($USER_BADID);

			// if ( $errcat == "" AND $errname == "" AND $errident == "" AND $erremail == "" ) {
			if ( $errname == "" AND $errident == "" AND $erremail == "" ) {
				$retcode = mysqli_query($mysql_link, $Query);

				$statut .= ( $retcode )
					? " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />"
					: " <img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />" . sql_error($mysql_link);

				$id = ( $ID ) ? $ID : mysqli_insert_id($mysql_link) ;

				// echo "<br>Query: ".$Query;
				// echo "<br>ID: ".$id;


				// Traitement champs perso
				foreach($_POST as $key => $val)
				{
					if(strpos($key, "rubperso_") !== false)
					{
						setRubriqueVal("user", intval(substr($key, strpos($key, "rubperso_")+9)), $val, $id, $_SESSION["lang"]);
					}
				}

				// Création du lien de validation envoyé à l'email de l'utilisateur:
				$url =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

				$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
				$exploded_url = explode("index.php", $escaped_url);
				// echo $exploded_url[0];

				// $encryptionKey = getParam("encryptionKeyForConfirmationMail");



				$mail_url = $exploded_url[0]."index.php?item=1100&account=".crypt($id, "FollowTheWhiteRabbitNeo");




				// l'admin valide directement, sinon en attente
				$isvalid = ( $valid ) ? "O" : "A" ;

				// fichier à transférer
				$file = @$_FILES["UploadedFile"]["tmp_name"];

				if ( $file AND $retcode ) {
					require_once "include/gallery.php";

					$dest = ( getAccess($IDsel) == 1 ) ? "$DOWNLOAD/photo/eleves" : "$DOWNLOAD/photo" ;

					// création de la vignette
					vignette("$file|".@$_FILES["UploadedFile"]["name"], $dest, "$id.gif", $srcWidth, $srcHeight);
					}

				// si c'est ok, on affiche un message pour les utilisateurs qui ont créé un compte
				if ( @$_SESSION["CnxAdm"] != 255 AND strstr($statut, "ok.gif") ) {
					require_once "lib/libmail.php";

					$name     = stripslashes($name);
					$fname    = stripslashes($fname);
					$code    = stripslashes($code);
					$ident    = stripslashes($ident);
					$pwd      = stripslashes($pwd);

					// Au lieux d'afficher le mot de passe en clair, on génère une chaine de "*" de la longueur du mot de passe saisi par l'utilisateur.
					$passwordToShow = "";
					for ($i=0; $i < strlen($pwd); $i++) {
						$passwordToShow .= "*";
					}

					$warning  = $msg->read($USER_CREATACCOUNT, Array("$fname $name", $ident, $passwordToShow)) . "<br/><br/>";
					$warning .= ( $AUTOVAL )
						? (strlen($email) ? $msg->read($USER_AUTOVAL) . "<br/>" : "")
						: $msg->read($USER_WAITOPEN) . "<br/>" ;
					$warning .= ( $AUTODEL )
						? $msg->read($USER_AUTODEL, strval($AUTODEL))
						: "" ;
					$warning .= "<br/><br/>";
					$warning .= $msg->read($USER_THANX);

					// envoi d'un email aux utilisateurs
					if ( isValidEmail($email) ) {
						$mymail = new Mail(); // create the mail

						// corps du message
						$link = "<a href=\"".$mail_url."\">cliquez ici</a>";
						$texte  = $msg->read($USER_BODY, $link);
						$texte .= "<br>--<br>";
						$texte .= "<b>Merci de ne pas répondre à ce mail</b><br>";
						$texte .= $_SESSION["CfgAdr"] . "<br>";
						$texte .= $_SESSION["CfgWeb"];

						// J'ai refais la fonction mail car le lien en html (dans le corps du message) n'était pas interprété
						$to  = $email;
						$subject = $msg->read($USER_SENDPWD, $_SESSION["CfgWeb"]);

						// Headers
						$headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
						$headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
			      $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
			      $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);

						mail($to, $subject, $texte, $headers);
					}


					// envoi d'un email à l'administrateur
					if ( isValidEmail($_SESSION["CfgAdmin"]) && getParam("adminRecieveMailWhenAccountCreated") ) {
						$mymail = new Mail(); // create the mail

						// corps du message
						$texte  = $msg->read($USER_AWAITBODYTEXT, "$fname $name");
						$texte .= "\n--\n";
						$texte .= $_SESSION["CfgAdr"] . "\n";
						$texte .= $_SESSION["CfgWeb"];

						$mymail->From("noreply@".$_SESSION["CfgWeb"]);
						$mymail->To($_SESSION["CfgAdmin"]);
						$mymail->Subject($msg->read($USER_AWAITING, $_SESSION["CfgIdent"]));
						$mymail->Body($texte, $CHARSET);	// set the body

						$mymail->Send();	// send the mail
						}
					}

				// raz champs de saisie
				if ( $ID == 0 AND $retcode )
					$name = $fname = $ident = $pwd = $email = $titre = $fonction = "";
				}
			}
		}





	// accès réservé au Big chef
	if ( $_SESSION["CnxAdm"] == 255 )
		// lecture du compte
		if ( $ID OR $IDeleve ) {
			// mise à jour du compte de connexion
			$query  = "select _IDcentre, _IDgrp, _name, _fname, _ident, _passwd, _email, _sexe, _title, _fonction, _chs, _delay, _centre, _IDmat, _lang, _IDclass, _code, _born, _adr1, _adr2, _cp, _city, _tel, _mobile ";
			$query .= "from user_id ";
			$query .= ( $ID ) ? "where _ID = '$ID' " : "where _ID = '$IDeleve' " ;
			$query .= "limit 1";

			$result = mysqli_query($mysql_link, $query);
			$myrow  = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			// lectures de données à modifier
			$mycentre  = (int) $myrow[0];
			$mysel     = ( getAccess($myrow[1]) == 1 ) ? -(($myrow[1] * 100) + $myrow[15]) : $myrow[1] ;
			$name      = $myrow[2];
			$fname     = $myrow[3];
			$ident     = $myrow[4];
			$pwd       = $myrow[5];
			$email     = $myrow[6];
			$sexe      = $myrow[7];
			$titre     = $myrow[8];
			$fonction  = str_replace("<br/>", "\n", $myrow[9]);
			$rb        = $myrow[10];
			$delay     = $myrow[11];
			$centre    = $myrow[12];
			$IDmat     = $myrow[13];
			$mylang    = ( $myrow ) ? $myrow[14] : $_SESSION["lang"] ;
			$code	   = $myrow[16];
			$born	   = $myrow[17];
			$adr1	   = $myrow[18];
			$adr2	   = $myrow[19];
			$cp		   = $myrow[20];
			$ville	   = $myrow[21];
			$tel	   = $myrow[22];
			$mobile	   = $myrow[23];
			}

	// on vérifie si la photo existe
	$path  = ( getAccess($IDsel) == 1 ) ? "$DOWNLOAD/photo/eleves/$ID.gif" : "$DOWNLOAD/photo/$ID.gif" ;
	$photo = ( file_exists($path) )
		? $path
		: $_SESSION["ROOTDIR"]."/css/themes/".$_SESSION["CfgTheme"]."/images/0.gif" ;

	// Redirection si transformation fiche vers étudiant
	if ($go_student)
	{
		echo "<script language=javascript>document.write(window.location.href='index.php?item=38&cmde=account&ID=$ID&Litem=$Litem');</script>";
	}





	function inputText($message, $for, $id, $name, $size, $value, $toShowToClass, $isRequired)
	{
		$advert = "";
		if ($isRequired != "none")
		{
			$advert = "<span style=\"color: #FF0000;\">*</span>";
			$requiredValue = "required";
		}
		else
		{
			$advert = "<span></span>";
			$requiredValue = "";
		}
		if (strpos($isRequired, 'required-password') !== false)
		{
			$type = "password";
		}
		elseif (strpos($isRequired, 'required-email') !== false)
		{
			$type = "email";
		}
		else $type = "text";

		return "
			<tr class=\"".$toShowToClass."\">
				<td class=\"align-right valign-middle\">". $message ."</td>
				<td>
					<label for=\"".$for."\">
						<input type=\"".$type."\" id=\"".$id."\" name=\"".$name."\" size=\"".$size."\" value=\"".$value."\" ".$requiredValue." />
					</label>
					".$advert."
				</td>
			</tr>
		";
	}

	function inputTextArea($message, $for, $numRow, $id, $name, $col, $value, $toShowToClass)
	{
		return "
			<tr class=\"".$toShowToClass."\">
				<td class=\"align-right valign-top\">". $message ."</td>
				<td>
					<label for=\"".$for."\">
						<textarea rows=\"".$numRow."\" id=\"".$id."\" name=\"".$name."\" cols=\"".$col."\">$value</textarea>
					</label>
				</td>
			</tr>
		";
	}

// Dans la page on affichera certains champs rubrique à certains endroits, cette variable permet de stoquer l'ID des champs déjà affichés pour ne pas les mettre deux fois
$alreadyShownRubrique = [];

function displayOneRubriqueTableElement($tab_champs, $class, $alreadyShownRubrique)
{
	print ("<tr style=\"height: 10px;\"><td></td><td></td></tr>");

	foreach($tab_champs as $key => $val)
	{
		array_push($alreadyShownRubrique, $key);

		echo "<tr class=\"".$class."\"><td class=\"align-right valign-middle\">";
		echo "<strong>".$val["rubrique"]." :</strong><br />";
		echo "</td><td>";
		echo "<label for=\"rubperso_$key\">";

		if($val["type"] == "liste")
		{
			echo "<select name=\"rubperso_$key\">";
			echo "<option value=\"\"></option>";
		}

		foreach($val["valeur"] as $key2 => $val2)
		{
			switch ($val["type"]) {
				case "chaine":
					if($val["valeur"][0] != "")
					{
						echo "<input type=\"text\" value=\"$val2\" name=\"rubperso_$key\" />";
					}
					else
					{
						echo "<input type=\"text\" value=\"".$val["default"]."\" name=\"rubperso_$key\" />";
					}
					break;
				case "liste":
					if($val["selected"] != "")
					{
						$selected = ( $val["selected"] == $key2 ) ? "selected=\"selected\"" : "" ;
					}
					else
					{
						$selected = ( $val["default"] == $key2 ) ? "selected=\"selected\"" : "" ;
					}
					echo "<option value=\"$key2\" $selected>$val2</option>";
					break;
			}
		}

		if($val["type"] == "liste") echo "</select>";

		echo "</label>";
		echo "</td></tr>";
	}

	return $alreadyShownRubrique;
}




?>

<script src="https://www.google.com/recaptcha/api.js?render=6LfuNY8UAAAAANWrIjOMV48xZSQTq0xQi-Iq8z3f"></script>
  <script>
  grecaptcha.ready(function() {
      grecaptcha.execute('6LfuNY8UAAAAANWrIjOMV48xZSQTq0xQi-Iq8z3f', {action: 'homepage'}).then(function(token) {
         ...
      });
  });
  </script>






<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($USER_USERACCOUNT)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="index.php" method="post" enctype="multipart/form-data">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"visu\"     value=\"$visu\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"ID\"       value=\"$ID\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDeleve\"  value=\"$IDeleve\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDsel\"    value=\"$IDsel\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"authuser\" value=\"$authuser\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"more\"     value=\"$more\" /></p>
			");
	?>

            <!-- <table class="width100">
              <tr>
                <td style="width:17%;" class="valign-top align-right"> -->
									<?php //print($msg->read($USER_STATUS)); ?>
                <!-- </td>
                <td style="width:83%;" class="valign-top"> -->
									<?php //print("$statut"); ?>
                <!-- </td>
              </tr> -->

              <!-- <tr>
                <td class="valign-top"> -->
									<?php // print("<img src=\"$photo\" title=\"$photo\" alt=\"$photo\" />"); ?>
                <!-- </td>
                <td class="valign-top" style="border:#cccccc solid 1px; padding:4px;"> -->
<style>
.valign-middle {
	width:5%;
}
</style>
				<table class="width100">

				<?php
      	            	if ( $warning != "" )
						print("
			                      <tr>
			                        <td class=\"align-center\" colspan=\"2\">
								$warning
							</td>
			                      </tr>");
					else {
						print("
			                    <tr style=\"display: none\">
			                      <td style=\"width:25%;\" class=\"align-right valign-middle\">". $msg->read($USER_CENTER) ."</td>
			                      <td style=\"width:75%;\" class=\"valign-middle\">
							<label for=\"IDcentre\">
							<select id=\"IDcentre\" name=\"IDcentre\" onchange=\"document.forms.formulaire.submit()\">");

						// lecture des centres constitutifs
						$query  = "select _IDcentre, _ident from config_centre ";
						$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
						$query .= "order by _ident";

						$result = mysqli_query($mysql_link, $query);
						$nbrow  = ( $result ) ? mysqli_affected_rows($mysql_link) : 0 ;
						$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

						// initialisation
						if ( !$IDcentre )
							$IDcentre = (int) ($mycentre ? $mycentre : $row[0]) ;

						while ( $row ) {
							if ( $IDcentre == $row[0] )
								print("<option selected=\"selected\" value=\"$row[0]\">$row[1]</option>");
							else
								print("<option value=\"$row[0]\">$row[1]</option>");

							$row = remove_magic_quotes(mysqli_fetch_row($result));
							}

						$more = ( $nbrow > 1 )
							? "<span style=\"cursor: pointer;\" onclick=\"$('centre')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"". $msg->read($USER_MORE) ."\" alt=\"". $msg->read($USER_MORE) ."\" /></span>"
							: "<img src=\"".$_SESSION["ROOTDIR"]."/images/home.gif\" title=\"\" alt=\"\" />" ;

						print("
							</select> $more
							</label>
			                      </td>
			                    </tr>");

						// affichage de tous les centres constitutifs pour détachement
						if ( $nbrow > 1 ) {
							mysqli_data_seek($result, 0);
							$row = remove_magic_quotes(mysqli_fetch_row($result));

							print("
					                    <tr>
					                      <td></td>
					                      <td class=\"valign-top\">
									<div id=\"centre\" style=\"display:none;\">");

							while ( $row ) {
								$check = ( @$centre & pow(2, $row[0] - 1) ) ? "checked=\"checked\"" : "" ;

								print("<label for=\"cb_$row[0]\"><input type=\"checkbox\" id=\"cb_$row[0]\" name=\"cb[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /></label> $row[1]<br/>");

								$row = remove_magic_quotes(mysqli_fetch_row($result));
								}

							print("
									</div>
					                      </td>
					                    </tr>");
							}

	      	            	if ( $errcat != "" )
	                  			print("
				                    <tr>
		          					<td class=\"align-justify valign-middle\" colspan=\"2\">
									<span style=\"color:#FF0000;\">$errcat</span>
	          						</td>
			                  	  </tr>");

						//$onchangegrp = ($ID != 0) ? "" : "onchange=\"document.forms.formulaire.submit()\"";
						//$onchangegrp = "onchange=\"showElementSpecific()\"";
						?>



						<tr class="selectType">
							<td class="align-right valign-middle">
								<strong>Type de compte:</strong>
							</td>
							<td class="valign-middle">
									<select class="" name="selectType" id="selectType" onchange="showElementSpecific()">
										<option value="" disabled selected><?php echo $msg->read($USER_SELECT_ACCOUNT_TYPE); ?></option>
										<option value="user_new_eleve"><?php echo $msg->read($USER_STUDENT); ?></option>
										<option value="user_new_formateur"><?php echo $msg->read($USER_TEACHER); ?></option>
									</select>
								</label>
							</td>
						</tr>




						<?php
							// Champ NOM
							echo inputText($msg->read($USER_MYNAME), "name", "name", "name", "40", $name, "user_new", "required");

							// Champ PRENOM
							echo inputText($msg->read($USER_FNAME), "fname", "fname", "fname", "40", $fname, "user_new", "required");
						?>


						<tr class="user_new error_field">
							<td></td>
							<td class="align-middle valign-middle" id="errorUserName" style="display: none;">
								<span style="color:#FF0000;"><strong>Erreur: </strong>Ce nom d'utilisateur à déjà été utilisé</span>
							</td>
						</tr>



						<?php
							echo inputText("<strong>".$msg->read($USER_USERID_NO_MAIL)." :</strong>", "ident", "ident", "ident", "20", $ident, "user_new", "required");
						?>


						<tr class="user_new error_field">
							<td></td>
							<td class="align-middle valign-middle" id="errorMail" style="display: none;">
								<span style="color:#FF0000;"><strong>Erreur: </strong>Cette adresse mail est déjà utilisée</span>
							</td>
						</tr>


						<?php
							echo inputText($msg->read($USER_EMAIL), "email", "email", "email", "40", $email, "user_new", "required-email");
							echo inputText("<strong>".$msg->read($USER_PASSWORD)." :</strong>", "pwd", "pwd", "pwd", "20", $email, "user_new", "required-password");
						?>

						<?php
							// $civilite_champ = displayOneRubriqueTable("user_new", 3, $ID, $_SESSION["lang"]);
							// $alreadyShownRubrique = displayOneRubriqueTableElement($civilite_champ, "user_new", $alreadyShownRubrique);
						?>

						<!-- Champ CLASSE -->
						<tr class="user_new_eleve">
								<!-- <label for="classID" class="user_new_eleve">Classe: -->

								<td class= "align-right valign-middle"><strong><?php echo $msg->read($USER_CLASS); ?> : </strong></td>
								<td class="valign-middle">
									<select name="classID" id="classID">
										<option value="" disabled selected><?php echo $msg->read($USER_PROMOTION); ?></option>
										<?php
										$query = "SELECT * FROM `campus_classe` WHERE 1 ";
										$result = mysqli_query($mysql_link, $query);

										while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
											echo "<option value=".$row[0].">".$row[5]."</option>";
										}
										?>
									</select>
								<!-- </label> -->

							</td>
						</tr>


						<?php
						print("
			                    <tr class=\"\" style=\"display: none;\">
			                      <td class=\"align-right valign-middle\">". $msg->read($USER_GROUP) ."</td>
			                      <td class= \"valign-middle\">
							<label for=\"IDgrp\">
							<select id=\"IDgrp\" name=\"IDgrp\" $onchangegrp >
								<option value=\"0\">". $msg->read($USER_CHOOSECATEGORY) ."</option>");
						print("
							</select> $more
							</label> <span style=\"color:#FF0000;\">". $msg->read($USER_MANDATORY) ."</span>
			                      </td>
			                    </tr>");




						// affichage des matières enseignées
						// if ( getAccess($IDsel) == 2 ) {
							// recherche des groupes
							$query  = "select _IDmat, _titre from campus_data ";
							$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
							$query .= "order by _titre asc";

							$result = mysqli_query($mysql_link, $query);
							$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

// <tr class=\"user_new_formateur\">
              echo "
								<tr style=\"display: none;\">
									<td></td>
									<td class=\"valign-top\">
										<div id=\"subject\" style=\"\">
											<fieldset style=\"width:80%; border:#cccccc solid 1px;\">
											<legend>". $msg->read($USER_SUBJECTS) ."</legend>
											<table class=\"width100\">
										";

							$i = 0;
							while ( $row ) {
								if ( $i++ % 2 == 0 )
									print("<tr>");

								$check = ( strstr(@$IDmat, " $row[0] ") ) ? "checked=\"checked\"" : "" ;

								echo"
												<td style=\"width:50%;\">
													<label for=\"cbox_$i\"><input type=\"checkbox\" id=\"cbox_$i\" name=\"cbox[]\" value=\"$row[0]\" $check /></label> $row[1]
												</td>
										";

								if ( $i % 2 == 0 )
									echo "
								</tr>
									";

								$row = remove_magic_quotes(mysqli_fetch_row($result));
								}

							if ( $i % 2 )
								print("
								  <td></td>
								</tr>
									");

							print("
											</table>
									  	</fieldset>
										</div>
                	</td>
								</tr>");
							// }



						print("
			                    <tr class=\"user_new\">
			                      <td class=\"align-right valign-middle\">". $msg->read($USER_SEX) ."</td>
			                      <td class= \"valign-middle\">
							<label for=\"sexe\">
							<select id=\"sexe\" name=\"sexe\">");

						$sex[0][1] = "H"; $sex[1][1] = $msg->read($USER_MALE);
						$sex[0][2] = "F"; $sex[1][2] = $msg->read($USER_FEMALE);
						// $sex[0][0] = "A"; $sex[1][0] = $msg->read($USER_ANONYMOUS);
						$sex[0][0] = "A"; $sex[1][0] = $msg->read($USER_SELECT_YOUR_CIVILITY);

						if($sexe == "")
						{
							$sexe = $sex[0][0];
						}

						for ($i = 0; $i < count($sex[0]); $i++)
						{
							if ( $sexe == $sex[0][$i] ) print("<option selected=\"selected\" value=\"".$sex[0][$i]."\">".$sex[1][$i]."</option>");
							else print("<option value=\"".$sex[0][$i]."\">".$sex[1][$i]."</option>");
						}
						print("
							</select>
							</label>
			                      </td>
			                    </tr>");








						// print("
						// 		<tr class=\"user_new\">
			      //                 <td class=\"align-right valign-middle\">
			      //                   ". $msg->read($USER_CODE) ."
			      //                 </td>
			      //                 <td class= \"valign-middle\">
						// 	<label for=\"code\"><input type=\"text\" id=\"code\" name=\"code\" size=\"40\" value=\"$code\" /></label>
			      //                 </td>
			      //               </tr>");

						$rbox  = "";
						$list  = Array();

						// ouverture du répertoire des langues
						$myDir = @opendir("msg");

						// lecture des répertoires
						while ( $entry = @readdir($myDir) )
							if ( is_dir("msg/$entry") AND strlen($entry) == 2 AND $entry != ".." )
								array_push($list, $entry);

						// fermeture du répertoire
						@closedir($myDir);

						for ($i = 0; $i < count($list); $i++) {
							$check = ( $list[$i] == $mylang ) ? "checked=\"checked\"" : "" ;

							$rbox .= "<label for=\"mylang_$i\"><input type=\"radio\" id=\"mylang_$i\" name=\"mylang\" value=\"$list[$i]\" $check /></label> ";
							$rbox .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/ico-".$list[$i].".png\" title=\"".$list[$i]."\" alt=\"".$list[$i]."\" /> ";
							}

						//Traitement de la date de naissance
						if ($born != "") {
						$tabDate = explode('-' , $born);
						$born  = $tabDate[2].'/'.$tabDate[1].'/'.$tabDate[0];
						}

						/*print("
				                    <tr class=\"user_new\">
				                      <td class=\"align-right valign-middle\">". $msg->read($USER_LANG) ."</td>
				                      <td class= \"valign-middle\">$rbox</td>
				                    </tr>");*/

									?>
									<tr class="user_new">
									  <td class="align-right valign-middle">
										<?php print($msg->read($USER_BORN)); ?>
									  </td>
									  <td class="valign-middle">

											<label for="born"><input class="js-date" type="text" id="born" name="born" size="12" value="<?php echo $born; ?>" required /></label>&nbsp;<span style="color: #FF0000;">*</span>&nbsp jj/mm/aaaa

									  </td>
									</tr>

									<?php
										$bornPlace = displayOneRubriqueTable("user_new", 7, $ID, $_SESSION["lang"]);
										$alreadyShownRubrique = displayOneRubriqueTableElement($bornPlace, "user_new", $alreadyShownRubrique);
									?>





								  <!-- <tr>
									<td class="align-right valign-middle"><?php print($msg->read($USER_ADDRESS)); ?></td>
									<td> -->
									  <?php // print("<label for=\"adr1\"><input type=\"text\" id=\"adr1\" name=\"adr1\" size=\"40\" value=\"$adr1\" /></label>"); ?>
									<!-- </td>
								  </tr>

								  <tr>
									<td></td>
									<td> -->
									  <?php // print("<label for=\"adr2\"><input type=\"text\" id=\"adr2\" name=\"adr2\" size=\"40\" value=\"$adr2\" /></label>"); ?>
									<!-- </td>
								  </tr> -->

									<?php
									echo inputText($msg->read($USER_ADDRESS), "adr1", "adr1", "adr1", "40", $adr1, "user_new", "none");
									echo inputText("", "adr2", "adr2", "adr2", "40", $adr2, "user_new", "none");
									?>

								  <tr class="user_new">
									<td class="align-right valign-middle"><?php print($msg->read($USER_CITY)); ?></td>
									<td>
									  <?php print("<label for=\"cp\"><input type=\"text\" id=\"cp\" name=\"cp\" size=\"5\" value=\"$cp\" /></label>"); ?> -
									  <?php print("<label for=\"ville\"><input type=\"text\" id=\"ville\" name=\"ville\" size=\"27\" value=\"$ville\" /></label>"); ?>
									</td>
								  </tr>


								  <?php
									// echo inputText("CP", "cp", "cp", "cp", "5", $cp, "user_new");
									// echo inputText("Ville", "ville", "ville", "ville", "27", $ville, "user_new");
									echo inputText($msg->read($USER_PHONE), "tel", "tel", "tel", "20", $tel, "user_new", "none");
									echo inputText($msg->read($USER_MOBILE), "mobile", "mobile", "mobile", "20", $mobile, "user_new", "none");



									// echo inputTextArea($msg->read($USER_TITLE), "titre", "5", "titre", "titre", "40", $titre, "user_new");
									// echo inputTextArea($msg->read($USER_FUNCTION), "fonction", "5", "fonction", "fonction", "40", $fonction, "user_new");





						if ( @$_SESSION["CnxAdm"] == 255 ) {
							$check1 = ( $rb == "N" ) ? "checked=\"checked\"" : "" ;
							$check2 = ( $rb == "O" ) ? "checked=\"checked\"" : "" ;

							print("
				                      <tr>
				                        <td class=\"align-right valign-middle\">". $msg->read($USER_CHS) ."</td>
				                        <td>
									<label for=\"rb_N\"><input type=\"radio\" id=\"rb_N\" name=\"rb\" value=\"N\" $check1 />". $msg->read($USER_NO) ."</label>
								      <label for=\"rb_O\"><input type=\"radio\" id=\"rb_O\" name=\"rb\" value=\"O\" $check2 />". $msg->read($USER_YES) ."</label>
				                        </td>
				                      </tr>

				                      <tr>
				                        <td class=\"align-right valign-middle\">". $msg->read($USER_DELAY) ."</td>
				                        <td>
									<label for=\"delay\"><input type=\"text\" id=\"delay\" name=\"delay\" size=\"20\" value=\"$delay\" /></label>
				                        </td>
				                      </tr>

				                      <tr>
				                        <td class=\"align-right valign-middle\">". $msg->read($USER_PICTURE) ." ($MAXIMGWDTH x $MAXIMGHGTH)</td>
				                        <td>
									<p class=\"hidden\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" /></p>
			      					<input type=\"file\" name=\"UploadedFile\" />
				                        </td>
				                      </tr>");
							}
						}



				?>






				<?php

				// $alreadyShownRubrique

					// Champs perso de user_new
					print ("<tr style=\"height: 10px;\"><td></td><td></td></tr>");
					$tab_champs = displayRubriqueTable("user_new", $ID, $_SESSION["lang"]);
					foreach($tab_champs as $key => $val)
					{
						if (!in_array($key, $alreadyShownRubrique))
						{
							echo "<tr class=\"user_new\"><td class=\"align-right valign-middle\">";
							echo "<strong>".$val["rubrique"]." :</strong><br />";
							echo "</td><td>";
							echo "<label for=\"rubperso_$key\">";
							if($val["type"] == "liste")
							{
								echo "<select name=\"rubperso_$key\">";
								echo "<option value=\"\"></option>";
							}
							foreach($val["valeur"] as $key2 => $val2)
							{
								switch ($val["type"]) {
									case "chaine":
										if($val["valeur"][0] != "")
										{
											echo "<input type=\"text\" value=\"$val2\" name=\"rubperso_$key\" />";
										}
										else
										{
											echo "<input type=\"text\" value=\"".$val["default"]."\" name=\"rubperso_$key\" />";
										}
										break;
									case "liste":
										if($val["selected"] != "")
										{
											$selected = ( $val["selected"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										else
										{
											$selected = ( $val["default"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										echo "<option value=\"$key2\" $selected>$val2</option>";
										break;
								}
							}
							if($val["type"] == "liste") echo "</select>";
							echo "</label>";
							echo "</td></tr>";
						}
					}


					// Champs perso de user_new_eleve
					print ("<tr style=\"height: 10px;\"><td></td><td></td></tr>");
					$tab_champs = displayRubriqueTable("user_new_eleve", $ID, $_SESSION["lang"]);
					foreach($tab_champs as $key => $val)
					{
						if (!in_array($key, $alreadyShownRubrique))
						{
							echo "<tr class=\"user_new_eleve\"><td class=\"align-right valign-middle\">";
							echo "<strong>".$val["rubrique"]." :</strong><br />";
							echo "</td><td>";
							echo "<label for=\"rubperso_$key\">";
							if($val["type"] == "liste")
							{
								echo "<select name=\"rubperso_$key\">";
								echo "<option value=\"\"></option>";
							}
							foreach($val["valeur"] as $key2 => $val2)
							{
								switch ($val["type"]) {
									case "chaine":
										if($val["valeur"][0] != "")
										{
											echo "<input type=\"text\" value=\"$val2\" name=\"rubperso_$key\" />";
										}
										else
										{
											echo "<input type=\"text\" value=\"".$val["default"]."\" name=\"rubperso_$key\" />";
										}
										break;
									case "liste":
										if($val["selected"] != "")
										{
											$selected = ( $val["selected"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										else
										{
											$selected = ( $val["default"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										echo "<option value=\"$key2\" $selected>$val2</option>";
										break;
								}
							}
							if($val["type"] == "liste") echo "</select>";
							echo "</label>";
							echo "</td></tr>";
						}
					}


					// Champs perso de user_new_formateur
					print ("<tr style=\"height: 10px;\"><td></td><td></td></tr>");
					$tab_champs = displayRubriqueTable("user_new_formateur", $ID, $_SESSION["lang"]);
					foreach($tab_champs as $key => $val)
					{
						if (!in_array($key, $alreadyShownRubrique))
						{
							echo "<tr class=\"user_new_formateur\"><td class=\"align-right valign-middle\">";
							echo "<strong>".$val["rubrique"]." :</strong><br />";
							echo "</td><td>";
							echo "<label for=\"rubperso_$key\">";
							if($val["type"] == "liste")
							{
								echo "<select name=\"rubperso_$key\">";
								echo "<option value=\"\"></option>";
							}
							foreach($val["valeur"] as $key2 => $val2)
							{
								switch ($val["type"]) {
									case "chaine":
										if($val["valeur"][0] != "")
										{
											echo "<input type=\"text\" value=\"$val2\" name=\"rubperso_$key\" />";
										}
										else
										{
											echo "<input type=\"text\" value=\"".$val["default"]."\" name=\"rubperso_$key\" />";
										}
										break;
									case "liste":
										if($val["selected"] != "")
										{
											$selected = ( $val["selected"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										else
										{
											$selected = ( $val["default"] == $key2 ) ? "selected=\"selected\"" : "" ;
										}
										echo "<option value=\"$key2\" $selected>$val2</option>";
										break;
								}
							}
							if($val["type"] == "liste") echo "</select>";
							echo "</label>";
							echo "</td></tr>";
						}
					}




				?>

								<tr>
									<td class="align-center" colspan="2"><hr style="width:100%;" /></td>
								</tr>

								<!-- CGU -->
								<tr class="user_new" style="display: none;">

									<td class="align-right valign-middle"><b>Conditions générales d'utilisation: </b></td>
									<td>
										<input type="checkbox" value="true" required> J'accepte les
										<a href="#" class="user_new" onclick="popWin('apropos.php', '580', '600'); return false;">conditions générales d'utilisation</a>
									</td>
								</tr>


								<tr>
									<td class="align-center" colspan="2"><hr style="width:100%;" /></td>
								</tr>

<!-- <div class="g-recaptcha" data-sitekey="6LfuNY8UAAAAANWrIjOMV48xZSQTq0xQi-Iq8z3f"></div> -->


				<?php
//					if ( $item != 1000 OR $ID )


					if ( @$_SESSION["CnxAdm"] & 8 OR ($item == 1000 AND $ID == 0) )
						print("
					           <tr id=\"validButton\" class=\"user_new\">
					              <td class=\"valign-middle align-right\">
								<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($USER_INPUTOK)."\" />
					              </td>
					              <td class= \"valign-middle\">
					              	". $msg->read($USER_UPDATEOK) ."
					              </td>
					           </tr>");

					// bouton retour
					if ($Litem == "") $Litem = "1";
					$href = ( @$_SESSION["CnxAdm"] & 8 )
						? ($IDeleve ? "item=1&cmde=show&visu=1" : "item=1&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&authuser=$authuser")
						: "item=0" ;
					echo "
				           <tr class=\"user_new\">
				              <td class=\"valign-middle align-right\">
				              	<a href=\"".myurlencode("index.php")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($USER_INPUTCANCEL)."\" /></a>
				              </td>
				              <td class= \"valign-middle\">
				              	". $msg->read($USER_BACK) ."
				              </td>
				           </tr>" ;
				?>
				</table>

                <!-- </td>
              </tr>
            </table> -->
	</form>

</div>



<script src="script/jquery.min.vupload.js"></script>



<style>
	.user_new {
		display: none;
	}

	.user_new_formateur {
		display: none;
	}

	.user_new_eleve {
		display: none;
	}

	.error_field {
		display: none;
	}



</style>

<div style="display: none;" id="isErrorMail">0</div>
<div style="display: none;" id="isErrorUsername">0</div>

<script type="text/javascript">

function showElementSpecific() {

	var selected = $('#selectType').val();
	$('#selectType').hide();
	$('.selectType').hide();

	$('.user_new_eleve').hide();
	$('.user_new_formateur').hide();

	$('.user_new').show();
	$('.'+ selected).show();



	if (selected == "user_new_eleve") {
		$('#classID').attr("required", true);
		$('#user_formateur_optgroup').attr("disabled", true);
		$('#user_admin_optgroup').attr("disabled", true);
	}
// 	user_new_eleve
// 	user_new_formateur
//
// 	user_eleve_optgroup
// user_formateur_optgroup
// user_admin_optgroup

}


$('#ident').keyup(function() {

	var ident = $('#ident').val();
	$.ajax({
		url : 'include/fonction/ajax/user_new.php?action=checkIdent',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : 'ident=' + ident,

		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			if (code_html == "success") {
				$("#errorUserName").hide();
        jQuery("#isErrorUsername").html('0');
        if (jQuery("#isErrorMail").html() == 0)
        {
          jQuery("#validButton").show();
        }
			}
			else {
				$("#errorUserName").show();
        jQuery("#isErrorUsername").html('1');
        jQuery("#validButton").hide();
			}
		}
	});
});

$('#email').keyup(function() {

	var email = $('#email').val();
	var emailLenght = $('#email').val().length;
	$.ajax({
		url : 'include/fonction/ajax/user_new.php?action=checkMail',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : 'email=' + email,

		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			if (code_html == "success") {
				$("#errorMail").hide();
        jQuery("#isErrorMail").html('0');
        if (jQuery("#isErrorUsername").html() == 0)
        {
          jQuery("#validButton").show();
        }
			}
			else {
				$("#errorMail").show();
        jQuery("#isErrorMail").html('1');
        jQuery("#validButton").hide();
			}
		}
	});
});

</script>
