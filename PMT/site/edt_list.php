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
 *		module   : edt_list.htm
 *		projet   : Page de visualisation des éléments de l'edt en mode liste avec filtres
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 09/04/19
 *    modif    :
 *					 08/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7
 */

  $IDpromotion = $IDmatiere = $IDpole = $IDprof = $type_UV = '';
  if (isset($_GET['IDpromotion'])) $IDpromotion = $_GET['IDpromotion'];
  elseif($_SESSION['CnxGrp'] == 1 && !getParam('edt_visible_par_tous')) $IDpromotion = $_SESSION['CnxClass'];
  if (isset($_GET['IDmatiere'])) $IDmatiere = $_GET['IDmatiere'];
  if (isset($_GET['IDpole'])) $IDpole = $_GET['IDpole'];
  if (isset($_GET['IDprof'])) $IDprof = $_GET['IDprof'];
  if (isset($_GET['type_UV'])) $type_UV = $_GET['type_UV'];

  if (isset($_GET['lessonStatus'])) $lessionStatus = $_GET['lessonStatus'];
  if (isset($_GET['idsyllabus'])) $idSyllabus = $_GET['idsyllabus'];


  if (isset($_GET['submit']) && $_GET['submit'] == "del")
  {
    $query = "DELETE FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }

  $IDpole = $IDpromotion = $IDmatiere = 0;

  if (isset($_POST['IDpole']) && $_POST['IDpole'] != "") $IDpole = $_POST['IDpole'];
  if (isset($_POST['IDpromotion']) && $_POST['IDpromotion'] != "") $IDpromotion = $_POST['IDpromotion'];
  if (isset($_POST['IDmatiere']) && $_POST['IDmatiere'] != "") $IDmatiere = $_POST['IDmatiere'];

  if (isset($_GET["submit"])) $submit   = $_GET["submit"];
  if (isset($_GET['action'])) $action = $_GET['action'];

  if (isset($action) && $action == "confirmEvents") $typeEvent = "2";
  else $typeEvent = "0";

  if (isset($_GET['lessonStatus'])) {
    switch ($_GET['lessonStatus']) {
      case 'waiting':
        $titleText = " en attente";
        break;
      case 'refused':
        $titleText = " refusés";
        break;
      case 'accepted':
        $titleText = " acceptés";
        break;
      default:
        $titleText = "";
        break;
    }
  }



?>

<!-- Latest compiled and minified CSS -->


<link href="css/dp.css" rel="stylesheet" />
<script src="script/jquery.js" type="text/javascript"></script>
<script src="script/Plugins/Common.js" type="text/javascript"></script>
<script src="script/jquery.timepicker.min.js" type="text/javascript"></script>
<link href="css/jquery.timepicker.css" rel="stylesheet" type="text/css" />
<!-- Calendar -->
<script type="text/javascript" src="script/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />







<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Liste des cours<?php if (isset($titleText)) echo '&nbsp;'.$titleText; ?></h1>

  <div style="float: right;">








    <div style="text-align:center; <?php if ($_GET['lessonStatus'] != "") echo "display: none;" ?>">


          <!-- Champ de recherche -->

        <form id="formulaire" action="index.php?item=<?php echo $item; ?>&tri=on" method="post">
          <?php
          //if ($IDpromotion == 0 and $IDmatiere == 0 and $IDpole == 0) {
          ?>
            <div class="input-append" style="float: left; display: inline-block; display: none;">
              <input class="span2" name="IDalpha" id="appendedInputButton" type="text">
              <button class="btn" type="submit">ok</button>
            </div>
          <?php //} ?>


          <div style="display: inline-block; float: right; text-align: right;">


            <div class="mb-3">
              <a class="btn btn-primary btn-sm noprint" id="export_list_excel" href="" onclick="window.open(this.href, '_blank'); return false;">
                <i class="fa fa-upload text-white-50" title="exporter xlsx"></i>&nbsp;Exporter (xlsx)
              </a>
              <a class="btn btn-primary btn-sm noprint" id="export_list" href="" onclick="window.open(this.href, '_blank'); return false;">
                <i class="fa fa-list-alt text-white-50" title="exporter ical"></i>&nbsp;Exporter (ical)
              </a>
              <button class="btn btn-primary btn-sm noprint" type="button" title="Imprimer" onclick="window.print();return false;">
                <i class="fa fa-print text-white-50" title="Imprimer"></i>&nbsp;Imprimer
              </button>
            </div>

            <div class="mb-3">
              <div class="form-row">
                <!-- Tri pas type -->
                <div class="col">
                  <select id="typeSelect" style="<?php if ($_SESSION['CnxGrp'] == 1) echo 'display: none;'; ?>" name="typeSelect" onchange="updateList()" class="custom-select">
                    <option value="0" <?php if ($typeEvent == "" && $_SESSION['CnxGrp'] != 1) echo "selected"; ?>>Programmés ou en attente</option>
                    <option value="1" <?php if ($typeEvent == 1 || $_SESSION['CnxGrp'] == 1) echo "selected"; ?>>Programmés</option>
                    <option value="2" <?php if ($typeEvent == 2) echo 'selected'; ?>>En attente</option>
                    <?php if($_SESSION['CnxAdm'] == 255) { ?>
                      <option value="3" <?php if ($typeEvent == 3) echo 'selected'; ?>>Refusés</option>
                    <?php } ?>
                  </select>
                </div>

                <!-- Tri pas année -->
                <div class="col">
                  <select id="IDpromotion" style="<?php if ($_SESSION['CnxGrp'] == 1 && !getParam('edt_visible_par_tous')) echo 'display: none;'; ?>" name="IDpromotion" onchange="updateList()" class="custom-select">
                    <option value="0" <?php if ($IDpromotion == "") echo "selected"; ?>>Par promotion</option>
                    <?php
                      $query = "SELECT * FROM `campus_classe` WHERE `_visible` = 'O' ORDER BY `campus_classe`.`_IDclass` ASC ";
                      $result = mysqli_query($mysql_link, $query);
                      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                        if ($IDpromotion == $row[0]) $selected = 'selected';
                        if ($IDpromotion == '' && $row[0] == $_SESSION['CnxClass']) $selected = 'selected'; else $selected = '';
                        echo '<option value="'.$row[0].'" '.$selected.'>'.$row[5].'</option>';
                      }
                    ?>
                  </select>
                </div>

                <!-- Tri pas matière -->
                <div class="col">
                  <select id="IDmatiere" name="IDmatiere" onchange="updateList()" class="custom-select">
                    <option value="0" <?php if ($IDmatiere == "") echo "selected"; ?>>Par matières</option>
                    <?php
                      $query = "SELECT * FROM `campus_data` WHERE `_type` = 1 ORDER BY _titre ASC ";
                      $result = mysqli_query($mysql_link, $query);
                      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                        if ($IDmatiere == $row[0]) $selected = 'selected'; else $selected = '';
                        echo '<option value="'.$row[0].'" '.$selected.'>'.$row[5].'</option>';
                      }
                    ?>
                  </select>
                </div>

                <!-- Tri pas pôle -->
                <div class="col">
                  <select id="IDpole" name="IDpole" onchange="updateList()" class="custom-select">
                    <option value="0" <?php if ($IDpole == "") echo "selected"; ?>>Par pôle</option>
                    <?php
                      $query = "SELECT * FROM `pole` ";
                      $result = mysqli_query($mysql_link, $query);
                      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                        if ($IDpole == $row[0]) $selected = 'selected'; else $selected = '';
                        echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
                      }
                    ?>
                  </select>
                </div>

                <?php if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) { ?>
                  <?php if (isset($action) && $action == "confirmEvents" && isset($lessionStatus) && $lessionStatus == "waiting") $IDprof = $_SESSION['CnxID']; ?>
                  <!-- Tri pas Enseignant -->
                  <div class="col">
                    <select id="IDprof" name="IDprof" onchange="updateList()" class="custom-select">
                      <option value="0" <?php if ($IDprof == "") echo "selected"; ?>>Par intervenant</option>
                      <?php
                        $query = "SELECT DISTINCT user._name, user._fname, user._ID, edt._ID FROM user_id user INNER JOIN edt_data edt ON (user._ID = edt._ID OR user._ID = edt._IDrmpl) WHERE user._IDgrp != 1  AND (user._adm = 1 OR user._adm = 255) ORDER BY user._name";
                        $result = mysqli_query($mysql_link, $query);
                        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                          if ($IDprof == $row[3]) $selected = 'selected'; else $selected = '';
                          echo '<option value="'.$row[2].'" '.$selected.'>'.$row[0].' '.$row[1].'</option>';
                        }
                      ?>
                    </select>
                  <?php } ?>
                </div>

                <!-- Tri par type -->
                <div class="col">
                  <select id="type_UV" name="type_UV" onchange="updateList()" class="custom-select">
                    <option value="0" <?php if ($type_UV == '') echo 'selected'; ?>>Par type</option>
                    <option value="1">Cours</option>
                    <option value="2">UV</option>
                    <option value="3">Agenda</option>
                  </select>
                </div>
              </div>


            </div>

            <div class="mb-3" style="float: right;">
              <div class="form-row">
                <?php
                  $date_end_month = new DateTime('now');
                  $date_end_month->modify('last day of this month');
                  $date_end_month->modify('+1 month');

                  // Si on liste les évènements en attente, alors on montre également le mois suivant
                  if (isset($action) && $action == 'confirmEvents') $final_end_date_of_the_month = date('t',strtotime('next month')).'/'.$date_end_month->format('m').'/'.$date_end_month->format('Y');
                  // Si on veux que les évènements affichées soient ceux des deux prochains mois:
                  elseif (!getParam('periodeFilterDefaultEdt')) $final_end_date_of_the_month = $date_end_month->format('d').'/'.$date_end_month->format('m').'/'.$date_end_month->format('Y');
                  // Ou si on veux que la date de fin de filtre corresponde à la date de fin de la période
                  else
                  {
                    $periodes = json_decode(getParam('periodeDates'), TRUE);
                    $current_period = $periodes[getParam('periode_courante').'_end'];
                    $temp = explode('-', $current_period);
                    $final_end_date_of_the_month = $temp[2].'/'.$temp[1].'/'.$temp[0];
                  }
                ?>

                  <i class="fas fa-calendar-alt" style="padding-top: 10px;"></i>
                  <input class="form-control mr-3 ml-3" style="width: 111px;" id="date_1" name="date_1" type="text" value="01/<?php echo date('m/Y'); ?>" />
                  <i class="fas fa-arrow-right" style="padding-top: 10px;"></i>
                  <input class="form-control mr-3 ml-3" style="width: 111px;" id="date_2" name="date_2" type="text" value="<?php echo $final_end_date_of_the_month; ?>" />

                  <i class="fas fa-clock" style="padding-top: 10px;"></i>
                  <input class="form-control mr-3 ml-3" id="start_time"  name="start_time" onchange="onTimeChange();" style="width: 90px;" type="text" value="07:00" />
                  <i class="fas fa-arrow-right" style="padding-top: 10px;"></i>
                  <input class="form-control mr-3 ml-3" id="end_time" name="end_time" onchange="onTimeChange();" style="width: 90px;" type="text" value="23:45" />

                <script>
                  $(function() {
                    $('#date_1').daterangepicker({
                      opens: 'left',
                      locale: lang_datepicker,
                      "autoApply": true,
                      "singleDatePicker": true
                    }).on('change', function(){
                      updateList();
                    });
                  });

                  $(function() {
                    $('#date_2').daterangepicker({
                      opens: 'left',
                      locale: lang_datepicker,
                      "autoApply": true,
                      "singleDatePicker": true
                    }).on('change', function(){
                      updateList();
                    });
                  });

                  $(document).ready(function(){
                    $('#start_time').timepicker({
                      'timeFormat': timepicker['timeFormat'],    // Pour le format en fr
                      'step': 15
                    });
                  });

                  $(document).ready(function(){
                    $('#end_time').timepicker({
                      'timeFormat': timepicker['timeFormat'],    // Pour le format en fr
                      'step': 15
                    });
                  });
                </script>
              </div>

            </div>
          </div>
        </form>
      </div>

  </div>
</div>



<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><span id="numberRowsToShow"></span>&nbsp;|&nbsp;<span id="numberHoursToShow"></span></h6>
  </div>
  <div class="card-body">
    <div id="list"></div>
  </div>
</div>


<nav id="pagination"></nav>










  <hr style="width:80%; text-align:center;"/>





<input type="hidden" id="currentPage" value="1">
<input type="hidden" id="nbElemPerPage">

<input type="hidden" id="sortOrder" value="<?php if (isset($_SESSION['sortOrder_liste_edt'])) echo $_SESSION['sortOrder_liste_edt']; ?>">
<input type="hidden" id="sortBy" value="<?php if (isset($_SESSION['sortBy_liste_edt'])) echo $_SESSION['sortBy_liste_edt']; ?>">


<script>
function updateList()
{
  var sortOrder = $("#sortOrder").val();
  var sortBy = $("#sortBy").val();

  var IDpromotion = $("#IDpromotion").val();
  var IDmatiere = $("#IDmatiere").val();
  var IDpole = $("#IDpole").val();
  var IDprof = $("#IDprof").val();
  var type_UV = $("#type_UV").val();


  var date_1 = $("#date_1").val();
  var date_2 = $("#date_2").val();

  var time_1 = $("#start_time").val();
  var time_2 = $("#end_time").val();

  var status = $("#typeSelect").val();
  var lessonStatus = '<?php if (isset($lessionStatus)) echo $lessionStatus; ?>';
  var remove = '<?php if (isset($_GET['remove'])) echo $_GET['remove']; ?>'


  var currentPage = $("#currentPage").val();
  var nbElemPerPage = $("#nbElemPerPage").val();

  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=getEdtList',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'sortOrder=' + sortOrder + '&sortBy=' + sortBy + '&IDpole=' + IDpole + '&IDmatiere=' + IDmatiere + '&IDprof=' + IDprof + '&IDpromotion=' + IDpromotion + '&date_1=' + date_1 + '&date_2=' + date_2 + '&time_1=' + time_1 + '&time_2=' + time_2 + '&status=' + status + '&currentPage=' + currentPage + '&lessonStatus=' + lessonStatus + '&remove=' + remove + '&nbElemPerPage=' + nbElemPerPage + '&type_UV=' + type_UV,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
      $("#list").html(code_html);
      $(".orderBtn").css("color", "");
      $("#" + sortOrder + "_" + sortBy).css("color", 'var(--blue)');

      var numberRows = $("#numberRows").html();
      $("#numberRowsToShow").html("Résultats: " + numberRows);

      var numberHours = $("#numberHours").html();
      $("#numberHoursToShow").html("Nombre total d'heures: " + numberHours);

      // Pagination
      var pagination = $('#pagination_block').html();
      $('#pagination').html(pagination);
      $('#pagination_block').remove();
    }
  });
  var new_date_1 = date_1.replace("/", ".");
  var new_date_2 = date_2.replace("/", ".");

  new_date_1 = new_date_1.replace("/", ".");
  new_date_2 = new_date_2.replace("/", ".");

  if (sortOrder == "") sortOrder = 0;
  if (sortBy == "") sortBy = 0;
  if (IDpromotion == "") IDpromotion = 0;
  if (IDmatiere == "") IDmatiere = 0;
  if (IDpole == "") IDpole = 0;
  if (IDprof == "") IDprof = 0;
  if (type_UV == "") type_UV = 0;
  if (new_date_1 == "") new_date_1 = 0;
  if (new_date_2 == "") new_date_2 = 0;
  if (time_1 == "") time_1 = 0;
  if (time_2 == "") time_2 = 0;
  if (status == "") status = 0;
  if (lessonStatus == "") lessonStatus = 0;

  // On modifie le lien de l'export pour y ajouter les filtres
  jQuery("#export_list").attr("href", "edt_list_export.php?sortOrder=" + sortOrder + "&sortBy=" + sortBy + "&idpromotion=" + IDpromotion + "&idmatiere=" + IDmatiere + "&idpole=" + IDpole + "&idprof=" + IDprof + "&date_1=" + new_date_1 + "&date_2=" + new_date_2 + "&time_1=" + time_1 + "&time_2=" + time_2 + "&status=" + status + "&lessonStatus=" + lessonStatus + '&currentPage=' + currentPage + '&nbElemPerPage=' + nbElemPerPage + '&type_UV=' + type_UV);
  jQuery("#export_list_excel").attr("href", "exports.php?item=29&cmde=&sortOrder=" + sortOrder + "&sortBy=" + sortBy + "&idpromotion=" + IDpromotion + "&idmatiere=" + IDmatiere + "&idpole=" + IDpole + "&idprof=" + IDprof + "&date_1=" + new_date_1 + "&date_2=" + new_date_2 + "&time_1=" + time_1 + "&time_2=" + time_2 + "&status=" + status + "&lessonStatus=" + lessonStatus + '&currentPage=' + currentPage + '&nbElemPerPage=' + nbElemPerPage + '&type_UV=' + type_UV);

}

function toDoWhenPaginationChange() {
  updateList();
}

updateList();

// $(document).on('click', '#fd-date', updateList());
// $(document).on('change', '#date_2', updateList());

$("#fd-date").click(function(){
  updateList();
});

$('#date_1').change(function(){
  updateList();
});
$('#date_2').change(function(){
  updateList();
});


$(document).on('click','.orderBtn',function(){

  var sortOrder = $(this).attr("id").substring(0, 3);
  $("#sortOrder").val(sortOrder);

  var sortBy = $(this).attr("id").substring(4);
  $("#sortBy").val(sortBy);

  updateList();

 });


function onTimeChange()
{
  var time_start = new Date(2000, 01, 01, $("#start_time").val().substring(0, 2), $("#start_time").val().substring(3, 5), 00).getTime();
  var time_end = new Date(2000, 01, 01, $("#end_time").val().substring(0, 2), $("#end_time").val().substring(3, 5), 00).getTime();

  if (time_end <= time_start) {
    $("#time_1").addClass("error");
    $("#time_2").addClass("error");
  }
  else {
    $("#time_1").removeClass("error");
    $("#time_2").removeClass("error");
    updateList();
  }
}

function acceptCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=acceptCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
      $("#row_" + id_event).fadeOut();
      $("#row_" + id_event).hide();
    }
  });
  updateList();
}

function refuseCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=refuseCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
      // $("#accept_" + id_event).fadeOut();
      // $("#refuse_" + id_event).fadeOut();
      // $("#infoRow_" + id_event).removeClass("askLesson");
      // $("#infoRow_" + id_event).removeClass("acceptedLesson");
      // $("#infoRow_" + id_event).addClass("refusedLesson");


    }
  });

  updateList();
}

function removeCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=removeCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
      $("#row_" + id_event).fadeOut();
      $("#row_" + id_event).hide();
    }
  });
}



</script>

<style>


.row_status {
  text-align: center;
  color: inherit;
}
/* .orderBtn {
  text-decoration: none !important;
  cursor: pointer;

}

.askLesson {
  background-color: #fcf8e3 !important;
  border: 1px solid #fbeed5 !important;
  color: #c09853 !important;
  text-align: center !important;
  vertical-align: middle;
  font-size: 20px;
}
.acceptedLesson {
  background-color: #dff0d8 !important;
  border: 1px solid #d6e9c6 !important;
  color: #468847 !important;
  text-align: center !important;
  vertical-align: middle;
  font-size: 20px;
}
.refusedLesson {
  background-color: #f2dede !important;
  border: 1px solid #eed3d7 !important;
  color: #b94a48 !important;
  text-align: center !important;
  vertical-align: middle;
  font-size: 20px;
} */
</style>
