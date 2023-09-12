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
 *		module   : bank_list.php
 *		projet   : Page de liste des opérations bancaires
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 18/09/19
 */

?>


<?php
  if (isset($_GET['year'])) $year = addslashes($_GET['year']);
  else $year = "0";
  if (isset($_GET['search_text'])) $search_text = addslashes($_GET['search_text']);
  else $search_text = "";
  if (isset($_GET['IDeleve'])) $IDeleve = addslashes($_GET['IDeleve']);
  else $IDeleve = 0;
  if (isset($_GET['triField'])) $triField = addslashes($_GET['triField']);
  else $triField = 'date';

  if (isset($_GET['IDclass'])) $IDclass = addslashes($_GET['IDclass']);
  else $IDclass = 0;

  if (isset($_GET['date_1'])) $date_1 = addslashes($_GET['date_1']);
  else $date_1 = date("d/m/Y", strtotime('-1 month'));

  if (isset($_GET['date_2'])) $date_2 = addslashes($_GET['date_2']);
  else $date_2 = "1/".date('m/Y', strtotime('next year'));

  $temp = explode('/', $date_1);
  $date_1 = $temp[2].'-'.$temp[1].'-'.$temp[0];

  $temp = explode('/', $date_2);
  $date_2 = $temp[2].'-'.$temp[1].'-'.$temp[0];




?>
<!-- Calendar -->
<script type="text/javascript" src="script/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
    <b>
  		Liste des opérations bancaires
    </b>
	</div>
</div>




<div class="maincontent">
  <hr>
  <?php if ($_SESSION['CnxAdm'] == 255) { ?>
    <!-- Filtres -->
    <div style="width: 100%;">
      <form id="filtres_form" action="" method="GET">
        <input type="hidden" name="item" value="61">
        <!-- Champ de recherche -->
        <div style="display: inline-block; width: 20%;" class="input-append">
          <input type="text" class="span4" name="search_text" value="<?php echo $search_text; ?>" placeholder="Recherche dans libellé / nom de l'élève / promotion">
          <button type="submit" class="btn">Ok</button>

        </div>

        <div style="display: inline-block; text-align: right; width: 79%;">


          <style>
          .datePicker {
            opacity: 1 !important;
          }

          #fd-but-date {
            color: black;
          }

          #fd-but-date\ 2 {
            color: black;
          }

          </style>



          <div class="control-group" style="display: inline-block; margin-bottom: 3px;" id="date_1">
            <input MaxLength="10" class="format-d-m-y divider-slash" id="date" name="date_1" style="width:90px; height: 18px; padding: 6px 4px 4px 6px; text-align: center" type="text" value="<?php echo date('d/m/Y', strtotime($date_1)); ?>" />
          </div>

          <div class="control-group" style="display: inline-block; margin-bottom: 3px;" id="date_2">
            <input MaxLength="10" class="format-d-m-y divider-slash date_2" id="date 2" name="date_2" style="width:90px; height: 18px; padding: 6px 4px 4px 6px; text-align: center" type="text" value="<?php echo date('d/m/Y', strtotime($date_2)); ?>" />
          </div>




          <!-- Filtre sur les élèves -->
          <select name="IDeleve" onchange="document.forms.filtres_form.submit()">
            <option value="0">Tous les élèves</option>
              <?php
                $old_annee = 0;
                $query = "SELECT DISTINCT bank._IDeleve, user_id._name, user_id._fname, user_id._IDclass FROM bank_data bank JOIN user_id ON bank._IDeleve = user_id._ID ";
                if ($IDclass != 0) $query .= "AND user_id._IDclass = '".$IDclass."' ";
                $query .= "ORDER BY user_id._IDclass DESC, user_id._name, user_id._fname ";
                $result = mysql_query($query);
                while ($row = mysql_fetch_array($result, MYSQL_NUM)) {


                  if ($row[3] != $old_annee) {
                    if ($old_annee != 0) echo '</optgroup>';
                    echo '<optgroup label="'.getClassNameByClassID($row[3]).'">';
                    $old_type = 0;
                  }
                  $old_annee = $row[3];


                  if ($row[0] == $IDeleve) $selected = "selected";
                  else $selected = "";
                  echo "<option value=\"".$row[0]."\" ".$selected.">".getUserNameByID($row[0])."</option>";
                }
              ?>
          </select>



          <!-- Filtre sur les promotions -->
          <select name="IDclass" onchange="document.forms.filtres_form.submit()" style="display: none;">
            <option value="0">Toutes les promotions</option>
              <?php

                $query = "SELECT DISTINCT _IDclass FROM campus_classe WHERE `_visible` = 1 ";
                $result = mysql_query($query);
                while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

                  if ($row[0] == $IDclass) $selected = "selected";
                  else $selected = "";
                  echo "<option value=\"".$row[0]."\" ".$selected.">".getClassNameByClassID($row[0])."</option>";
                }
              ?>
          </select>

          <input type='hidden' id='triField' name='triField' value='<?php echo $triField; ?>'>


          <a href="exports.php?item=<?php echo $_GET['item']; ?>&search_text=<?php echo $search_text; ?>&IDeleve=<?php echo $IDeleve; ?>&date_1=<?php echo $date_1;?>&date_2=<?php echo $date_2; ?>&IDclass=<?php echo $IDclass; ?>&triField=<?php echo $triField; ?>" class="btn btn-default" style="margin-bottom: 10px;"><i class="fa fa-upload"></i></a>
          <?php if ($_SESSION['CnxAdm'] == 255) { ?>
            <a href="#new_modal" role="button" class="btn btn-default" data-toggle="modal" style="margin-bottom: 10px;"><i class="fa fa-plus"></i></a>
          <?php } ?>
        </div>
      </form>
    </div>
    <hr>
  <?php } ?>


  <table style="width: 100%;" class="table table-bordered table-striped" id='data_table'>

    <tr id='table_header'>
      <th>Date <a class='orderBtn' <?php if ($triField == 'date') echo 'style="color: grey;"'; ?> onclick='updateTri("date");'>▲</a></th>
      <th>Intitulé</th>

      <th>Élève <a class='orderBtn' <?php if ($triField == 'student') echo 'style="color: grey;"'; ?> onclick='updateTri("student");'>▲</a></th>
      <th>Promotion</th>
      <th style='text-align: right;'>Montant</th>
      <?php if ($_SESSION['CnxAdm'] == 255) echo "<th></th>"; ?>
    </tr>


    <?php
      $query = "SELECT bank._ID, bank._date, bank._libele, bank._price, bank._IDeleve, bank._attr FROM bank_data bank INNER JOIN `user_id` user WHERE bank._IDeleve = user._ID ";
      // $date_1_query = date('Y-m-d', strtotime($date_1));
      // $date_2_query = date('Y-m-d', strtotime($date_2));
      $query .= "AND bank._date >= '".$date_1."' ";
      $query .= "AND bank._date <= '".$date_2."' ";
      if ($_SESSION['CnxAdm'] != 255) $query .= "AND bank._IDeleve = '".$_SESSION['CnxID']."' ";
      if ($IDeleve != 0) $query .= "AND bank._IDeleve = '".$IDeleve."' ";
      if ($IDclass != 0) $query .= "AND bank._IDeleve IN (SELECT _ID FROM user_id WHERE _IDclass = '".$IDclass."') ";
      if ($search_text != "") $query .= "AND (bank._libele LIKE '%".$search_text."%' OR user._name LIKE '%".$search_text."%' OR user._fname LIKE '%".$search_text."%' OR bank._attr LIKE '%".$search_text."%') ";
      if ($triField == 'date') $query .= 'ORDER BY bank._date ASC ';
      elseif ($triField == 'student') $query .= 'ORDER BY user._name ASC ';
      // elseif ($triField == 'promo') $query .= 'ORDER BY user._IDclass DESC ';

      $result = mysql_query($query);
      $numRows = mysql_num_rows($result);
      $count = 0;
      $somme = 0;
      $currentStudent = 0;
      $currentAmount = 0;

      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        $count++;
        if ($currentStudent == 0) $currentStudent = $row[4];
        if ($row[4] != $currentStudent && $triField == 'student')
        {
          echo '<tr><td colspan="3" style="text-align: right; font-weight: bold;">Total:</td><td style="font-weight: bold;">'.$currentAmount.' €</td>';
          if ($_SESSION['CnxAdm'] == 255) echo '<td colspan="2"></td>';
          echo '</tr>';
          $currentAmount = 0;
          $currentStudent = $row[4];
        }
        $currentAmount += $row[3];

        echo "<tr id=\"tr_".$row[0]."\">";
          echo "<td id=\"date_".$row[0]."\">".date('d/m/Y', strtotime($row[1]))."</td>";
          echo "<td id=\"libelle_".$row[0]."\">".$row[2]."</td>";
          echo "<td id=\"user_name_".$row[0]."\">".getUserNameByID($row[4])."</td>";
          echo "<td id=\"user_promo_".$row[0]."\" style=\"width: 12%;\">".json_decode($row[5], true)['prom_name']."</td>";
          echo "<td id=\"price_".$row[0]."\" style='text-align: right;'>".$row[3]." €</td>";

          echo "<td><div style='width: 50px;'>";

            echo '<a href="bank_certificate.php?IDeleve='.$row[4].'&date_1='.$date_1.'&date_2='.$date_2.'" target="_blank"><i class="fa fa-upload"></i></a>';
            if ($_SESSION['CnxAdm'] == 255)
            {
              echo "&nbsp;<a href=\"#\" onclick=\"editData('".$row[0]."');\"><i class=\"fa fa-pencil\"></i></a>&nbsp;";
              echo "<a href=\"#\" onclick=\"removeData('".$row[0]."');\"><i class=\"fa fa-trash\"></i></a>";
            }
          echo "</div></td>";
        echo "</tr>";


        if ($count == $numRows && $triField == 'student')
        {
          echo '<tr><td colspan="3" style="text-align: right; font-weight: bold;">Total:</td><td style="font-weight: bold;">'.$currentAmount.' €</td>';
          if ($_SESSION['CnxAdm'] == 255) echo '<td colspan="2"></td>';
          echo '</tr>';
        }

	      $somme += $row[3];

      }

		// Somme
		if($numRows && ($triField == 'date' || $triField == 'promo')) {
			echo "<tr><td colspan='4' style='text-align: right;'><strong>Total</strong></td><td style='text-align: right;'><strong>".(int)$somme." €</strong></td><td></td></tr>";
		}

    ?>

  </table>



</div>


<!-- Button to trigger modal -->
<!-- <a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a> -->

<!-- Modal -->
<div id="edit_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Modifier une donnée bancaire</h3>
  </div>
  <div class="modal-body" id="edit_modal_body">

  </div>
  <div class="modal-footer">
    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Fermer</button>
    <button class="btn btn-success" onclick="saveEditModal()">Enregistrer</button>
  </div>
</div>

<?php if ($_SESSION['CnxAdm'] == 255) { ?>
  <!-- Modal create new data -->
  <div id="new_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Créer une donnée bancaire</h3>
    </div>
    <div class="modal-body" id="create_modal_body">
      <label for="date_new"><b>Date:</b><br>
        <input id="date_new" type="date" value="" required>
      </label><br>
      <label for="libelle_new" style="width: 97%;"><b>Libellé:</b><br>
        <input id="libelle_new" type="text" style="width: 100%;" value="" required>
      </label>
      <label for="student_new" style="width: 97%;"><b>Élève:</b><br>
        <select id="student_new" required>
          <?php
          // On récupère la liste des élèves:
          $query = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _adm = 1 AND _IDclass != '0' AND _IDgrp = '1' ORDER BY _IDclass DESC, _name ASC ";
          $result = mysql_query($query);
          $list_eleves = Array();
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            $tempArray = Array();
            $tempArray['ID'] = $row[2];
            $tempArray['name'] = $row[0];
            $tempArray['fname'] = $row[1];
            $tempArray['promo'] = $row[3];
            $list_eleves[] = $tempArray;
          }
          foreach ($list_eleves as $key => $value) {
            if ($value['promo'] != $oldPromo || !isset($oldPromo))
            {
              if (isset($oldPromo)) echo "</optgroup>";
              echo "<optgroup label=\"".getClassNameByClassID($value['promo'])."\">";
              $oldPromo = $value['promo'];
            }
            if ($value['ID'] == $row[4]) $selected = "selected";
            else $selected = "";
            echo "<option value=\"".$value['ID']."\" ".$selected.">".$value['name']." ".$value['fname']."</option>";
          }
          ?>
        </select>
      </label>
      <label for="price_new"><b>Montant:</b><br>
        <input type="number" id="price_new" step="0.01" value="" required>
      </label>
    </div>
    <div class="modal-footer">
      <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Fermer</button>
      <button class="btn btn-success" onclick="saveNewModal()">Enregistrer</button>
    </div>
  </div>
<?php } ?>


<script>
  // ---------------------------------------------------------------------------
  // Fonction: Supprime une entrée dans la liste des relevés bancaires
  // IN:		   L'ID de la donnée (INT)
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  function removeData(idData) {
    if (confirm('Êtes vous sûr de vouloir supprimer cette donnée ?'))
    {
      jQuery.ajax({
        url : 'include/fonction/ajax/bank_operations.php?action=removeData',
        type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
        data : 'idData=' + idData,                                // Les données
        dataType : 'html',                                        // On désire recevoir du HTML
        success : function(code_html, statut){                    // Fonction appelée si succes

          jQuery('#tr_' + idData).fadeOut();
        }
      });
    }
  }

  // ---------------------------------------------------------------------------
  // Fonction: Modifie une entrée dans la liste des relevés bancaires
  // IN:		   L'ID de la donnée (INT)
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  function editData(idData) {
    jQuery.ajax({
      url : 'include/fonction/ajax/bank_operations.php?action=getEditFormData',
      type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
      data : 'idData=' + idData,                                // Les données
      dataType : 'html',                                        // On désire recevoir du HTML
      success : function(code_html, statut){                    // Fonction appelée si succes

        jQuery('#edit_modal_body').html(code_html);
      }
    });
    jQuery('#edit_modal').modal('show');
  }


  // ---------------------------------------------------------------------------
  // Fonction: Sauvegarde les modification d'une donnée
  // IN:		   Les différentes données (MULTI)
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  function saveEditModal() {
    // On récupère les données
    var date_edit = jQuery("#date_edit").val();
    var libelle_edit = jQuery("#libelle_edit").val();
    var student_edit = jQuery("#student_edit").val();
    var price_edit = jQuery("#price_edit").val();
    var ID_edit = jQuery("#ID_edit").val();

    jQuery.ajax({
      url : 'include/fonction/ajax/bank_operations.php?action=applyEditFormData',
      type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
      data : 'ID_edit=' + ID_edit + '&price_edit=' + price_edit + '&student_edit=' + student_edit + '&libelle_edit=' + libelle_edit + '&date_edit=' + date_edit,
      dataType : 'html',                                        // On désire recevoir du HTML
      success : function(code_html, statut){                    // Fonction appelée si succes
        jQuery('#edit_modal').modal('hide');
        updateRow(code_html);
        $.notify({
    			// options
    			icon: 'fa fa-check',
    			message: 'Modification réussie.'
    		},{
    			// settings
    			type: 'success'
    		});
      }
    });
  }


  // ---------------------------------------------------------------------------
  // Fonction: Sauvegarde la nouvelle donnée
  // IN:		   Les différentes données (MULTI)
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  function saveNewModal() {
    // On récupère les données
    var date_edit = jQuery("#date_new").val();
    var libelle_edit = jQuery("#libelle_new").val();
    var student_edit = jQuery("#student_new").val();
    var price_edit = jQuery("#price_new").val();

    // On vide les champs
    jQuery("#date_new").val(<?php echo date('d/m/Y'); ?>);
    jQuery("#libelle_new").val();
    jQuery("#student_new").val(0);
    jQuery("#price_new").val(0);

    jQuery.ajax({
      url : 'include/fonction/ajax/bank_operations.php?action=applyNewFormData',
      type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
      data : 'price_edit=' + price_edit + '&student_edit=' + student_edit + '&libelle_edit=' + libelle_edit + '&date_edit=' + date_edit,
      dataType : 'html',                                        // On désire recevoir du HTML
      success : function(code_html, statut){                    // Fonction appelée si succes
        jQuery('#new_modal').modal('hide');
        jQuery('#table_header').after(code_html);
        $.notify({
    			// options
    			icon: 'fa fa-check',
    			message: 'Création réussie.'
    		},{
    			// settings
    			type: 'success'
    		});
      }
    });
  }

  // Vide les champs du modal de nouvel élément quand on l'ouvre
  jQuery('#new_modal').on('show', function () {
    jQuery('#date_new').val('');
    jQuery('#libelle_new').val('');
    jQuery('#price_new').val('');
  })


  // ---------------------------------------------------------------------------
  // Fonction: Met à jour les données dans le tableau
  // IN:		   ID de la ligne/donnée (MULTI)
  // OUT: 		 Les différentes infos
  // ---------------------------------------------------------------------------
  function updateRow(idData) {
    jQuery.ajax({
      url : 'include/fonction/ajax/bank_operations.php?action=getNewData',
      type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
      data : 'idData=' + idData,
      dataType : 'html',                                        // On désire recevoir du HTML
      success : function(code_html, statut){                    // Fonction appelée si succes
        var obj = jQuery.parseJSON(code_html);
        jQuery("#date_" + idData).html(obj.date);
        jQuery("#libelle_" + idData).html(obj.libelle);
        jQuery("#user_name_" + idData).html(obj.user_name);
        jQuery("#user_promo_" + idData).html(obj.user_promo);
        jQuery("#price_" + idData).html(obj.price);
      }
    });
  }


  // ---------------------------------------------------------------------------
  // Fonction: Met à jour l'affichage en fonction des dates
  // IN:		   -
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  jQuery('.date_2').change(function(){
    document.forms.filtres_form.submit()
  });

  jQuery('#date').change(function(){
    document.forms.filtres_form.submit()
  });

  // ---------------------------------------------------------------------------
  // Fonction: Met à jour l'affichage en fonction du tri choisi
  // IN:		   Le nom du champ sur lequel on veux trier
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  function updateTri(fieldName) {
    jQuery('#triField').attr('value', fieldName);
    document.forms.filtres_form.submit()
  }
</script>
