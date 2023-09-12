<?php
// Classe de gestion/affichage du calendrier
class Calendar {

  /**
   * undocumented function summary
   *
   * Undocumented function long description
   *
   * @param type var Description
   * @return return type
   */
  function newEvent($value='') {
    return 'coucou';
  }


  /**
   * undocumented function summary
   *
   * Undocumented function long description
   *
   * @param type var Description
   * @return return type
   */
  function getEvents($start = '', $end = '') {
    // global $db;

    $events = array(
      array(
        'title' => 'Event 1',
        'start' => '2020-10-26T09:00:00',
        'end' => '2020-10-26T18:00:00',
        'color' => '#F5A498'
      ),
      array(
        'title' => 'Event 2',
        'start' => '2020-10-26',
        'end' => '2020-10-28',
        'color' => '#73CDDE'
      )
    );

    return json_encode($events, JSON_UNESCAPED_UNICODE);
  }

}


?>
