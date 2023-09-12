<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : log_mail.php
 *		projet   : la page de visualisation des logs d'envoi de mails automatique
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 14/10/19
 *		modif    :
 */

?>



<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<strong>Logs d'envois des mails</strong>
	</div>
</div>

<div class="maincontent">
  <table class="table table-striped" style="width: 100%;">
    <tr>
      <th style="width: 1%;">Date</th>
      <th style="width: 10%;">Type</th>
      <th style="width: 1%;">Nb dest</th>
      <th>Destinataires</th>
    </tr>

    <?php
      $query = "SELECT _date, _type, _dest_count, _dest FROM mail_log WHERE 1 ORDER BY _id DESC LIMIT 100 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<tr>";
          echo "<th style=\"width: 1%;\">".date('d/m/Y H:i', strtotime($row[0]))."</th>";
					// 1 = Récap des cours
					// 2 = Demande de confirmation
					// 3 = Ajout de cours
					// 4 = Modif de cours
					// 5 = Suppression de cours
					// 6 = Email
					// 7 = Postit
					switch ($row[1]) {
						case '1':
							echo "<td style=\"width: 10%;\">EDT de la semaine à venir</td>";
							break;
						case '2':
							echo "<td style=\"width: 10%;\">Demande de confirmation</td>";
							break;
						case '3':
							echo "<td style=\"width: 10%;\">Ajout de cours</td>";
							break;
						case '4':
							echo "<td style=\"width: 10%;\">Modif de cours</td>";
							break;
						case '5':
							echo "<td style=\"width: 10%;\">Suppression de cours</td>";
							break;
						case '6':
							echo "<td style=\"width: 10%;\">Email</td>";
							break;
						case '7':
							echo "<td style=\"width: 10%;\">Postits</td>";
							break;
					}
					$alreaduShown_email = ';';
					$listEmails = explode(';', $row[3]);
					$count = 0;
					foreach ($listEmails as $key => $value) {
						if (!isset($value) || $value == '') continue;
						if (strpos($alreaduShown_email, $value) === false)
						{
							if ($value != "" && $value > 0 && getUserNameByID(getUserIDByEmail($value)) != "") $count++;
							elseif ($value != "") $count++;
							$alreaduShown_email .= $value.';';
						}
					}
          echo "<td>".$count."</td>";
          echo "<td>";
						$alreaduShown_email = ';';
            $listEmails = explode(';', $row[3]);
            foreach ($listEmails as $key => $value) {
							if (isset($value) && $value != '')
							if (strpos($alreaduShown_email, $value) === false)
							{
								if (isUserTeacher(getUserIDByEmail($value))) $badge_color = 'btn-success';
								else $badge_color = "";
	              if ($value != "" && getUserNameByID(getUserIDByEmail($value)) != "") echo "<span class=\"badge ".$badge_color."\"><i class=\"fa fa-user\"></i>&nbsp;".getUserNameByID(getUserIDByEmail($value))."</span>&nbsp;";
								elseif ($value != "") echo "<span class=\"badge\"><i class=\"fa fa-user\"></i>&nbsp;".$value."</span>&nbsp;";
								$alreaduShown_email .= $value.';';
							}

            }
          echo "</td>";
        echo "</tr>";
      }
    ?>
  </table>
</div>
