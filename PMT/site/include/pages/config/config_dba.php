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
 *		module   : config_dba.php
 *		projet   : paramétrage de la base de données
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 13/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

  // On vérifie que l'on soit bien un super-administrateur
  if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');


  // On récupère les éléments dans le GET
  $get = array('show', 'idcat', 'value', 'newcentre', 'newgrp', 'newclass', 'newmat', 'newmotif', 'newsalle', 'newforfait', 'newpole');
  foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

  // On récupère les éléments dans le POST
  $post = array('option', 'matclass', 'profclass', 'goclass', 'gosalle', 'supplogo', 'forfaitval', 'type_matiere', 'currentPane', 'ident', 'identforfait', 'titre', 'color1', 'text', 'code', 'delay', 'idrec', 'name_pole_text');
  foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

  // On récupère les éléments dans le POST et si existe pas alors dans le GET
  $post_get = array('submit', 'tablidx', 'IDcentre', 'IDconf');
  foreach ($post_get as $value) {
    if (isset($_POST[$value]) && $_POST[$value]) $$value = $_POST[$value];
    elseif (isset($_GET[$value]) && $_GET[$value]) $$value = $_GET[$value];
  }


  // On récupère les valeurs par défaut si pas d'info récupérées dans le post ou get
  if (!isset($IDcentre)) $IDcentre = $_SESSION['CnxCentre'];
  if (!isset($delay)) $delay = $ACOUNTIME;
  if (isset($optiona) && $optiona == 'on') $option = 1; else $option = 0;
  if (isset($name_pole_text)) $identpole = $name_pole_text;

  // On formatte les éléments récupérés correctement
  $formatslashes = array('ident', 'identforfait', 'titre', 'color1', 'text', 'code', 'identpole');
  foreach ($formatslashes as $value) if (isset($$value)) $$value = addslashes(stripslashes($$value));


  unset($name_pole_text, $optiona);

  if (isset($newcentre)   && $newcentre != '')     $currentPane = '#tab_centers';
  if (isset($newgrp)      && $newgrp != '')        $currentPane = '#tab_groups';
  if (isset($newclass)    && $newclass != '')      $currentPane = '#tab_classes';
  if (isset($newmat)      && $newmat != '')        $currentPane = '#tab_matieres';
  if (isset($newmotif)    && $newmotif != '')      $currentPane = '#tab_abs_motifs';
  if (isset($newsalle)    && $newsalle != '')      $currentPane = '#tab_salles';
  if (isset($newforfait)  && $newforfait != '')    $currentPane = '#tab_forfait';
  if (isset($newpole)     && $newpole != '')       $currentPane = '#tab_poles';


  if (isset($color1)) {
    if ($color1 == '' || $color1 == '#') $color1 = 'aaaaaa';
    $color1 = strtolower(str_replace('#', '', $color1));
  }
?>


<?php
	// création
	if (isset($submit) && $submit == $msg->read($CONFIG_CREAT))
	{
		switch ($tablidx) {
      // Création d'un centre
			case 1 :
				if (isset($ident) && $ident != '') {
          // On crée le nouveau centre
          $query  = "insert into config_centre ";
					$query .= "values(NULL, '$ident', '', '', '', '', '', 'O', '".$_SESSION["lang"]."', '[1,\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\",\"1\",\"2\"]', '{\"start\":[\"\",\"\",\"\"],\"end\":[\"\",\"\",\"\"]}')";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
          $temp_centreID = mysqli_insert_id($mysql_link);
					// mise à jour des mot-clefs
					for ($i = 0; $i < 3; $i++) {
  					$query  = "insert into config_def values('', '".$temp_centreID."', '$keywords_search[$i]', '$keywords_replace[$i]', '".$_SESSION["lang"]."') ";
  					mysqli_query($mysql_link, $query);
            unset($query);
					}
				}
				break;

			case 2 :
        if (isset($ident) && $ident != '') {
          $query  = "insert into user_group ";
          $query .= "values(NULL, '$ident', ";
          if ($delay != '' && $delay != '0000-00-00 00:00:00') $query .= "'$delay'";
          else $query .= 'NULL';
          $query .= ", '$HDQUOTAS', '1', 'O', '".$_SESSION["lang"]."')";
          mysqli_query($mysql_link, $query);
          unset($query);  // Nécessaire sinon double création
        }
				break;

			case 3 :
				if (isset($text) && $text != '')
				{
					$query  = "insert into campus_classe ";
					$query .= "values(NULL, '0', '255', '255', '$IDcentre', '$text', '', '0', '0', 'N', 'N', NULL, 'O', '$code','".$_SESSION["lang"]."', '".$_POST['grad_year']."')";
					@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					$lastidclass = mysqli_insert_id($mysql_link);

          // Matières de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRuser IS NULL ";
					$query .= "AND _IDRclass = $idrec ";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
          if (isset($matclass)) {
            for ($i = 0; $i < count($matclass); $i++ )
  					{
              if (isset($matclass[$i])) {
                $query  = "INSERT INTO class_mat_user VALUE ('$idrec', '$matclass[$i]', NULL, '') ";
    						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
                unset($query);
              }
  					}
          }

          // Profs de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRmat IS NULL ";
					$query .= "AND _IDRclass = $idrec ";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
          if (isset($profclass)) {
            for ($i = 0; $i < count($profclass); $i++ )
  					{
              if (isset($profclass[$i])) {
                $query  = "INSERT INTO class_mat_user VALUE ('$idrec', NULL, '$profclass[$i]', '') ";
    						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
                unset($query);
              }
  					}
          }
				}
				break;

			case 4 :
				if (isset($titre) && $titre != '') {
					// on crée une entrée dans les ressources pour la nouvelle matière
					$query  = "insert into resource_data values(NULL, '2', '0', '".$_SESSION["CnxGrp"]."', '255', '$titre', '', '0', '1', 'N', '1', 'O', 'O', '".$_SESSION["lang"]."') ";
          mysqli_query($mysql_link, $query);
          $listInsertID = mysqli_insert_id($mysql_link);
          unset($query);
					// on enregistre le forfait de la matière
          if (isset($forfaitval)) {
            $query  = "insert into mat_forfait values('$forfaitval', '".$listInsertID."') ";
  					mysqli_query($mysql_link, $query);
            unset($query);
          }

					$query  = "insert into campus_data values(NULL, '0', '".$_SESSION["CnxGrp"]."', '255', '', '$titre', '".$msg->read($CONFIG_WELCOMEPAGE, $titre)."', '10', 'N', 'N', NULL, 'O', '".$_SESSION["lang"]."', '$color1', $option, '$code', '$type_matiere') ";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
				}
				break;

			case 5 :
				if (isset($ident) && $ident != '') {
					$query  = "insert into absent_data ";
					$query .= "values(NULL, '$ident', 'O', '".$_SESSION["lang"]."')";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
				}
				break;

			case 6 :
				if (isset($text) && $text != '') {
					$query  = "insert into edt_items ";
					$query .= "values(NULL, '$IDcentre', '$text', '".$_SESSION["lang"]."')";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
				}
				break;

			case 7 :
        if (isset($identforfait) && $identforfait != '') {
          $Query  = "select _IDforfait from forfait order by _IDforfait desc limit 1 ";
          $result = mysqli_query($mysql_link, $Query);
          $row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
          $newidforfait = $row[0] + 1;
          $query  = "insert into forfait values('$newidforfait', '$identforfait') ";
          mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
        }
				break;

			case 8 :
				if (isset($identpole) && $identpole != '') {
					$query  = "insert into `pole` ";
					$query .= "values(NULL, '".$identpole."', '".$_SESSION["lang"]."', '', 'N')";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          unset($query);
					$IDOfInsertQuery = mysqli_insert_id($mysql_link);
					foreach ($_POST as $key => $value) {
						if (strpos($key, "Matiere_annee_") !== FALSE)
						{
							$anneeToInser = substr($key, -1);
              if (isset($value) && is_array($value)) {
                foreach ($value as $key2 => $value2) {
  								// $query2  = "INSERT INTO `pole_mat_annee`(`_ID_pma`, `_ID_year`, `_ID_pole`, `_ID_matiere`, `_pma_coef`)";
  								// $query2 .= "VALUES (NULL, '".$anneeToInser."', '".$IDOfInsertQuery."', '".$value[$key2]."', '0')";
  								// mysqli_query($mysql_link, $query2) or die('Erreur SQL !<br>'.$query2.'<br>'.mysqli_error($mysql_link));

                  $datas = array(
                    '_ID_year'    => $anneeToInser,
                    '_ID_pole'    => $IDOfInsertQuery,
                    '_ID_matiere' => $value[$key2],
                    '_pma_coef'   => 0
                  );
                  $db->query("INSERT INTO `pole_mat_annee` SET ?u ", $datas);
  							}
              }
              elseif (isset($value) && !is_array($value)) {
                $datas = array(
                  '_ID_year'    => $anneeToInser,
                  '_ID_pole'    => $IDOfInsertQuery,
                  '_ID_matiere' => $value,
                  '_pma_coef'   => 0
                );
                $db->query("INSERT INTO `pole_mat_annee` SET ?u ", $datas);
              }
						}
					}

				}
				$identpole = "";
				break;

			default :
				break;
			}
	}

	// modification
	if (isset($submit) && $submit == $msg->read($CONFIG_MODIFY))
	{
		switch ( $tablidx )
		{
			case 1 :
				if ( $ident != "" ) {
					$query  = "update config_centre ";
					$query .= "set _ident = '$ident' ";
					$query .= "where _IDcentre = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 2 :
				if ( $ident != "" ) {
					$query  = "update user_group ";
					$query .= "set _ident = '$ident' ";


					if ($delay != '0000-00-00 00:00:00' && $delay != '') $query .= ", `_delay` = '".$delay."' ";
					else $query .= ', `_delay` = NULL ';
					$query .= "where _IDgrp = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
			case 3 :
				if ( $text != "" )
				{
					$query  = "update campus_classe ";
					$query .= "set _IDcentre = '$IDcentre', _ident = '$text', _code = '$code', `_graduation_year` = '".$_POST['grad_year']."' ";
					$query .= "where _IDclass = '$idrec'";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

          // Matières de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRuser IS NULL ";
					$query .= "AND _IDRclass = $idrec ";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          if (isset($matclass)) {
            for ($i = 0; $i < count($matclass); $i++ )
  					{
              if (isset($matclass[$i])) {
                $query  = "INSERT INTO class_mat_user VALUE ('$idrec', '$matclass[$i]', NULL, '') ";
    						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
                unset($query);
              }
  					}
          }

          // Profs de la classe
					$query  = "DELETE FROM class_mat_user ";
					$query .= "WHERE _IDRmat IS NULL ";
					$query .= "AND _IDRclass = $idrec ";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
          if (isset($profclass)) {
            for ($i = 0; $i < count($profclass); $i++ )
  					{
              if (isset($profclass[$i])) {
                $query  = "INSERT INTO class_mat_user VALUE ('$idrec', NULL, '$profclass[$i]', '') ";
    						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
                unset($query);
              }
  					}
          }
				}
				break;
			case 4 :
				if ( $titre != "" ) {
					// on crée une entrée dans les ressources pour la nouvelle matière
					$query  = "insert into resource_data ";
					$query .= "values('', '2', '0', '".$_SESSION["CnxGrp"]."', '255', '$titre', '', '0', '1', 'N', '1', 'O', 'O', '".$_SESSION["lang"]."')";
					mysqli_query($mysql_link, $query);

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
					mysqli_query($mysql_link, $query);

					$query  = "update campus_data ";
					$query .= "set _titre = '$titre', _color = '$color1', _option = '$option', _code = '$code', _type = '$type_matiere' ";
					$query .= "where _IDmat = '$idrec' AND _lang = '".$_SESSION["lang"]."' limit 1";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 5 :
				if ( $ident != "" ) {
					$query  = "update absent_data ";
					$query .= "set _texte = '$ident' ";
					$query .= "where _IDdata = '$idrec'";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 6 :
				if ( $text != "" ) {
					$query  = "update edt_items ";
					$query .= "set _title = '$text' ";
					$query .= "where _IDitem = '$idrec'";
					mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
					}
				break;
				case 7 :
					if (isset($identforfait) && $identforfait != '') {
						$query  = "update forfait ";
						$query .= "set _Nom = '$identforfait' ";
						$query .= "where _IDforfait = '$idrec'";
						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
						}
					break;
				case 8 :
					if (isset($identpole) && $identpole != '') {
						$query  = "update `pole` ";
						$query .= "set `_name` = '$identpole' ";
						$query .= "where `_ID` = '$idrec'";
						mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

						$query3  = "DELETE FROM `pole_mat_annee` ";
						$query3 .= "WHERE `_ID_pole` = '".$idrec."' ";

						foreach ($_POST as $key => $value) {
							if (strpos($key, "Matiere_annee_") !== FALSE)
							{
// echo $value;
								$anneeToInser = substr($key, -1);
								foreach (explode(',', $value) as $key2 => $value2) {
									$query4 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_year` = '".$anneeToInser."' AND `_ID_matiere` = '".$value2."' AND `_ID_pole` = '".$idrec."' ";
									$result4 = mysqli_query($mysql_link, $query4);
									while ($row4 = mysqli_fetch_array($result4, MYSQLI_NUM)) {
										$query3  .= "AND `_ID_pma` != '".$row4[0]."' ";
									}
								}
							}
						}
						$result3 = mysqli_query($mysql_link, $query3);

						foreach ($_POST as $key => $value) {
							if (strpos($key, "Matiere_annee_") !== FALSE)
							{
								$anneeToInser = substr($key, -1);
								foreach (explode(',', $value) as $key2 => $value2) {
									$query4  = "SELECT * FROM `pole_mat_annee` WHERE `_ID_year` = '".$anneeToInser."' AND `_ID_matiere` = '".$value2."' AND `_ID_pole` = '".$idrec."' ";
									$result4 = mysqli_query($mysql_link, $query4);
									if (mysqli_num_rows($result4) == 0)
									{
                    if (!$value2) continue;
										$query2  = "INSERT INTO `pole_mat_annee`(`_ID_pma`, `_ID_year`, `_ID_pole`, `_ID_matiere`, `_pma_coef`)";
										$query2 .= "VALUES (NULL, '".$anneeToInser."', '".$idrec."', '".$value2."', '0')";
										mysqli_query($mysql_link, $query2) or die('Erreur SQL !<br>'.$query2.'<br>'.mysqli_error($mysql_link));
									}
								}
							}
						}

					}
					break;
				default :
				break;
		}
	}

	// droits étendus et catégorie des groupes
	if (isset($submit) && $submit == "toggle") {
		$idcat  = ( $idcat ) ? ($idcat % 3) + 1 : 0 ;

		$query  = "update user_group ";
		$query .= ( $idcat ) ? "set _IDcat = '$idcat'" : "" ;
		$query .= " where _IDgrp = '$value' AND _lang = '".$_SESSION["lang"]."' limit 1";
		}

	if (isset($query) && $query != '')
		mysqli_query($mysql_link, $query);
	// l'utilisateur a validé
	if ((isset($_POST["delc"]) && $_POST["delc"] != '') || (isset($_POST["delg"]) && $_POST["delg"] != '') || (isset($_POST["delk"]) && $_POST["delk"] != '') || (isset($_POST["delm"]) && $_POST["delm"] != '') || (isset($_POST["delmo"]) && $_POST["delmo"] != '') || (isset($_POST["delsa"]) && $_POST["delsa"] != '') || (isset($_POST["delforfait"]) && $_POST["delforfait"] != '') || (isset($_POST["delp"]) && $_POST["delp"] != ''))
	{
		//---- suppression ----
		if (isset($_POST["delc"])) $delc = $_POST["delc"];
		if (isset($_POST["delg"])) $delg = $_POST["delg"];
		if (isset($_POST["delk"])) $delk = $_POST["delk"];
		if (isset($_POST["delm"])) $delm = $_POST["delm"];
		if (isset($_POST["delmo"])) $delmo = $_POST["delmo"];
		if (isset($_POST["delsa"])) $delsa = $_POST["delsa"];
		if (isset($_POST["delforfait"])) $delforfait = $_POST["delforfait"];
		if (isset($_POST["delp"])) $delpole = $_POST["delp"];

		// suppression des centres
    if (isset($delc)) {
      for ($i = 0; $i < count($delc); $i++ )
        if (isset($delc[$i]))
        {
          $query  = "delete from config_centre ";
          $query .= "where _IDcentre = '$delc[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
          mysqli_query($mysql_link, $query);
        }
    }

    // suppression des groupes
    if (isset($delg)) {
      for ($i = 0; $i < count($delg); $i++ )
  			if (isset($delg[$i])) {
  				$query  = "delete from user_group ";
  				$query .= "where _IDgrp = '$delg[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";

  				mysqli_query($mysql_link, $query);
  			}
    }



		// suppression des classes
    if (isset($delk)) {
      for ($i = 0; $i < count($delk); $i++ )
        if (isset($delk[$i])) {
          $query  = "delete from campus_classe ";
          $query .= "where _IDclass = '$delk[$i]' ";
          mysqli_query($mysql_link, $query);
        }
    }


		// suppression des matières
    if (isset($delm)) {
      for ($i = 0; $i < count($delm); $i++ )
  			if (isset($delm[$i])) {
  				$query  = "delete from campus_data ";
  				$query .= "where _IDmat = '$delm[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
  				mysqli_query($mysql_link, $query);
  			}
    }



		// suppression des motifs
    if (isset($delmo)) {
      for ($i = 0; $i < count($delmo); $i++ )
        if ( @$delmo[$i] ) {
          $query  = "delete from absent_data ";
          $query .= "where _IDdata = '$delmo[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
          mysqli_query($mysql_link, $query);
        }
    }


		// suppression des salles
    if (isset($delsa)) {
      for ($i = 0; $i < count($delsa); $i++ )
        if (isset($delsa[$i])) {
          $query  = "delete from edt_items ";
          $query .= "where _IDitem = '$delsa[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
          mysqli_query($mysql_link, $query);
        }
    }


		// suppression des forfaits
    if (isset($delforfait)) {
      for ($i = 0; $i < count($delforfait); $i++ )
        if (isset($delforfait[$i])) {
          $query  = "delete from forfait ";
          $query .= "where _IDforfait = '$delforfait[$i]' limit 1";
          mysqli_query($mysql_link, $query);
        }
    }


		// suppression des pôles
    if (isset($delpole)) {
      for ($i = 0; $i < count($delpole); $i++)
  		{
  			if (isset($delpole[$i])){
  				$query  = "delete from `pole` ";
  				$query .= "WHERE `_ID` = '".$delpole[$i]."' ";
  				mysqli_query($mysql_link, $query);

  				$query  = "delete from `pole_mat_annee` ";
  				$query .= "WHERE `_ID_pole` = '".$delpole[$i]."' ";
  				mysqli_query($mysql_link, $query);
  			}
  		}
    }


		//---- rendre visible/invisible ----
		$isc  = @$_POST["isc"];
		$isg  = @$_POST["isg"];
		$isk  = @$_POST["isk"];
		$ism  = @$_POST["ism"];
		$ismo = @$_POST["ismo"];
		$isp = @$_POST["isp"];

		// les centres
		$query  = "update config_centre ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($isc)) {
        for ($i = 0; $i < count($isc); $i++)
          if (isset($isc[$i])) {
            $query  = "update config_centre ";
            $query .= "set _visible = 'O' ";
            $query .= "where _IDcentre = '$isc[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
    }


		// les groupes
		$query  = "update user_group ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($isg)) {
        for ($i = 0; $i < count($isg); $i++)
          if (isset($isg[$i])) {
            $query  = "update user_group ";
            $query .= "set _visible = 'O' ";
            $query .= "where _IDgrp = '$isg[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
    }


		// les classes
		$query  = "update campus_classe ";
		$query .= "set _visible = 'N' ";
		$query .= "where _IDcentre = '$IDcentre' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($isk)) {
        for ($i = 0; $i < count($isk); $i++)
          if (isset($isk[$i])) {
            $query  = "update campus_classe ";
            $query .= "set _visible = 'O' ";
            $query .= "where _IDcentre = '$IDcentre' AND _IDclass = '$isk[$i]' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
    }


		// les matières
		$query  = "update campus_data ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($ism)) {
        for ($i = 0; $i < count($ism); $i++)
          if (isset($ism[$i])) {
            $query  = "update campus_data ";
            $query .= "set _visible = 'O' ";
            $query .= "where _IDmat = '$ism[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
    }


		// les motifs
		$query  = "update absent_data ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($ismo)) {
        for ($i = 0; $i < count($ismo); $i++)
          if (isset($ismo[$i])) {
            $query  = "update absent_data ";
            $query .= "set _visible = 'O' ";
            $query .= "where _IDdata = '$ismo[$i]' AND _lang = '".$_SESSION["lang"]."' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
    }


		// les pôles
		$query  = "update `pole` ";
		$query .= "set _visible = 'N' ";
		$query .= "where _lang = '".$_SESSION["lang"]."' ";

		if (mysqli_query($mysql_link, $query)) {
      if (isset($isp)) {
        for ($i = 0; $i < count($isp); $i++)
          if (isset($isp[$i])) {
            $query  = "update `pole` ";
            $query .= "set `_visible` = 'O' ";
            $query .= "where `_ID` = '".$isp[$i]."' AND _lang = '".$_SESSION["lang"]."' limit 1";
            mysqli_query($mysql_link, $query);
          }
      }
		}
	}


  // initialisation des liens
  $mylink = 'index.php?item='.$item.'&cmde=dba&IDconf='.(@$IDconf).'&IDcentre='.$IDcentre;
?>

<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_DBACONFIG); ?></h1>

	<form id="formulaire" action="index.php?item=<?php echo $item; ?>&tablidx=<?php echo $tablidx; ?>" method="post" enctype="multipart/form-data">
    <?php
      $temp = array('item', 'cmde', 'IDconf', 'goclass', 'gosalle');
      foreach ($temp as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
    ?>

    <?php include("include/config_menu_top.php"); ?>

    <?php echo centerSelect($IDcentre); ?>

    <div class="card shadow mb-4">
      <div class="card-header">
        <ul class="nav nav-pills card-header-pills" id="myTab" role="tablist">
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (!isset($currentPane) || $currentPane == '#tab_centers') echo 'active'; ?>"      id="tab_centers-tab"        data-toggle="tab" href="#tab_centers"    role="tab" aria-controls="tab_centers"    aria-selected="true"><?php echo $msg->read($CONFIG_CENTERS); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_groups') echo 'active'; ?>"        id="tab_groups-tab"         data-toggle="tab" href="#tab_groups"     role="tab" aria-controls="tab_groups"     aria-selected="false"><?php echo $msg->read($CONFIG_GROUPS); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_classes') echo 'active'; ?>"       id="tab_classes-tab"        data-toggle="tab" href="#tab_classes"    role="tab" aria-controls="tab_classes"    aria-selected="false"><?php echo $msg->read($CONFIG_CLASS); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_matieres') echo 'active'; ?>"      id="tab_matieres-tab"       data-toggle="tab" href="#tab_matieres"   role="tab" aria-controls="tab_matieres"   aria-selected="false"><?php echo $msg->read($CONFIG_MATTER); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_abs_motifs') echo 'active'; ?>"    id="tab_abs_motifs-tab"     data-toggle="tab" href="#tab_abs_motifs" role="tab" aria-controls="tab_abs_motifs" aria-selected="false"><?php echo $msg->read($CONFIG_MOTIF); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_salles') echo 'active'; ?>"        id="tab_salles-tab"         data-toggle="tab" href="#tab_salles"     role="tab" aria-controls="tab_salles"     aria-selected="false"><?php echo $msg->read($CONFIG_SALLES); ?></a></li>
          <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_poles') echo 'active'; ?>"         id="tab_poles-tab"          data-toggle="tab" href="#tab_poles"      role="tab" aria-controls="tab_poles"      aria-selected="false"><?php echo $msg->read($CONFIG_POLE); ?></a></li>
          <?php if (getParam('FORFAIT')) { ?>
            <li class="nav-item" role="presentation"><a class="nav-link <?php if (isset($currentPane) && $currentPane == '#tab_forfait') echo 'active'; ?>"     id="tab_forfait-tab"        data-toggle="tab" href="#tab_forfait"    role="tab" aria-controls="tab_forfait"      aria-selected="false">Forfait</a></li>
          <?php } ?>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade <?php if (!isset($currentPane) || $currentPane == '#tab_centers') echo 'show active'; ?>"     id="tab_centers"     role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_centre.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_groups') echo 'show active'; ?>"       id="tab_groups"      role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_group.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_classes') echo 'show active'; ?>"      id="tab_classes"     role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_classe.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_matieres') echo 'show active'; ?>"     id="tab_matieres"    role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_matiere.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_abs_motifs') echo 'show active'; ?>"   id="tab_abs_motifs"  role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_absence_motif.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_salles') echo 'show active'; ?>"       id="tab_salles"      role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_salle.php'); ?></div>
          <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_poles') echo 'show active'; ?>"        id="tab_poles"       role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_pole.php'); ?></div>
          <?php if (getParam('FORFAIT')) { ?>
            <div class="tab-pane fade <?php if (isset($currentPane) && $currentPane == '#tab_forfait') echo 'show active'; ?>"    id="tab_forfait"     role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/database/config_forfait.php'); ?></div>
          <?php } ?>
        </div>


        <?php if ((!isset($_GET['newforfait']) || $_GET['newforfait'] == '') && (!isset($_GET['newcentre']) || $_GET['newcentre'] == '') && (!isset($_GET['newclass']) || $_GET['newclass'] == '') && (!isset($_GET['newsalle']) || $_GET['newsalle'] == '') && (!isset($_GET['newmotif']) || $_GET['newmotif'] == '') && (!isset($_GET['newgrp']) || $_GET['newgrp'] == '') && (!isset($_GET['newmat']) || $_GET['newmat'] == '')) { ?>
          <div class="mt-3">
            <button type="submit" class="btn btn-success"><?php echo $msg->read($CONFIG_INPUTOK); ?></button>
            <a href="index.php" class="btn btn-danger"><?php echo $msg->read($CONFIG_INPUTCANCEL); ?></a>
          </div>
        <?php } ?>

      </div>
    </div>


    <!-- C'est dans cet input que l'on va stoquer l'onglet actuel -->
    <input type="hidden" name="currentPane" id="currentPaneInput" value="<?php echo $currentPane; ?>">
    </form>







<script src="script/bootstrap-notify-min.js"></script>



<input type="hidden" id="matDisabled" value="">

<script>

jQuery(document).ready(function() {
	// Pour vérifier si la matière n'est pas liée à un pôle
		$.ajax({
			url : 'include/fonction/ajax/update_pma.php?action=gedIfMatIsLinkToPole',
			type : 'POST', // Le type de la requête HTTP, ici devenu POST
			data : '',
			async: false,
			dataType : 'html', // On désire recevoir du HTML
			success : function(code_html, statut){ // code_html contient le HTML renvoyé
				jQuery("#matDisabled").attr("value", code_html);
			}
		});
});

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

				// On vérifie si la matière n'est pas liée à un pôle
				var elemID = jQuery(this).attr("id").substring(5);
				var listMat = jQuery("#matDisabled").attr("value");
				if (listMat.indexOf(";" + elemID + ";") >= 0)
				{
					jQuery("#delm_" + elemID).attr("disabled", "true");
				}

			}
		});

		// On vérifie si la matière n'est pas liée à un pôle
		var elemID = jQuery(this).attr("id").substring(5);
		var listMat = jQuery("#matDisabled").attr("value");
		if (listMat.indexOf(";" + elemID + ";") >= 0)
		{
			jQuery("#delm_" + elemID).attr("disabled", "true");
		}
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


	jQuery("input[id^='delp_']").each(function( index ) {
	jQuery(this).change(function() {



		if(this.checked) {


			alert("Attention : en cochant cette case vous allez effectuer une suppression !");



			$.ajax({
				url : 'include/fonction/ajax/update_pma.php?action=checkForRemovePole',
				type : 'POST', // Le type de la requête HTTP, ici devenu POST
				data : 'currentpole=' + this.value,

				//line added to get ajax response in sync
				async: false,

				dataType : 'html', // On désire recevoir du HTML
				success : function(code_html, statut){ // code_html contient le HTML renvoyé
					// alert(code_html);
					if (code_html != "error") {
						$('#errorWhenRemovePole').html("0");
					}
					else
					{
						$.notify({
							// options
							icon: 'fa fa-times',
							message: 'Erreur, un syllabus est lié à une matière de ce pôle'
						},{
							// settings
							type: 'danger'
						});
						$('#errorWhenRemovePole').html("1");
					}
				}
			});

			if ($('#errorWhenRemovePole').html() == "1") {
				$('#delp_' + this.value).prop('checked', 0);
				$('#errorWhenRemovePole').html("0");
			}
		}
	});
});

});



// Fonction qui change la valeur de l'onglet actuel dans l'input, cette valeur sera transmise en POST lors de l'envois du formulaire et nous permet de revenir directement sur l'onglet actuel
$("li").click(function(){
	if ($(this).find("a").attr("href").indexOf("tab") != -1) {
		$("#currentPaneInput").attr("value", $(this).find("a").attr("href"));
	}
});

<?php if (isset($_GET['newclass']) && $_GET['newclass'] != '') { ?>
  $("#grad_year").change(function() {
  		var graduationYear = $("#grad_year").val();

  		$.ajax({
  			url : 'include/fonction/ajax/fonction.php?action=getPromotionByGraduationYear',
  			type : 'POST', // Le type de la requête HTTP, ici devenu POST
  			data : 'graduationYear=' + graduationYear,

  			dataType : 'html', // On désire recevoir du HTML
  			success : function(code_html, statut){ // code_html contient le HTML renvoyé
  				if (code_html != "error") {
  					$("#currentPromotion").html("<b>Classe actuelle: </b>" + code_html);
  					$('input[name="code"]').attr("value", code_html.charAt(0));
  				}
  				else
  				{
  					$('#currentPromotion').html('<b>ERREUR</b>');
  					$('input[name="code"]').attr('value', "");
  				}

  			}

  		});
  });


  $( document ).ready(function() {
  	var graduationYear = $("#grad_year").val();
  	$.ajax({
  		url : 'include/fonction/ajax/fonction.php?action=getPromotionByGraduationYear',
  		type : 'POST', // Le type de la requête HTTP, ici devenu POST
  		data : 'graduationYear=' + graduationYear,
  		dataType : 'html', // On désire recevoir du HTML
  		success : function(code_html, statut){ // code_html contient le HTML renvoyé
  			if (code_html != "error") {
  				$("#currentPromotion").html("<b>Classe actuelle: </b>" + code_html);
  				$('input[name="code"]').attr("value", code_html.charAt(0));
  			}
  			else
  			{
  				$("#currentPromotion").html("<b>ERREUR</b>");
  				$('input[name="code"]').attr("value", "");
  			}

  		}

  	});
  });
<?php } ?>


</script>
