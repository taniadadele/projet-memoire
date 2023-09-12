<?php
session_start();
require_once "config.php";
require_once "include/sqltools.php";
include_once("php/functions.php");
include 'include/fonction.php';
// connexion à la base de données
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

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

$image = @base64_decode($_GET["image"]);
$image = substr($image, strpos($image, "_")+1);


if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION['CnxID'] != "")
{
	if (!isset($_GET['action']))
	{
		// Récupère le fichier
		$new_image = substr($image, 0, strrpos($image, '.'));

		$query  = "SELECT _IDimage, _type, _title, _ID ";
		$query .= "FROM images ";
		if ($_SESSION['CnxAdm'] != 255)
		{
			$query .= "WHERE _IDimage = ".$new_image." AND ( `_ID` = '".$_SESSION['CnxID']."' OR ";
			// ")"
			$groupeSelector = "";
			$groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);
			foreach ($groupOfUser as $key => $value) {
				$groupeSelector .= "_share LIKE '%G_".$key."%' OR ";
			}

			$query .= $groupeSelector;
			$query .= "_share LIKE '%U_".$_SESSION['CnxID']."%' ";
			$query .= "OR _share LIKE '%C_".$_SESSION['CnxClass']."%' ";
			$query .= ") ";
		}
		else $query .= "WHERE _IDimage = ".$new_image." ";


		$query .= "LIMIT 1 ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ($result) ? mysqli_fetch_row($result) : 0 ;

		$extension_file = substr($row[2], strrpos($row[2], '.'));

		if ($row[1] == "user") $type = "user_ged";
		else $type = $row[1];

		$file = "download/".$type."/files/".$row[3]."/".$row[0].$extension_file;

		header('Content-Type: application/force-download');
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $row[2])));
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile("$file");
	}

	// Si on télécharge un fichier envoyé par mail
	elseif ($_GET['action'] == "link_file")
	{
		$file_token = $_GET['token'];
		$decrypted_chaine = openssl_decrypt($file_token, "AES-128-ECB" ,$CRYPT_KEY);
    $temp = explode(';', $decrypted_chaine);

    $fileID    = $temp[0];
    $userID    = $temp[1];
    $timeShare = $temp[2];


		if (time() <= strtotime($timeShare.' + '.$TIMEOUT_GED.' minute'))
    {
			$query = "SELECT _ext, _title FROM images WHERE _IDimage = '".$fileID."' AND _ID = '".$userID."' LIMIT 1 ";
			$result = mysqli_query($mysql_link, $query);
			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$fileExt  = $row[0];
				$fileName = $row[1];
			}

			$file = "download/ged/files/".$userID."/".$fileID.".".$fileExt;

			header('Content-Type: application/force-download');
			header("Content-Transfer-Encoding: binary");
			header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $fileName)));
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');
			readfile("$file");
			// echo $file;
		}

	}

	// Si on télécharge un fichier envoyé par mail
	elseif ($_GET['action'] == "syllabus")
	{
		// Récupère le fichier
		$new_image = substr($image, 0, strrpos($image, '.'));

		$query  = "SELECT _IDimage, _type, _title, _ID ";
		$query .= "FROM images ";
		$query .= "WHERE _IDimage = ".$new_image." AND _type = 'syllabus' ";
		$query .= "LIMIT 1 ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ($result) ? mysqli_fetch_row($result) : 0 ;

		$extension_file = substr($row[2], strrpos($row[2], '.'));


		$file = "download/syllabus/files/".$row[3]."/".$row[0].$extension_file;

		$fileName = $row[2];

		header('Content-Type: application/force-download');
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $fileName)));
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile("$file");
		// echo $file;
	}




	// echo $file;
}
?>
