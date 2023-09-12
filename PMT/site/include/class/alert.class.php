<?php

/**
 * Gestion/affichage des alertes
 */
class Alert
{
  // Affichage d'une alerte de type success
  function success($title, $content, $icon = '') {
    if ($icon == '') $icon = 'check';
    $this->show($title, $content, 'success', $icon);
  }
  // Affichage d'une alerte de type error
  function error($title, $content, $icon = '') {
    if ($icon == '') $icon = 'times';
    $this->show($title, $content, 'danger', $icon);
  }
  // Affichage d'une alerte de type info
  function info($title, $content, $icon = '') {
    if ($icon == '') $icon = 'info';
    $this->show($title, $content, 'info', $icon);
  }
  // Affichage d'une alerte de type warning
  function warning($title, $content, $icon = '') {
    if ($icon == '') $icon = 'exclamation';
    $this->show($title, $content, 'warning', $icon);
  }


  /**
   * showAlert
   *
   * Affiche une alerte
   *
   * @param string $title Le titre de l'alerte
   * @param string $content Le contenu de l'alerte
   * @param string $type Le type d'alerte (success, danger, info...)
   * @param string $icon L'icone de l'alerte (défini par défaut en fonction du  type) (sans le 'fa-')
   * @return return string
   */
  function show($title, $content, $type = 'success', $icon = '') {
    $toReturn = '';
    $toReturn .= '<div class="col-12 mb-4">';
      $toReturn .= '<div class="card border-left-'.$type.' shadow h-100 py-2">';
        $toReturn .= '<div class="card-body">';
          $toReturn .= '<div class="row no-gutters align-items-center">';
          $toReturn .= '<div class="col mr-2">';
            $toReturn .= '<div class="text-xs font-weight-bold text-'.$type.' text-uppercase mb-1">'.$title.'</div>';
              $toReturn .= '<div class="h5 mb-0 font-weight-bold text-gray-800">'.$content.'</div>';
            $toReturn .= '</div>';
            $toReturn .= '<div class="col-auto">';
              $toReturn .= '<i class="fas fa-'.$icon.' fa-2x text-gray-300"></i>';
            $toReturn .= '</div>';
          $toReturn .= '</div>';
        $toReturn .= '</div>';
      $toReturn .= '</div>';
    $toReturn .= '</div>';
    echo  $toReturn;
  }
}


?>
