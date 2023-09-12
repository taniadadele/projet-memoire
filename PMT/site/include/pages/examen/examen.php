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
 *		module   : examen.htm
 *		projet   : Page de visualisation des examens
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 14/03/19
 */


  if (isset($_GET['submit']) && $_GET['submit'] == "del" && $_SESSION['CnxGrp'] > 1)
  {
    $query = "DELETE FROM `campus_examens` WHERE `_ID_exam` = '".$_GET['idexam']."' ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }

  if (isset($_GET["submit"])) $submit = $_GET["submit"];

  if (isset($_GET['shownext'])) $showNext = addslashes($_GET['shownext']);
  elseif (isset($_POST['shownext'])) $showNext = addslashes($_POST['shownext']);
  else $showNext = 1;


  $get = array('IDclass', 'typeExam', 'oralExam', 'IDpole', 'IDmat');
  foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

  if (!isset($IDclass) || $_SESSION['CnxGrp'] <= 1) $IDclass = 0;
  if (!isset($typeExam)) $typeExam = 0;

  if (isset($oralExam) && $oralExam == 'oral') $oralExam = 'O';
  elseif (isset($oralExam) && $oralExam == 'ecrit') $oralExam = 'N';
  elseif (isset($oralExam)) unset($oralExam);

  // Je stocke le nom des exams dans un tableau
  $query = "SELECT _ID_exam, _nom from campus_examens where 1 ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result)) $exam_name[$row[0]] = stripslashes($row[1]);



  // $promotion = substr(getPromotionByGraduationYear(getGraduationYearByClassID($_SESSION['CnxClass'])), 0, 1);


  $query = "SELECT campus_examens.* FROM `campus_examens` LEFT JOIN `edt_data` edt ON (edt._ID_examen = campus_examens._ID_exam) LEFT JOIN `pole_mat_annee` `pma` ON `campus_examens`.`_ID_pma` = `pma`.`_ID_pma` WHERE 1 ";
  if ($_SESSION['CnxGrp'] == 1) $query .= "AND campus_examens._ID_pma IN (SELECT _ID_pma FROM pole_mat_annee WHERE _ID_year = (SELECT _code FROM campus_classe WHERE _IDclass = '".$_SESSION['CnxClass']."')) ";
  if (isset($IDclass) && $IDclass) $query .= "AND campus_examens._ID_pma IN (SELECT _ID_pma FROM pole_mat_annee WHERE _ID_year = (SELECT _code FROM campus_classe WHERE _IDclass = '".$IDclass."')) ";
  if (isset($typeExam) && $typeExam) $query .= "AND campus_examens._type = '".$typeExam."' ";
  if (isset($oralExam) && $oralExam) $query .= "AND campus_examens._oral = '".$oralExam."' ";

  // Si on veux voir que les examens à venir
  if ($showNext)
  {
    // Si on est la dernière semaine de l'année alors on s'assure de pas avoir 1 comme numéro de semaine...
    if (date('m') == 12) $weekNumber = date('W', mktime(0, 0, 0, 12, 28, $year));
    else $weekNumber = date('W');
    $query .= "AND ((edt._annee > '".date('Y')."' OR (edt._annee = '".date('Y')."' AND edt._nosemaine > '".$weekNumber."' OR (edt._annee = '".date('Y')."' AND edt._nosemaine = '".$weekNumber."' AND edt._jour >= '".date('N')."' ))) OR edt._annee IS NULL) ";
  }
  $query .= "ORDER BY `pma`.`_ID_year` ASC, FIELD(campus_examens.`_type`, 1, 3, 2, 4) ASC ";
  $result = mysqli_query($mysql_link, $query);

  $num_rows = mysqli_num_rows($result);
?>





<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($EXAMEN_LIST); ?></h1>
  <div style="float: right;">
    <div style="float: right; text-align: right;">
    <div class="mb-3">
      <?php if ( $_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 2 || $_SESSION["CnxGrp"] == 4 ) { ?>
        <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo myurlencode("index.php?item=$item&cmde=new"); ?>">
          <i class="fas fa-plus-square fa-sm text-white-50" aria-hidden="true"></i>&nbsp;Nouveau
        </a>
      <?php } ?>
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo $_SESSION["ROOTDIR"]; ?>/exports.php?item=70&cmde=&shownext=<?php echo $showNext; ?>" onclick="window.open(this.href, '_blank'); return false;">
        <i class="fas fa-upload fa-sm text-white-50" title="Export"></i>&nbsp;Exporter
      </a>
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="#" onlick="window.print();return false;">
        <i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
      </a>
    </div>

    <div class="mb-3">
      <form id='formulaire' method="get" action="" style='display: inline-block;'>
        <div class="form-row mb-3">
          <div class="col">
            <input type="hidden" name='item' value='<?php echo $_GET['item']; ?>'>
            <select name='shownext' id='shownext' onchange="document.forms.formulaire.submit()" class="custom-select">
              <option value='1' <?php if ($showNext) echo 'selected'; ?>>À venir</option>
              <option value='0' <?php if (!$showNext) echo 'selected'; ?>>Tous</option>
            </select>
          </div>
          <div class="col">
            <?php echo getClassSelect('IDclass', 'IDclass', $IDclass, true, 'formulaire', true, false); ?>
          </div>
          <div class="col">
            <select name="typeExam" onchange="document.forms.formulaire.submit()" class="custom-select">
              <option value="0">Type d'examen</option>
              <?php
                $type_exams = json_decode(getParam('type-examen'), true);
                foreach ($type_exams as $key => $value) {
                  if ($typeExam == $key) $selected = 'selected'; else $selected = '';
                  echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                }
              ?>
            </select>
          </div>
          <div class="col">
            <select name="oralExam" onchange="document.forms.formulaire.submit()" class="custom-select">
              <option value="all">Support</option>
              <option value="oral" <?php if (isset($oralExam) && $oralExam == 'O') echo 'selected'; ?>>Oral</option>
              <option value="ecrit" <?php if (isset($oralExam) && $oralExam == 'N') echo 'selected'; ?>>Ecrit</option>
            </select>
          </div>
        </div>


        <div class="form-row">
          <div class="col">
            <select name="IDpole" onchange="document.forms.formulaire.submit()" class="custom-select">
                <option value="0">Pôle</option>
                <?php
                  $query = "SELECT _ID, _name FROM pole WHERE 1 ";
                  $result_filtre = mysqli_query($mysql_link, $query);
                  while ($row = mysqli_fetch_array($result_filtre)) {
                    if (isset($IDpole) && $IDpole == $row[0]) $selected = 'selected'; else $selected = '';
                    echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
                  }
                ?>
              </select>
            </div>
            <div class="col">
              <select name="IDmat" onchange="document.forms.formulaire.submit()" class="custom-select">
                <option value="0">Matière</option>
                <?php
                  $query = "SELECT _IDmat, _titre FROM campus_data WHERE _visible = 'O' ";
                  $result_filtre = mysqli_query($mysql_link, $query);
                  while ($row = mysqli_fetch_array($result_filtre)) {
                    if ($IDmat == $row[0]) $selected = 'selected'; else $selected = '';
                    echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
                  }
                ?>
              </select>
            </div>
        </div>
      </form>
    </div>

  </div>



  </div>
</div>






<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Résultats: <?php echo $num_rows; ?></h6>
  </div>
  <div class="card-body">



    <script src="script/sweetalert2.min.js"></script>
    <style>
      .swal-text {
      	text-align: center;
      }
    </style>

    	<?php
    		if ( $result AND $page ) {
    			echo "<table class=\"table table-bordered table-striped\">
                    <tr>
                      <th>Nom de l'examen</th>
                      <th>Année</th>
                      <th>Type</th>
                      <th>Pôle</th>
                      <th>Matière</th>
    									<th>Note max</th>
    									<th>Coef</th>
    									<th>Date</th>
                      <th>Certificat initial</th>";

           if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) {
             echo "<th class=\"noprint\">Options</th>";
           }
           echo "</tr>";


    			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    				// suppression : il faut les droits du gestionnaire
    				$delete = ( $_SESSION["CnxAdm"] & 8 or strpos($row[5], ";".$_SESSION['CnxID'].";") !== false )
    					? "<a href=\"".myurlencode("index.php?item=$item&idexam=$row[0]&submit=del")."\" class=\"icon-trash\">".
    					"</a>"
    					: "" ;

            $deleteURL = ( $_SESSION["CnxAdm"] & 8 or strpos($row[5], ";".$_SESSION['CnxID'].";") !== false )
    					? myurlencode("index.php?item=$item&idexam=$row[0]&submit=del")
    					: "" ;

    				echo "<tr id=\"row_".$row[0]."\">";
              $query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$row[6]." ";
      				$result2 = mysqli_query($mysql_link, $query2);
      				$row2   = remove_magic_quotes(mysqli_fetch_row($result2));
      				$annee = getNiveauNameByNumNiveau($row2[1]);

      				$query3 = "SELECT `_name` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
      				$result3 = mysqli_query($mysql_link, $query3);
      				$row3   = mysqli_fetch_row($result3);
      				$poleName = $row3[0];

      				$query4 = "SELECT `_titre` FROM `campus_data` WHERE `_IDmat` = ".$row2[3]." ";
      				$result4 = mysqli_query($mysql_link, $query4);
      				$row4   = remove_magic_quotes(mysqli_fetch_row($result4));
      				$matiereName = $row4[0];


              $examType = getParam('type-examen');
              $examType = json_decode($examType, TRUE);
              if ($row[5] == "O") $oral = " (ORAL)";
              else $oral = "";
              if ($row[7] != "" && $row[7] != NULL) $rattrapage = " (RATTRAPAGE)";
              else $rattrapage = "";

      				echo "<td>".stripslashes($row[2])."</td>";
              echo "<td>".$annee."</td>";
              echo "<td>".$examType[$row[1]].$oral.$rattrapage."</td>";
              echo "<td>".$poleName."</td>";
              echo "<td>".$matiereName."</td>";
      				echo "<td>".stripslashes($row[4])."</td>";
              echo "<td>".stripslashes($row[3])."</td>";

        			// Affiche la date de l'examen (va la chercher dans l'edt) et sinon "à définir"
        			$query5 = "SELECT * FROM `edt_data` WHERE `_ID_examen` = '".$row[0]."' ORDER BY `_jour` DESC, `_nosemaine` DESC, `_annee`  LIMIT 1 ";
        			$result5 = mysqli_query($mysql_link, $query5);
        			$isdateexam = false;
        			while ($row5 = mysqli_fetch_array($result5, MYSQLI_NUM)) {
        				// Création de la date
                $gendate = new DateTime();
                $gendate->setISODate($row5[16],$row5[13],($row5[9] + 1)); //year , week num , day
        				echo "<td>
        				  ".getDayNameByDayNumber(date('N', strtotime($gendate->format('d-m-Y'))) - 1)." ".$gendate->format('d')." ".getMonthNameByMonthNumber(date('m', strtotime($gendate->format('d-m-Y'))))." "
                  .date('Y', strtotime($gendate->format('d-m-Y')))."
        				</td>";
        				$isdateexam = true;
        			}
        			if(!$isdateexam) echo "<td></td>";

              // Certificat initial
              if (isset($exam_name[$row[7]])) echo '<td>'.$exam_name[$row[7]].'</td>';
              else echo '<td></td>';

              $query_2 = "SELECT _value FROM notes_items WHERE _IDdata IN (SELECT _IDdata FROM notes_data WHERE _IDmat = '".($row[0] + 100000)."') AND _value != '' ";
              $result_2 = mysqli_query($mysql_link, $query_2);
              $number_of_results = mysqli_num_rows($result_2);

              // Boutons supprimer et éditer
      				if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) {
                echo '<td style="width:70px; text-align: center;" class="noprint" >';

                  echo '<a href="index.php?item='.$item.'&cmde=new&idexam='.$row[0].'" class="link-unstyled"><i class="fas fa-pencil-alt fa-sm"></i></a>';

                  if ($number_of_results == 0) echo '&nbsp;<div style="display: inline-block; margin-top: 4px;margin-left: 4px; cursor: pointer;" id="deleteButton_'.$row[0].'" class="deleteButton" idToDelete="'.$row[0].'" deleteUrl="'.$deleteURL.'"><span class="fas fa-trash fa-sm"></span></a></div>';
                  else echo '&nbsp;<a href="#" class="link-unstyled" onclick="alert(\'Des notes sont liées à cet examen, il ne peux pas être supprimé\');"><span class="fas fa-trash fa-sm"></span></a></div>';
                echo "</td>";
      				}
  		      echo "</tr>";
    			}
        	echo "</table>";
    			}
        ?>

    <script src="script/bootstrap-notify-min.js"></script>


    <script>
      $('.deleteButton').click(function () {

        var deleteURL = $(this).attr('deleteUrl');
        var deleteID = $(this).attr('idToDelete');

        swal({
          title: "Attention",
          text: "Vous allez supprimer cet élément de façon définitive!\n Êtes-vous sûr ?",
          icon: "warning",
          buttons: ["Annuler", true],
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url : deleteURL,
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : '',
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé
                swal("L'élément à bien été supprimé !", {
                  icon: "success",
                });
                $("#row_" + deleteID).fadeOut();
              }
            });
          } else {
            swal("L'élément n'à pas été supprimé !");
          }
        });
      });

    </script>
  </div>
</div>
