<?php
/**
 * getUserPictureLink
 *
 * Renvoi le lien de l'image de profile d'un utilisateur
 *
 * @param int $userID The ID of the user we want the profile picture of
 * @return return string
 */
function getUserPictureLink($userID = 0) {
  if (!$userID) $userID = $_SESSION['CnxID'];
  // On récupère l'image de profile
  return "ged_thumbnail.php?action=userImage&fileID=".base64_encode($userID);
}


?>
