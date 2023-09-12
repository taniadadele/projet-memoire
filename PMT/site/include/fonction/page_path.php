<?php
/**
 * getBackLink
 *
 * Donne le lien de retour à la page précédente
 *
 * @param int $currentItem L'item de la page actuelle
 * @param string $currentCmde Le cmde de la page actuelle
 * @param bool $showPipe Est-ce que l'on affiche la séparation ' | ' après le lien
 * @return return type
 */
function getBackLink($currentItem = 0, $currentCmde = '', $showPipe = true) {
  global $msg;
  if (!isset($_SESSION['page_path'])) return '';
  if (count($_SESSION['page_path']) <= 1) return '';
  $last_page_number = count($_SESSION['page_path']) - 2;
  $last_page = $_SESSION['page_path'][$last_page_number];
  $toReturn = '<a href="index.php?item='.$last_page['item'].'&cmde='.$last_page['cmde'].'&idmenu='.$last_page['idmenu'].'&back=true"><i class="fas fa-chevron-left"></i>&nbsp;'.$msg->getTrad('_BACK').'</a>';
  if ($showPipe) $toReturn .= '&nbsp;|&nbsp;';
  return $toReturn;
}

/**
 * storePagePath
 *
 * Fonction pour stocker la page actuelle dans le fil d'ariane
 *
 * @param int $item L'item de la page actuelle
 * @param string $cmde Le cmde de la page actuelle
 * @param int $idmenu L'idmenu de la page actuelle
 * @param bool $back Est-ce que l'utilisateur à appuyé sur 'Retour'
 * @return return type
 */
function storePagePath($item = 0, $cmde = 0, $idmenu = 0, $back = false) {
  // Si on a cliqué sur 'Retour'
  if ($back) {
    $page_path = $_SESSION['page_path'];
    $last_page = end($page_path);
    if ($last_page['item'] != $item || $last_page['cmde'] != $cmde) {
      $last_page_number = count($_SESSION['page_path']) - 1;
      unset($_SESSION['page_path'][$last_page_number]);
    }
  }

  if ($item == 0) return '';

  if (!isset($_SESSION['page_path'])) $_SESSION['page_path'][] = array('item' => $item, 'cmde' => $cmde, 'idmenu' => $idmenu);
  else {
    $page_path = $_SESSION['page_path'];
    $last_page = end($page_path);
    if ($last_page['item'] != $item || $last_page['cmde'] != $cmde) {
      $page_path[] = array(
        'item' => $item,
        'cmde' => $cmde,
        'idmenu' => $idmenu
      );
      $_SESSION['page_path'] = $page_path;
    }
  }
}


?>
