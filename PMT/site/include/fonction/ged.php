<?php
/* -----------------------------------------------------*/
/*                                                      */
/*          Fonctions utiles pour la page GED           */
/*                                                      */
/*------------------------------------------------------*/




/* Fonction qui génère le fil d'ariane */
function create_breadcrumbs ($current_folder_id, $user_id, $cmde) {
  global $mysql_link;
  $parent = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
  // Si on est à la racine
  if ($current_folder_id == 0 || $current_folder_id == "")
  {
    $parent .= '<li class="breadcrumb-item active"><i class="fas fa-folder-open"></i>&nbsp;Racine</li>';
  }
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

    $parent .= '<li class="breadcrumb-item"><a href="index.php?item=28&cmde='.$cmde.'&path=0"><i class="fas fa-folder"></i>&nbsp;Racine</a></li>';
    $temp = '';
    while ($current_parent != "")
    {
      $query  = "SELECT _title, _parent ";
      $query .= "FROM images ";
      $query .= "WHERE _ID = ".$user_id." AND _IDimage = ".$current_parent." ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $folder_name = substr($row[0], 1);
        $temp = '<li class="breadcrumb-item"><a href="index.php?item=28&cmde='.$cmde.'&path='.$current_parent.'"><i class="fas fa-folder"></i>&nbsp;'.$folder_name.'</a></li>'.$temp;
        $current_parent = $row[1];
      }
    }
    $parent .= $temp;
    $parent .= '<li class="breadcrumb-item active"><i class="fas fa-folder-open"></i>&nbsp;'.$current_folder_title.'</li>';
  }
  $parent .= '</ol></nav>';
  return $parent;
}







?>
