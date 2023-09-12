<h5><?php echo $msg->read($CONFIG_CONFVAC); ?></h5>


<?php
$query  = "select _vacances from config_centre ";
$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre ";
$query .= "order by _IDcentre";

$result = mysqli_query($mysql_link, $query);
$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

$res = json_decode($row[0]);
?>


<?php
  // On crée le template des lignes
  $template = '';
  $template = '<div class="form-row mb-2" style="margin: auto;">';
    $template .= '<div class="col-2"></div>';
    $template .= '<div class="col-1" style="text-align: right; padding-top: 5px;">Du </div>';
    $template .= '<div class="control-group col-3" style="display: inline-block;" id="vac_start_key">';
      $template .= '<input class="form-control datepicker" name="vac_start_key" type="text" value="start_date_value" />';
    $template .= '</div>';
    $template .= '<div class="col-1" style="text-align: right; padding-top: 5px;">'.$msg->read($CONFIG_AU).'</div>';
    $template .= '<div class="control-group col-3" style="display: inline-block;" id="vac_end_key">';
      $template .= '<input class="form-control datepicker" name="vac_end_key" type="text" value="end_date_value" />';
    $template .= '</div>';
    $template .= '<div class="col-2"></div>';
  $template .= '</div>';
?>


<div id="template" style="display: none;">
  <?php
    $temp = str_replace('start_date_value', date('d/m/Y'), $template);
    $temp = str_replace('end_date_value', date('d/m/Y'), $temp);
    echo $temp;
  ?>
</div>

<div style="text-align: center; cursor: pointer;" id="div_vac">
  <?php
  if($row[0] == '')
  {
    $temp = str_replace('start_date_value', date('d/m/Y'), $template);
    $temp = str_replace('end_date_value', date('d/m/Y'), $temp);
    $temp = str_replace('vac_start_key', 'vac_start_0', $temp);
    $temp = str_replace('vac_end_key', 'vac_end_0', $temp);
    echo $temp;
    
    $nbvac = 1;
  }
  else
  {
    $start = $res->start;
    $end = $res->end;

    // Tri par ordre chronologique des périodes de vacances
    $new_array = array();
    foreach ($start as $key => $value) {
      $new_array[] = array('start' => $start[$key], 'end' => $end[$key]);
    }

    function sortFunction( $a, $b ) {
      $temp = explode('/', $a['start']);
      $date_a = strtotime($temp[2].'-'.$temp[1].'-'.$temp[0]);
      $temp_2 = explode('/', $b['start']);
      $date_b = strtotime($temp_2[2].'-'.$temp_2[1].'-'.$temp_2[0]);

      return $date_a - $date_b;
    }

    usort($new_array, 'sortFunction');

    $start = array();
    $end = array();
    foreach ($new_array as $key => $value) {
      $start[] = $value['start'];
      $end[] = $value['end'];
    }

    foreach($start as $key => $val)
    {
      $temp = str_replace('start_date_value', $start[$key], $template);
      $temp = str_replace('end_date_value', $end[$key], $temp);
      $temp = str_replace('vac_start_key', 'vac_start_'.$key, $temp);
      $temp = str_replace('vac_end_key', 'vac_end_'.$key, $temp);
      echo $temp;
    }
    $nbvac = $key + 1;
  }
  ?>
</div>
<div style="width: 100%; text-align: center;">
  <button type="button" id="add_vac" class="btn btn-success"><i class="fas fa-plus"></i>&nbsp;Ajouter une période</button>
</div>

<span id="num_vac" style="display: none;"><?php echo $nbvac ?></span>

<script>
  $(document).ready(function() {
    $("#add_vac").click(function() {
      // On récupère le numéro du champ
      var num_vac = $('#num_vac').html();
      // On récupère le template
      var template = $('#template').html();
      $('#div_vac').append(template);
      // On remplace les ID des inputs
      $('#div_vac').find('#vac_start_key').attr('id', 'vac_start_' + num_vac);
      $('#div_vac').find('#vac_end_key').attr('id', 'vac_end_' + num_vac);
      $('#div_vac').find('input[name="vac_start_key"]').attr('name', 'vac_start_' + num_vac);
      $('#div_vac').find('input[name="vac_end_key"]').attr('name', 'vac_end_' + num_vac);
      // On charge les datepickers
      loadDatepicker();
      // Et on stoque l'ID du prochain évènement
      $('#num_vac').html(eval(num_vac) + eval(1));
    });
  });


  // Fonction: charger les datepickers (on les supprimes avant pour être sûr de ne pas avoir plusieurs instances du même datepicker)
  function loadDatepicker() {
    $('.datepicker').each(function() {
      $(this).datepicker('destroy');
      $(this).daterangepicker({
        opens: 'left',
        locale: lang_datepicker,
        "singleDatePicker": true,
        "autoApply": true
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
    })
  }

  // Au chargement de la page, on charge les datepickers
  $(function() {
    loadDatepicker();
  });

</script>
