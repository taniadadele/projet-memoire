<?php
/**
 * getPagination
 *
 * Permet d'avoir le module de pagination formaté en HTML
 *
 * @param int/string $currentPage La page courante (all si toute les pages sont affichées)
 * @param int $total_element Le nombre total d'éléments
 * @param string $link_infos Le lien qui précède le paramètre de la page
 * @param bool $showAllButton Est-que que l'on affiche le bouton pour afficher toute les pages (le script dois le prendre en compte)
 * @param int $max_elem_par_page Nombre max d'éléments par page
 * @return return string La pagination formatée en HTML
 */
function getPagination($currentPage = 1, $total_element, $link_infos, $showAllButton = false, $max_elem_par_page = 0) {
  global $MAXSHOW;
  $href = '';
  if (!$max_elem_par_page) $max_elem_par_page = $MAXSHOW;
  if ($total_element == 0) return '';
  // Bouton précédent
  if (!getParam('showPrevNextButtonPagination') || $currentPage == 1 || $currentPage == 'all') $prev = '';
  else {
    if ($link_infos) $href = myurlencode($link_infos.'&skpage='.($currentPage - 1).'&currentPage='.($currentPage - 1));
    $prev = '<li class="page-item"><a class="page-link" page="'.($currentPage - 1).'" href="'.$href.'"><i class="fas fa-angle-double-left"></i>&nbsp;précédent</a></li>';
  }

  $choix = '';
  // Le nombre de pages arrondi à l'entier suppérieur
  $nbPages = ceil($total_element / $max_elem_par_page);
  if ($currentPage == 'all') {
    $showAll = true;
    $currentPage = 0;
  }
  else $showAll = false;

  $startPagination = 1;
  if ($currentPage > 5) $startPagination = $currentPage - 4;

  $endPagination = $nbPages;
  if ($nbPages > $currentPage + 5) $endPagination = $currentPage + 4;
  if ($currentPage > $nbPages - 5) $startPagination = $nbPages - ($nbPages - $currentPage) - 4;
  if ($startPagination < 1) $startPagination = 1;
  for ($i = $startPagination; $i <= $endPagination; $i++) {
    if ($link_infos) $href = myurlencode($link_infos.'&skpage='.$i.'&currentPage='.$i);
    if ($currentPage == $i) $choix .= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
  	else $choix .= '<li class="page-item"><a class="page-link" currentPage="'.$i.'" href="'.$href.'">'.$i.'</a></li>';
  }

  // Bouton suivant

  if (!getParam('showPrevNextButtonPagination') || $currentPage == $endPagination || $currentPage == 'all') $next = '';
  else {
    if ($link_infos) $href = myurlencode($link_infos.'&skpage='.($currentPage + 1).'&currentPage='.($currentPage + 1));
    $next = '<li class="page-item"><a class="page-link" page="'.($currentPage + 1).'" href="'.$href.'">suivant&nbsp;<i class="fas fa-angle-double-right"></i></a></li>';
  }
  $toReturn = '';
  $toReturn .= '<nav aria-label="Pagination">';
  	$toReturn .= '<ul class="pagination justify-content-center mb-0">';
  		$toReturn .= $prev.$choix.$next;
      if ($showAll) $active = 'active'; else $active = '';
      if ($link_infos) $href = myurlencode($link_infos.'&skpage=all&currentPage=all');
      if ($showAllButton) $toReturn .= '<li class="page-item '.$active.'"><a class="page-link" page="all" href="'.$href.'">Tout afficher</a></li>';
  	$toReturn .= '</ul>';
  $toReturn .= '</nav>';

  return $toReturn;
}

?>
