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
 *		module   : stage_hours_view.php
 *		projet   : Page de visualisation/modification des heures de stages des étudiants
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 14/04/20
 */


if (isset($_POST['IDclass'])) $IDclass = addslashes($_POST['IDclass']);

$list_periodes = json_decode(getParam('periodeList'), TRUE);
$year = getParam('START_Y');



if ($_GET['action'] == 'submithours')
{

  foreach ($_POST as $key => $value) {
    foreach ($value as $periode_temp => $value_periode) {
      // On vérifie si il y a déjà une valeur et si il n'y en a pas alors on la crée
      $query_hours = 'SELECT _stage_hours FROM notes_text WHERE _period = '.$periode_temp.' AND _IDeleve = '.$key.' AND _year = '.$year.' LIMIT 1 ';
      $result_hours = mysql_query($query_hours);
      if (mysql_num_rows($result_hours) != 0) $query = 'UPDATE notes_text SET _stage_hours = '.$value_periode.' WHERE _period = '.$periode_temp.' AND _IDeleve = '.$key.' AND _year = '.$year.' ';
      else $query  = "insert into notes_text values('".$key."', '".$_SESSION["CnxID"]."', '0', NOW(), '$IDclass', '0', '$year', '$periode_temp', '', 'N', '$value_periode', 0, 0, '') ";
      mysql_query($query, $mysql_link);
    }



  }
}



?>


<!-- Titre -->
<div class="maintitle">
	<div style="text-align: center; font-weight: bold;">
    Liste des heures de stage
	</div>
</div>


<!-- Contenu -->
<div class="maincontent">
  <div style="text-align:center;">

    <!-- Champ de recherche -->
    <form id="formulaire" action="index.php?item=<?php echo $item; ?>" method="post">
      <div style="/* display: inline-block; float: right; */ width: 100%; text-align: right;">
        <!-- Select de la classe -->
        <select name='IDclass' onchange='document.forms.formulaire.submit()'>
          <?php
            $query = 'SELECT _ident, _IDclass FROM campus_classe WHERE _visible = "O" ORDER BY _code ASC ';
            $result = mysql_query($query);
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
              if ($IDclass == $row[1]) $selected = 'selected';
              else $selected = '';
              echo '<option value="'.$row[1].'" '.$selected.'>'.$row[0].'</option>';
            }
          ?>
        </select>

        <a href="exports.php?item=<?php echo $_GET['item']; ?>&IDclass=<?php echo $IDclass; ?>" class="btn btn-default" style="color: black; margin-bottom: 10px; margin-left: 5px;"><i class="fa fa-upload"></i></a>
      </div>
    </form>

    <hr style="width:100%;" />
    <?php if ($_GET['action'] == 'submithours') { ?>
      <div class="alert alert-success">Modifications enregistrées</div>
    <?php } else { ?>
      <div class="alert alert-info">N'oubliez pas d'enregistrer vos modifications</div>
    <?php } ?>

    <form action="index.php?item=<?php echo $item; ?>&action=submithours" method="post">
      <input type="hidden" name="IDclass" value="<?php echo $IDclass; ?>">

      <table class="table table-striped">
        <tr>
          <th>Nom</th>

          <?php
            foreach ($list_periodes as $key => $value) {
              echo '<th>'.$value.'</th>';
            }
          ?>
          <th>Année</th>
        </tr>


        <?php
          $query = 'SELECT _ID, _name, _fname FROM user_id WHERE _adm = 1 AND _IDclass = '.$IDclass.' ';
          $result = mysql_query($query);
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {



            echo '<tr>';
              echo '<td>'.$row[1].' '.$row[2].'</td>';

              foreach ($list_periodes as $key => $value) {
                $hours_value = 0;
                $query_hours = 'SELECT _stage_hours FROM notes_text WHERE _period = '.$key.' AND _IDeleve = '.$row[0].' AND _year = '.$year.' LIMIT 1 ';
                $result_hours = mysql_query($query_hours);
                while ($row_hours = mysql_fetch_array($result_hours, MYSQL_NUM)) {
                  $hours_value = $row_hours[0];
                }
                echo '<td><input name="'.$row[0].'['.$key.']" type="number" value="'.$hours_value.'"></td>';
              }
              $hours_value = 0;
              $query_hours = 'SELECT _stage_hours FROM notes_text WHERE _period = 0 AND _IDeleve = '.$row[0].' AND _year = '.$year.' LIMIT 1 ';
              $result_hours = mysql_query($query_hours);
              while ($row_hours = mysql_fetch_array($result_hours, MYSQL_NUM)) {
                $hours_value = $row_hours[0];
              }

              echo '<td><input name="'.$row[0].'[0]" type="number" value="'.$hours_value.'"></td>';

            echo '</tr>';
          }
        ?>
      </table>

      <div style="float: right;">
        <input type="submit" class="btn btn-success" value="Enregistrer">
      </div>
    </form>
  </div>
</div>
