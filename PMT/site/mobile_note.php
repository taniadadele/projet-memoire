<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : mobile_note.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : 29/07/19 - Thomas Dazy - remise à jour de la version mobile
 */

 include("mobile_banner.php");

$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

$IDclass  = ( @$_POST["IDclass"] )			// Identifiant de la classe
	? (int) addslashes($_POST["IDclass"])
	: (int) addslashes(@$_GET["IDclass"]);

// Matière
if (isset($_POST['IDmat'])) $IDmat = addslashes($_POST['IDmat']);
else $IDmat                        = addslashes($_GET['IDmat']);

// Année
if (isset($_POST["year"])) $year    = addslashes($_POST['year']);
elseif (isset($_GET["year"])) $year = addslashes($_GET['year']);
else
{
  $Query  = "select distinctrow notes_data._year from notes_data, campus_classe ";
  $Query .= "where campus_classe._IDcentre = '$IDcentre' ";
  $Query .= "AND campus_classe._visible = 'O' ";
  $Query .= "AND campus_classe._IDclass = notes_data._IDclass ";
  $Query .= ( $IDclass ) ? "AND campus_classe._IDclass = '$IDclass' " : "" ;
  $Query .= "order by _year DESC LIMIT 1";
  $result = mysql_query($Query, $mysql_link);
  $row    = ( $result ) ? remove_magic_quotes(mysql_fetch_row($result)) : 0 ;
  while ( $row )
  {
    $year = $row[0];
    $row = remove_magic_quotes(mysql_fetch_row($result));
  }
}

// Période (trimestre/semestre...)
if (isset($_POST['period'])) $period    = addslashes($_POST['period']);
elseif (isset($_GET['period'])) $period = addslashes($_GET['period']);
else $period                            = 0;


$setlock  = @$_POST["unlocked_x"];			// verrouillage du trimestre
$unlock   = @$_POST["locked_x"];			// déverrouillage du trimestre
$submit   = @$_POST["valid_x"];				// bouton validation

require "msg/notes.php";
require_once "include/TMessage.php";

$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/notes.php");
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;
?>
<?php $currentPage = "note"; ?>
<?php include("mobile_menu.php"); ?>

<div class="maincontent">

	<?php
		require_once $_SESSION["ROOTDIR"]."/include/notes.php";
		require_once $_SESSION["ROOTDIR"]."/include/postit.php";
	?>

	<form id="formulaire" action="" method="post" enctype="multipart/form-data">
  	<?php
  		print("
  			<input type=\"hidden\" name=\"item\"   value=\"$item\" />
  			<input type=\"hidden\" name=\"cmde\"   value=\"$cmde\" />
  			");
  	?>
    <div style="width: 100%; text-align: center; margin-top: 10px;">
    	<label for="IDmat" style="width: 90%;">
      	<select id="IDmat" name="IDmat" style="width: 90%;" onchange="document.forms.formulaire.submit()">
          <option value="0">Tout</option>
        	<?php
          // Section pour les UVs
          echo "<optgroup label=\"UV\">";
            $query  = "SELECT DISTINCT notes_data._IDmat, notes_items._value from notes_data inner join notes_items ON notes_data._IDdata = notes_items._IDdata ";
            $query .= "WHERE notes_items._value != '' ";
            $query .= 'AND notes_data._IDmat > 100000 ';
            $query .= "AND notes_items._IDeleve = '".$_SESSION['CnxID']."' ";

            $result = mysql_query($query);
            if ( $IDmat == "" ) $IDmat = 0;
            $alreadyShownMat = ";";
            while ($mat = mysql_fetch_array($result, MYSQL_NUM)) {
                $uvID = $mat[0] - 100000;
                if (strpos($alreadyShownMat, ";".$mat[0].";") === false)
                {
                  $select = ( $IDmat == $mat[0] ) ? "selected=\"selected\"" : "" ;
                  print("<option value=\"$mat[0]\" $select>".getUVNameByUVID($uvID)." (".getMatNameByIdMat(getMatIDByPMAID(getUVPMAByUVID($uvID))).")</option>");
                  $alreadyShownMat .= $mat[0].";";
                }


            }
          echo "</optgroup>";

          // Section pour les matières
          echo "<optgroup label=\"Matières\">";
              $query  = "SELECT DISTINCT notes_data._IDmat, notes_items._value, notes_data._IDdata from notes_data inner join notes_items ON notes_data._IDdata = notes_items._IDdata ";
              $query .= "WHERE notes_items._value != '' ";
              $query .= "AND notes_items._IDeleve = '".$_SESSION['CnxID']."' ";
              $query .= 'AND notes_data._IDmat < 100000 ';

              $result = mysql_query($query);
              if ( $IDmat == "" ) $IDmat = 0;
              $alreadyShownMat = ";";
          		while ($mat = mysql_fetch_array($result, MYSQL_NUM)) {
                if (strpos($alreadyShownMat, ";".$mat[0].";") === false)
                {
                  $select = ( $IDmat == $mat[0] ) ? "selected=\"selected\"" : "" ;
            			print("<option value=\"$mat[0]\" $select>".getMatNameByIdMat(getMatIDByPMAID($mat[0]))."</option>");
                  $alreadyShownMat .= $mat[0].";";
                }
        			}
              echo "</optgroup>";
        	?>
      	</select>
    	</label>

      <div style="width: 90%; text-align: center; margin: auto;">
  			<table width="90%" style="text-align: center; margin: auto;">
  				<tr>
  					<td width="50%" style="text-align: left;">
  						<label for="period" style="width: 90%">
    						<select id="period" name="period" style="width: 100%;" onchange="document.forms.formulaire.submit()" width="100%">";
                  <option value="0" <?php if ($period == 0) echo "selected"; ?>>Tout</option>
                  <?php
                    $periodeList = json_decode(getParam('periodeList'), TRUE);
                    foreach ($periodeList as $key => $value) {
                      if ($period == $key) $selected = "selected";
                      else $selected = "";
                      echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
                    }
                  ?>
    						</select>
  						</label>
  					</td>
  					<td style="text-align: right; width: 50%;">
  						<label for="year" style="width: 90%;">
    						<select id="year" style="width: 100%;" name="year" onchange="document.forms.formulaire.submit()" width="100%">");
                  <?php
        						// affichage des années
        						$query  = "select distinctrow notes_data._year from notes_data, campus_classe ";
        						$query .= "where campus_classe._IDcentre = '$IDcentre' ";
        						$query .= "AND campus_classe._visible = 'O' ";
        						$query .= "AND campus_classe._IDclass = notes_data._IDclass ";
        						$query .= ( $IDclass ) ? "AND campus_classe._IDclass = '$IDclass' " : "" ;
        						$query .= "order by _year DESC";
        						$result = mysql_query($query);
        						if ( mysql_numrows($result) == 0 )
        							echo '<option value="'.$year.'">'.$year.'</option>';

        						while ($row = mysql_fetch_array($result, MYSQL_NUM))
        							echo '<option value="'.$row[0].'" %s>'.$row[0].'</option>', ($year == $row[0]) ? 'selected="selected"' : "";
                  ?>
    						</select>
  						</label>
  					</td>
  				</tr>
  			</table>
      </div>
    </div>











    <table class="table table-bordered table-striped">
      <tr>
        <th>Note</th>
        <th>Coef</th>
      </tr>

    <?php
    	$query  = "SELECT notes_items._IDitems, notes_items._IDdata, notes_items._index, notes_items._value, notes_data._type, notes_data._total, notes_data._coef, notes_data._IDmat ";
      $query .= "FROM notes_items ";
      $query .= "INNER JOIN notes_data ON notes_items._IDdata = notes_data._IDdata ";
    	$query .= "WHERE notes_items._IDeleve = '".$_SESSION['CnxID']."' ";
      $query .= "AND notes_items._value != ''  ";
      // Section pour les filtres
      if ($IDmat != 0 and $IDmat != "" and isset($IDmat)) $query .= "AND notes_data._IDmat = '".$IDmat."' ";
      if ($period != 0 and $period != "") $query .= "AND notes_data._period = '".$period."' ";
      if ($year != 0 and $year != "") $query .= "AND notes_data._year = '".$year."' ";
      $query .= "ORDER BY notes_data._IDmat, notes_items._index ASC ";

      // echo $query."<br>";

      $currentMatIDLoop = "";
      $result = mysql_query($query, $mysql_link);
      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

        $row_type   = explode(";", $row[4]);
        $row_total  = explode(";", $row[5]);
        $row_coef   = explode(";", $row[6]);

        $currentMatID = $row[7];
        if ($currentMatID > 100000)
        {
          $currentMatID = $currentMatID - 100000;
          $matName = "UV - ".getUVNameByUVID($currentMatID)." (".getMatNameByIdMat(getMatIDByPMAID(getUVPMAByUVID($currentMatID))).")";
        }
        else $matName = getPoleNameByIdPole(getPoleIDByPMAID($row[7])).' - '.getMatNameByIdMat(getMatIDByPMAID($row[7]));


        if ($row[3] >= (3 * ($row_total[$row[2]] / 4))) $alert = "alert alert-success";
        elseif ($row[3] <= ($row_total[$row[2]] / 4)) $alert = "alert alert-danger";
        elseif ($row[3] <= ($row_total[$row[2]] / 2)) $alert = "alert alert-warning";
        else $alert = "";

        if ($currentMatIDLoop != $row[7])
        {
          $currentMatIDLoop = $row[7];
          echo "<tr class=\"alert alert-info\" style=\" color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; \">";
            echo "<td colspan=\"2\" style=\"text-align: center; font-weight: bold;\">$matName</td>";
          echo "</tr>";
        }

        echo "<tr>";
          echo "<td class=\"note_visu ".$alert."\">".$row[3]."/".$row_total[$row[2]]."</td>";
          echo "<td class=\"note_visu ".$alert."\">".$row_coef[$row[2]]."</td>";
          // echo "<td>".$row[7]."</td>";
        echo "</tr>";

      }






    	?>
    </table>

    <style>
      .note_visu {
        text-align: center;
      }
    </style>

	</form>

</div>

<?php include("mobile_footer.php"); ?>
