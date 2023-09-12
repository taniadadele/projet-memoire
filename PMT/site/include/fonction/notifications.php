<?php
// Page qui permet d'afficher les notifications en haut à droite
function showNotification($link, $texte, $style = 'primary', $icon = 'fa-file-alt') {
  $toReturn = '';
  $toReturn .= '<a class="dropdown-item d-flex align-items-center" href="'.$link.'">';
    $toReturn .= '<div class="mr-3">';
      $toReturn .= '<div class="icon-circle bg-'.$style.'">';
        $toReturn .= '<i class="'.$icon.' text-white"></i>';
      $toReturn .= '</div>';
    $toReturn .= '</div>';
    $toReturn .= '<div>';
      // $toReturn .= '<div class="small text-gray-500">December 12, 2019</div>';
      $toReturn .= '<span class="font-weight-bold">'.$texte.'</span>';
    $toReturn .= '</div>';
  $toReturn .= '</a>';
  return $toReturn;
}


$totalNotif = 0;
// Alerte de l'utilisateur en cas de cours en attente
$query2 = "SELECT `_ID` FROM `edt_data` WHERE `_ID` = '".$_SESSION['CnxID']."' AND `_visible` = 'O' AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') AND `_etat` = '4' ";
$result2 = mysqli_query($mysql_link, $query2);
$numberOfResults = mysqli_num_rows($result2);
if ($numberOfResults != 0)
{
	if ($numberOfResults == 1)
	{
    $link = 'index.php?item=29&action=confirmEvents&lessonStatus=waiting';
    $texte = ''.$numberOfResults.'&nbsp;cours en attente de validation';
    echo showNotification($link, $texte, 'primary', 'fas fa-question');
	}
	else
	{
    $texte = $numberOfResults.'&nbsp;cours en attente de validation';
    $link = 'index.php?item=29&action=confirmEvents&lessonStatus=waiting';
    echo showNotification($link, $texte, 'primary', 'fas fa-question');
	}
}
$totalNotif += $numberOfResults;

// Alerte de l'admin en cas de cours refusés
if ($_SESSION['CnxAdm'] == 255)
{
	$query2 = "SELECT `_IDx` FROM `edt_data` WHERE `_visible` = 'O' AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') AND `_etat` = '6' ";
  $result2 = mysqli_query($mysql_link, $query2);
	$numberOfResults = mysqli_num_rows($result2);
	if ($numberOfResults > 0)
	{
		if ($numberOfResults == 1)
		{
      $link = 'index.php?item=29&action=confirmEvents&IDprof=0&remove=refused&lessonStatus=refused';
      $texte = $numberOfResults.'&nbsp;cours en attente de validation a été refusé';
      echo showNotification($link, $texte, 'danger', 'fa fa-times');
		}
		else
		{
      $link = 'index.php?item=29&action=confirmEvents&IDprof=0&remove=refused&lessonStatus=refused';
      $texte = $numberOfResults.'&nbsp;cours en attente de validation ont été refusés';
      echo showNotification($link, $texte, 'danger', 'fa fa-times');
		}
	}
}
$totalNotif += $numberOfResults;

// Alerte de l'admin en cas de cours acceptés
if ($_SESSION['CnxAdm'] == 255)
{
	$query2 = "SELECT `_IDx` FROM `edt_data` WHERE `_visible` = 'O' AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') AND `_etat` = '5' ";
  $result2 = mysqli_query($mysql_link, $query2);
	$numberOfResults = mysqli_num_rows($result2);
	if ($numberOfResults > 0)
	{
		if ($numberOfResults == 1)
		{
      $link = 'index.php?item=29&action=confirmEvents&IDprof=0&remove=accepted&lessonStatus=accepted';
      $texte = $numberOfResults.'&nbsp;cours en attente de validation a été accepté';
      echo showNotification($link, $texte, 'success', 'fas fa-check');
		}
		else
		{
      $link = 'index.php?item=29&action=confirmEvents&IDprof=0&remove=accepted&lessonStatus=accepted';
      $texte = $numberOfResults.'&nbsp;cours en attente de validation ont été acceptés';
      echo showNotification($link, $texte, 'success', 'fas fa-check');
		}
	}
}
$totalNotif += $numberOfResults;

// Alerte de l'admin en cas de fin de traitement des copies
if ($_SESSION['CnxAdm'] == 255)
{
	if (getParam('isCurrentlyWorking') == 2 && getParam('importCopies') == 1)
	{
    $link = 'index.php?item=60&cmde=barcode';
    $texte = 'Le traitement des copies est terminé';
    echo showNotification($link, $texte, 'success', 'fas fa-bell');
    $totalNotif += 1;
	}
}
elseif (getParam('importCopies') == 1)
{
	$query = "SELECT `_ID` FROM `images` WHERE `_ID` = '".$_SESSION['CnxID']."' AND `_date` BETWEEN DATE_SUB('".date('Y-m-d H:i:s')."', INTERVAL 1 DAY) AND '".date('Y-m-d H:i:s')."' ";
	$result = mysqli_query($mysql_link, $query);
	$numberOfResults = mysqli_num_rows($result);
	if ($numberOfResults > 0)
	{
    $link = 'index.php?item=28&cmde=myWork';
    $texte = $numberOfResults.'&nbsp;nouvelle(s) copie(s) a/on été ajoutée(s)';
    echo showNotification($link, $texte, 'success', 'fas fa-bell');
    $totalNotif += 1;
	}
}

// Si aucune nouvelle notif, on l'affiche
if ($totalNotif == 0) {
	echo '<a class="dropdown-item d-flex align-items-center" href="#">';
		echo '<div class="text-center w-100">Aucunes nouvelles notifications</div>';
	echo '</a>';
}

echo '<span id="notif_count_hidden" style="display: none;" notif_count="'.$totalNotif.'"></span>';


?>
