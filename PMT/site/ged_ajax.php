<?php
  session_start();
  require_once "config.php";
  require_once "include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  include ('include/fonction/protection_input.php');

  if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {
    if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
    else $action = "";
    if (isset($_POST['userID'])) $userID_request = addslashes(stripslashes($_POST['userID']));
    else $userID_request = "";
    if (isset($_POST['currentPath'])) $currentPath = addslashes(stripslashes($_POST['currentPath']));
    else $currentPath = "";
    if (isset($_POST['idOfFileToMove'])) $idOfFileToMove = addslashes(stripslashes($_POST['idOfFileToMove']));
    else $idOfFileToMove = "";
    if (isset($_POST['type'])) $type = addslashes(stripslashes($_POST['type']));
    else $type = "";
    if (isset($_POST['idOfFileToShare'])) $idOfFileToShare = addslashes(stripslashes($_POST['idOfFileToShare']));
    else $idOfFileToShare = "";
    if (isset($_POST['idOfFile'])) $idOfFile = addslashes(stripslashes($_POST['idOfFile']));
    else $idOfFile = "";
    if (isset($_POST['idFolder'])) $idFolder = addslashes(stripslashes($_POST['idFolder']));
    else $idFolder = "";
    if (isset($_POST['ID_copie'])) $ID_copie = addslashes(stripslashes($_POST['ID_copie']));
    else $ID_copie = "";





      if ($action == "getFolderList")
      {
      	//$arrayToReturn = array();

        if ($_SESSION['CnxGrp'] == 4) $userID = $userID_request;
        else $userID = $_SESSION['CnxID'];

      	$query  = "SELECT * ";
      	$query .= "FROM images ";
      	$query .= "WHERE _ID = ".$userID." ";
      	$query .= "AND _parent = '".$currentPath."' ";
      	$query .= "AND _ext = 'fol' ";
      	$query .= "AND _IDimage != '".$idOfFileToMove."' ";
      	$query .= ($type == "user") ? "AND _type = 'user' " : "";
      	$query .= "ORDER BY `images`.`_title` DESC ";

      	$result = mysqli_query($mysql_link, $query);
      	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    		// ID: $row[0]
    		// NOM: $row[3]
    		$nameOfFolder = substr($row[3], 1);
    		if ($row[0] != "")
    		{
    			echo "
    			<tr>
    				<td style=\"width: 40px;\"><img src=\"images/filetype/fol.png\" style=\"height: 30px;\"></td>
    				<td>
    					<a href=\"#\" onclick=\"showFileListForMove(".$idOfFileToMove.", ".$row[0].");\" class=\"folder_name_to_move\" title=\"".$nameOfFolder."\">".$nameOfFolder."</a>
    				</td>
    			</tr>";
    		}
      	}
      }


      if ($action == "getBreadcrumb")
      {
      	$current_folder_id = $currentPath;
        if ($_SESSION['CnxGrp'] == 4) $user_id = $userID_request;
        else $user_id = $_SESSION['CnxID'];
        $parent = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
      	if ($current_folder_id == "") $parent .= '<li class="breadcrumb-item active">Racine</li>';
      	else
      	{
      		$query  = "SELECT _parent, _title ";
      		$query .= "FROM images ";
      		$query .= "WHERE _ID = ".$user_id." AND _IDimage = ".$current_folder_id." ";
      		$result = mysqli_query($mysql_link, $query);
      		while ($row1 = mysqli_fetch_array($result, MYSQLI_NUM)) {
      		  $current_parent = $row1[0];
      		  $current_folder_title = substr($row1[1], 1);
      		}
          $parent .= '<li class="breadcrumb-item"><a href="#" onclick="showFileListForMove('.$idOfFileToMove.', \'\');"><i class="fas fa-folder"></i>&nbsp;Racine</a></li>';
          $temp = '';
      		while ($current_parent != "")
      		{
      		  $query  = "SELECT _title, _parent ";
      		  $query .= "FROM images ";
      		  $query .= "WHERE _ID = ".$user_id." AND _IDimage = ".$current_parent." ";
      		  $result = mysqli_query($mysql_link, $query);
      		  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      			$folder_name = substr($row[0], 1);
            $temp = '<li class="breadcrumb-item"><a href="#" onclick="showFileListForMove('.$idOfFileToMove.', '.$current_parent.');"><i class="fas fa-folder"></i>&nbsp;'.$folder_name.'</a></li>'.$temp;
      			$current_parent = $row[1];
      		  }
      		}
          $parent .= $temp;
          $parent .= '<li class="breadcrumb-item active"><i class="fas fa-folder-open"></i>&nbsp;'.$current_folder_title.'</li>';
      	}
        $parent .= '</ol></nav>';
        echo $parent;
      }



    if ($action == "getTitleFolderName")
    {
    	if ($idOfFileToMove == "") echo "Racine";

      if ($_SESSION['CnxGrp'] == 4) $userID = $userID_request;
      else $userID = $_SESSION['CnxID'];

    	$query  = "SELECT _title ";
    	$query .= "FROM images ";
    	$query .= "WHERE _ID = ".$userID." AND _IDimage = ".$idOfFileToMove." ";
    	$result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    		if ($row[0] == "") echo "Racine";
    		else echo substr($row[0], 1);
    	}
    }



    if ($action == "getShareForm")
    {
      $list_user  = "";
      $list_class = "";
      $list_group = "";

      if ($_SESSION['CnxGrp'] == 4) $userID = $userID_request;
      else $userID = $_SESSION['CnxID'];

      $query  = "SELECT _share ";
      $query .= "FROM images ";
      $query .= "WHERE _ID = ".$userID." AND _IDimage = ".$idOfFileToShare." ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $share_db = json_decode($row[0]);
        foreach ($share_db as $key => $value) {
          if (strpos($value, 'U_') !== false)
          {
            $query1  = "SELECT _name, _fname ";
            $query1 .= "FROM user_id ";
            $query1 .= "WHERE _ID = ".substr($value, 2)." ";
            $result1 = mysqli_query($mysql_link, $query1);
            while ($row1 = mysqli_fetch_array($result1, MYSQLI_NUM)) {
              $name = $row1[0]." ".$row1[1];
            }
            $list_user .= $name.'<span style="display: none;" class="hidden">'.substr($value, 2).'</span>,';
          }
          if (strpos($value, 'C_') !== false)
          {
            $query2  = "SELECT _ident ";
            $query2 .= "FROM campus_classe ";
            $query2 .= "WHERE _IDclass = ".substr($value, 2)." AND _visible = 'O' ORDER BY _code DESC";
            $result2 = mysqli_query($mysql_link, $query2);
            while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
              $nameClass = $row2[0];
            }
            $list_class .= $nameClass.'<span style="display: none;" class="hidden">'.substr($value, 2).'</span>,';
          }
          if (strpos($value, 'G_') !== false)
          {
            $query3  = "SELECT _nom ";
            $query3 .= "FROM groupe_nom ";
            $query3 .= "WHERE _IDgrp = ".substr($value, 2)." ";
            $result3 = mysqli_query($mysql_link, $query3);
            while ($row3 = mysqli_fetch_array($result3, MYSQLI_NUM)) {
              $nameGroup = $row3[0];
            }
            $list_group .= $nameGroup.'<span style="display: none;" class="hidden">'.substr($value, 2).'</span>,';
          }
        }
      }

      echo "
            <!-- Tagit User -->
            <tr>
              <script type=\"text/javascript\">
                $(document).ready(function() {
                  $(\"#UserTags\").tagit({
                    autocomplete: {delay: 0, minLength: 1, source: \"getInfos.php?type=5\", html: 'html'},
                    allowDuplicates: false,
                    singleField: false,
                    fieldName: \"UserTags[]\"
                  });";


            $ar_list_user = explode(",", $list_user);
            foreach($ar_list_user as $val)
            {
              echo "$(\"#UserTags\").tagit(\"createTag\", \"".str_replace('"', "'", $val)."\");";
            }
    echo "

            $(\"#UserTags\").tagit(\"createTag\", \"\");
          });

        </script>

        <td class=\"align-right\" style=\"padding-right: 10px\"><strong>Utilisateur</strong></td>
        <td class=\"align-right\">
          <style>
            /* .ui-autocomplete { max-height: 300px; overflow-y: scroll; overflow-x: hidden;} */
          </style>

          <ul id=\"UserTags\" name=\"UserTags\" class=\"tagit ui-widget ui-widget-content ui-corner-all\">
            <li class=\"tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable\">
              <span class=\"tagit-label\"></span>
            </li>
          </ul>
        </td>
      </tr>


      <!-- Tagit Classe -->
      <tr>
        <td class=\"align-right\" style=\"padding-right: 10px\"><strong>Classe</strong></td>
        <td class=\"align-right\">
          <script type=\"text/javascript\">
            $(document).ready(function() {
              $(\"#ClassTags\").tagit({
                autocomplete: {delay: 0, minLength: 1, source: \"getInfos.php?type=6\", html: 'html'},
                allowDuplicates: false,
                singleField: false,
                fieldName: \"ClassTags[]\"
              });";

              $ar_list_class = explode(",", $list_class);
              foreach($ar_list_class as $val)
              {
                echo '$("#ClassTags").tagit("createTag", "'.str_replace('"', "'", $val).'");';
              }
    echo "
              $(\"#ClassTags\").tagit(\"createTag\", \"\");
            });
          </script>

          <ul id=\"ClassTags\" name=\"ClassTags\" class=\"tagit ui-widget ui-widget-content ui-corner-all\">
            <li class=\"tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable\">
              <span class=\"tagit-label\"></span>
            </li>
          </ul>
        </td>
      </tr>

      <!-- Tagit Groupe -->
      <tr style=\"display: none;\">
        <td class=\"align-right\" style=\"padding-right: 10px\"><strong>Groupe</strong></td>
        <td class=\"align-right\">
          <script type=\"text/javascript\">
            jQuery(document).ready(function() {
              jQuery(\"#GroupTags\").tagit({
                autocomplete: {delay: 0, minLength: 1, source: \"getInfos.php?type=7\", html: 'html'},
                allowDuplicates: false,
                singleField: false,
                fieldName: \"GroupTags[]\"
              });";
              $ar_list_group = explode(",", $list_group);
              foreach($ar_list_group as $val)
              {
                echo "jQuery(\"#GroupTags\").tagit(\"createTag\", \"".str_replace('"', "'", $val)."\");";
              }
    echo "
              jQuery(\"#GroupTags\").tagit(\"createTag\", \"\");
            });
          </script>

          <ul id=\"GroupTags\" name=\"GroupTags\" class=\"tagit ui-widget ui-widget-content ui-corner-all\">
            <li class=\"tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable\">
              <span class=\"tagit-label\"></span>
            </li>
          </ul>
        </td>
      </tr>


    ";


    }



    if ($action == "getSharedShow")
    {
      if ($_SESSION['CnxGrp'] == 4) $userID = $userID_request;
      else $userID = $_SESSION['CnxID'];

      $query  = "SELECT _share ";
      $query .= "FROM images ";
      $query .= "WHERE _ID = ".$userID." AND _IDimage = ".$idOfFile." ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $userNumber   = substr_count($row[0], "U_");
        $groupNumber  = substr_count($row[0], "G_");
        $classNumber  = substr_count($row[0], "C_");
      }

      if ($userNumber != 0) echo "<span class=\"badge\"><i class=\"fa fa-user\"></i>1</span>";


    }


    if ($action == "getNumberOfFilesInFolderToRemove")
    {
      $query  = "SELECT * FROM `images` WHERE `_IDimage` = '".$idFolder."' ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        // Si on supprime un dossier, on supprime les éléments récursifs
        if ($row[7] == "fol")
        {
          $arrayOfValuesToReturn = [1, 0];
          $returned = getRecursiveNumberOfFilesInFolder($idFolder, $arrayOfValuesToReturn);
          if ($returned[0] > 1) $toEcho = $returned[0]." dossiers";
          if ($returned[0] == 1) $toEcho = $returned[0]." dossier";
          if ($toEcho != "" and $returned[1] != 0) $toEcho .= " et ";
          if ($returned[1] > 1) $toEcho .= $returned[1]." fichiers";
          if ($returned[1] == 1) $toEcho .= $returned[1]." fichiers";
          echo $toEcho;
          // echo $returned[0]." dossiers et ".$returned[1]." fichiers";
        }
        else
        {
          echo "cet élément";
        }
      }



    }
    function getRecursiveNumberOfFilesInFolder($idFolder, $arrayOfValuesToReturn)
    {


      $query  = "SELECT * FROM `images` WHERE `_parent` = '".$idFolder."' ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        // Si on supprime un dossier, on supprime les éléments récursifs
        if ($row[7] == "fol")
        {
          $arrayOfValuesToReturn[0] = $arrayOfValuesToReturn[0] + 1;
          $arrayOfValuesToReturn = getRecursiveNumberOfFilesInFolder($row[0], $arrayOfValuesToReturn);
        }
        else
        {
          $arrayOfValuesToReturn[1] = $arrayOfValuesToReturn[1] + 1;
        }

      }
      return $arrayOfValuesToReturn;
    }






    if ($action == "getSharedFilesForm")
    {

    }

    if ($action == "getThumbnailImage")
    {
      showGEDThumbnails($fileID);
    }


    // ---------------------------------------------------------------------------
    // Fonction: Supprime la copie d'un élève
    // IN:		   L'ID de la copie (INT) (POST)
    // OUT: 		 Réussite (1 = oui), (0 = erreur) (INT)
    // ---------------------------------------------------------------------------
    if ($action == "removeCopie")
    {
      // ID_copie
      if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4)
      {
        // On récupère l'ID de l'utilisateur à qui appartient la copie
        $query = "SELECT _ID FROM images WHERE _IDimage = '".$ID_copie."' LIMIT 1 ";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          $user_ID = $row[0];
        }

        // On supprime le fichier
        unlink($DOWNLOAD."/copies/eleves/".$user_ID."/".$ID_copie.".pdf");

        // On supprime l'entrée dans la base de donnée
        $query = "DELETE FROM images WHERE _IDimage = '".$ID_copie."' LIMIT 1 ";
        @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

        // On vérifie que le fichier n'existe plus pour la réponse
        if (!file_exists($DOWNLOAD."/copies/eleves/".$user_ID."/".$ID_copie.".pdf")) echo "1";
        else echo "0";
      }

      // echo $ID_copie;
    }


    // ---------------------------------------------------------------------------
    // Fonction: Donne le lien de partage
    // IN:		   l'ID du fichier (INT)
    // OUT: 		 Lien (TEXT)
    // ---------------------------------------------------------------------------
    if ($action == "getShareLink")
    {
      $ID_file = addslashes($_POST['id_file']);
      $query = "SELECT _IDimage, _ID, _title FROM images WHERE _ID = '".$_SESSION['CnxID']."' AND _IDimage = '".$ID_file."' LIMIT 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

        $current_time = date('Y-m-d H:i:s');
        $chaine = $row[0].";".$row[1].";".$current_time;
        $file_link = openssl_encrypt($chaine, "AES-128-ECB" ,$CRYPT_KEY);


        $url =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        $exploded_url = explode("ged_ajax.php?action=getShareLink", $escaped_url);
        $mail_url = $exploded_url[0]."index.php?item=28&cmde=link_shared&file_id=".urlencode($file_link);

        echo $mail_url;
        // $texte_ckeditor_post = "<a href=\"".$mail_url."\">Télécharger le fichier joint</a>";
      }

    }

  }

?>
