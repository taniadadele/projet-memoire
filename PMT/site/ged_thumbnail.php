<?php
  session_start();
  include_once("php/functions.php");
  include 'include/fonction.php';
  $fileID = $_GET['fileID'];
  $realFileID = @base64_decode($fileID);
  //---------------------------------------------------------------------------
  function remove_magic_quotes($array)
  {
  	/*
  	 * fonction :	nettoyage des \ dans une chaîne
  	 * in :		$array : tableau de valeurs
  	 */

  	// On n'exécute la boucle que si nécessaire
  	if ( $array AND get_magic_quotes_gpc() == 1 )
  		foreach($array as $key => $val) {
  			// Si c'est un array, recursion de la fonction, sinon suppression des slashes
  			if ( is_array($val) )
  				remove_magic_quotes($array[$key]);
  			else
  				if ( is_string($val) )
  					$array[$key] = stripslashes($val);
  			}

  	return $array;
  }
  //---------------------------------------------------------------------------
  require_once "config.php";
  require_once "include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

  $seconds_to_cache = 604800;
  $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
  header("Expires: $ts");
  header('Cache-Control: public');
  header("Pragma: cache");


  if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
  else $action = "";

  if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {
    if ($action == "")
    {
      $query = "SELECT `_ext`, `_ID`, `_type` FROM `images` WHERE `_IDimage` = '".$realFileID."' AND ( `_ID` = '".$_SESSION['CnxID']."' OR ";
      // ")"
      $groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);

      $groupeSelector = "";
      foreach ($groupOfUser as $key => $value) {
        $groupeSelector .= "_share LIKE '%G_".$key."%' OR ";
      }

      $query .= $groupeSelector;
      $query .= "_share LIKE '%U_".$_SESSION['CnxID']."%' ";
      $query .= "OR _share LIKE '%C_".$_SESSION['CnxClass']."%' ";
      $query .= ")";

      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        header('Content-Type: image/jpeg');
        $file = "download/ged/files/".$row[1]."/thumbnail/".$realFileID.".".$row[0];
        readfile($file);
      }
    }


    if ($action == "user_ged")
    {
      $query = "SELECT `_ID` FROM `images` WHERE `_IDimage` = '".$realFileID."' LIMIT 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $userID = $row[0];
        $userClass = getClassIDByUserID($row[0]);
      }
      $query = "SELECT `_ext`, `_ID`, `_type` FROM `images` WHERE `_IDimage` = '".$realFileID."' AND ( `_ID` = '".$userID."' OR ";
      // ")"
      $groupOfUser = getGroupsByIDuser($userID);

      $groupeSelector = "";
      foreach ($groupOfUser as $key => $value) {
        $groupeSelector .= "_share LIKE '%G_".$key."%' OR ";
      }

      $query .= $groupeSelector;
      $query .= "_share LIKE '%U_".$userID."%' ";
      $query .= "OR _share LIKE '%C_".$userClass."%' ";
      $query .= ")";

      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        header('Content-Type: image/jpeg');
        $file = "download/user_ged/files/".$row[1]."/thumbnail/".$realFileID.".".$row[0];
        readfile($file);
      }
    }

    if ($action == "userImage")
    {
      $file = 'download/photo/eleves/'.$realFileID.".jpg";
      if (!file_exists($file)) $file = 'download/photo/eleves/no_picture_user.png';
      header('Content-Type: image/jpeg');
      readfile($file);
    }

    if ($action == "postitImage")
    {
      $file_index    = addslashes($_GET['index']);
      $fileName      = addslashes($_GET['file_name']);
      $file = "download/post-it/".$file_index."_".$fileName;
      $temp = explode('.', $fileName);
      $ext = end($temp);
      if (!file_exists($file) || ($ext != "jpg" && $ext != "jpeg" && $ext != "JPG" && $ext != "JPEG" && $ext != "png" && $ext != "PNG" && $ext != "gif" && $ext != "GIF"))
      {

        $file = "images/filetype/".$ext.'.png';
      }
      header('Content-Type: image/jpeg');
      readfile($file);
    }


    if ($action == "syllabusImage")
    {
      $file_index    = addslashes($_GET['index']);
      $fileName      = addslashes($_GET['file_name']);
      $syllabusID      = addslashes($_GET['syllabusID']);
      $fileext  = substr($fileName, strrpos($fileName, '.'));
      $file = 'download/syllabus/files/'.$syllabusID.'/thumbnail/'.$file_index.$fileext;
      $temp = explode('.', $fileName);
      $ext = end($temp);
      // Si la miniature n'existe pas alors on prend l'image du type de fichier
      if (!file_exists($file)) $file = "images/filetype/".str_replace('.', '', $fileext).'.png';
      header('Content-Type: image/jpeg');
      readfile($file);
    }

  }
?>
