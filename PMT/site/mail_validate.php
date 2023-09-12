<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/
/*
 *		module   : mail_validate.php
 *		projet   : Page de validation de compte par mail
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 03/02/19
 *		modif    : 03/02/19 - par Thomas Dazy
 */

$validation = "error";

$query  = "SELECT * FROM `user_id` WHERE `_adm` = '-1' ";
$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

if(mysqli_num_rows($req) > 0) // Si existe alors mise Ã  jour
{

  while($row = mysqli_fetch_assoc($req))
  {
    if ($row['_adm'] == '-1' && crypt($row['_ID'], "FollowTheWhiteRabbitNeo") == $_GET['account'])
    {
      $newValue = getParam("adminAccountValidation");
      if ($newValue == 1) $newValue = 0;
      else if ($newValue == 0) $newValue = 1;
      $IDToSearch = $row["_ID"];
      $query2 = "UPDATE `user_id` SET `_adm` = '".$newValue."' WHERE `user_id`.`_ID` = '".$IDToSearch."'; ";
      $req2 = mysqli_query($mysql_link, $query2) or die('Erreur SQL !<br>'.$query2.'<br>'.mysqli_error($mysql_link));
      $validation = "success";
    }
  }
}

if ($validation == "error")
{
  $query  = "SELECT * FROM `user_id` WHERE `_adm` = '0' ";
  $req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  if(mysqli_num_rows($req) > 0) // Si existe alors mise Ã  jour
  {
    while($row = mysqli_fetch_assoc($req))
    {
      if (crypt($row['_ID'], "FollowTheWhiteRabbitNeo") == $_GET['account']) $validation = "already";
    }
  }
}

?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<strong>Validation de l'email</strong>
	</div>
</div>

<div class="maincontent" style="text-align: center;">
  <table class="width100">

    <?php if ($validation == "error") { ?>
      <tr style="color: red;">Il y a eu une erreur, merci de réessayer.</tr>
    <?php } else if($validation == "already") { ?>
      <tr>Votre adresse mail a déjà été validé.</tr>
    <?php } else if(getParam("adminAccountValidation") == "1") { ?>
      <tr>Merci, l'administrateur va vérifier et activer votre compte.</tr>
    <?php } else { ?>
      <tr>Merci, votre compte est disponible.</tr>
    <?php } ?>

  </table>
</div>
