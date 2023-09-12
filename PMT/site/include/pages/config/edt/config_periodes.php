<h5>Configuration des périodes</h5>

  <?php
    $periode_list = json_decode(getParam('periodeList'), true);
    $periode_date_list = json_decode(getParam('periodeDates'), true);
  ?>


  <?php

    foreach ($periode_list as $key => $value) {


      $template = '<div class="form-row mb-2" style="margin: auto;">';
        $template .= '<div class="col-2" style="font-weight: bold; text-align: center; padding-top: 5px;">'.$value.'</div>';
        $template .= '<div class="col-1" style="text-align: right; padding-top: 5px;">Du </div>';
        $template .= '<div class="control-group col-3" style="display: inline-block;" id="periode_start_'.$key.'">';
          $template .= '<input class="form-control datepicker" name="periode_start_'.$key.'" type="text" value="'.date("d/m/Y", strtotime($periode_date_list[$key."_start"])).'" />';
        $template .= '</div>';
        $template .= '<div class="col-1" style="text-align: right; padding-top: 5px;">'.$msg->read($CONFIG_AU).'</div>';
        $template .= '<div class="control-group col-3" style="display: inline-block;" id="periode_end_'.$key.'">';
          $template .= '<input class="form-control datepicker" name="periode_end_'.$key.'" type="text" value="'.date("d/m/Y", strtotime($periode_date_list[$key."_end"])).'" />';
        $template .= '</div>';
        $template .= '<div class="col-2"></div>';
      $template .= '</div>';
      echo $template;
    }
  ?>


  <script>

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

  $(document).ready(function(){
    loadDatepicker();
  })


  </script>
