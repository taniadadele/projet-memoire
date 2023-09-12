<?php
/**
 * setLog
 *
 * Écris un log dans les fichiers de logs
 *
 * @param string $type Le type de log (warn, info, debug, fatal, error, trace)
 * @param string $name Le nom du log
 * @param int|string|array $value Les valeurs du log, peux être un int, une string ou un array
 * @return return type
 */
function setLog($type, $name, $value)
{
  global $logger;


  // -------------- Les types de logs -------------- \\
  // | Level | Severity | Description                                                                                      |
  // | FATAL | Highest  | Very severe error events that will presumably lead the application to abort.                     |
  // | ERROR | ...      | Error events that might still allow the application to continue running.                         |
  // | WARN  | ...      | Potentially harmful situations which still allow the application to continue running.            |
  // | INFO  | ...      | Informational messages that highlight the progress of the application at coarse-grained level.   |
  // | DEBUG | ...      | Fine-grained informational events that are most useful to debug an application.                  |
  // | TRACE | Lowest   | Finest-grained informational events.                                                             |


  $log_values = array(
    'userID'    => $_SESSION['CnxID'],
    'userName'  => getUserNameByID($_SESSION['CnxID']),
    'userIP'    => $_SESSION['CnxIP'],
    'name'      => $name,
    'values'    => $value
  );

  // $logger->info('USER ID: '.$_SESSION['CnxID'].' - USER NAME: '.getUserNameByID($_SESSION['CnxID']));
  // $logger->info($log_values);
  switch (strtoupper($type)) {
    case 'FATAL': $logger->fatal($log_values); break;
    case 'ERROR': $logger->error($log_values); break;
    case 'WARN':  $logger->warn($log_values);  break;
    case 'INFO':  $logger->info($log_values);  break;
    case 'DEBUG': $logger->debug($log_values); break;
    case 'TRACE': $logger->trace($log_values); break;
  }
}
?>
