<?php
/*
 *		module   : absent/edit_absent.php
 *		projet   : page de saisie/modification d'une absence
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy (IP-Solutions) (contact@ip-solutions.fr)
 *		creation : 27/10/20
 *		modif    :
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();


require_once '../../../config.php';
require_once '../../../php/dbconfig.php';
require_once '../../fonction.php';
require_once '../../fonction/auth_tools.php';

// Si l'utilisateur n'est pas connecté, alors on arrête tout!
if (!isUserConnected()) exit();

// Si on est pas un membre de l'administration, on arrête tout
if ($_SESSION['CnxGrp'] <= 1) exit();

// // On récupère les éléments dans le get
// $get = array();
// foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];
//
// // On récupère les éléments dans le post
// $post = array();
// foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

// On récupère les éléments dans le post et si existe pas dans le get
$post_get = array('IDabs');
foreach ($post_get as $value) {
  if (isset($_POST[$value])) $$value = $_POST[$value];
  elseif (isset($_GET[$value])) $$value = $_GET[$value];
}
?>


<?php
// $IDabs = 1;


if (isset($IDabs) && $IDabs) {
  $absence = $db->getRow("SELECT _IDdata, _IDctn, _IDgrp, _IDabs, _start, _end, _texte, _isok, _valid, _file FROM absent_items WHERE _IDitem = ?i ", $IDabs);

  $start = date('d/m/Y H:i', strtotime($absence->_start));
  $end = date('d/m/Y H:i', strtotime($absence->_end));
  $IDdata = $absence->_IDdata;
  $IDuser = $absence->_IDabs;
  $note = $absence->_texte;
  if ($absence->_valid == 'O') $justified_checked = 'checked'; else $justified_checked = '';
}
else {
  $IDclass = $IDuser = $IDdata = 0;
  $start = $end = date('d/m/Y H:i');
  $note = $justified_checked = '';
}


?>


<form id="abs_edit" action="?submitAbs" method="post" enctype="multipart/form-data">
  <input type="hidden" name="item" value="63">
  <input type="hidden" name="cmde" value="show">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
  <input type="hidden" name="IDabs" value="<?php if (isset($IDabs)) echo $IDabs; ?>">

  <div class="form-group">
    <label for="IDuser">Choississez un utilisateur</label>
    <select class="form-control custom-select" id="IDuser" name="IDuser" required>
      <?php
      $list_users = $db->getAll("SELECT _ID as ID, _name as name, _fname as fname from user_id where _IDgrp = 1 ");
        foreach ($list_users as $list_user) {
          if ($IDuser == $list_user->ID) $selected = 'selected'; else $selected = '';
          echo '<option value="'.$list_user->ID.'" '.$selected.'>'.$list_user->name.'&nbsp;'.$list_user->fname.'</option>';
        }
      ?>
    </select>
  </div>


  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="start">Du</label>
      <input type="text" name="start" class="form-control" value="<?php echo $start; ?>" required>
    </div>
    <div class="form-group col-md-6">
      <label for="end">au</label>
      <input type="text" name="end" class="form-control" value="<?php echo $end; ?>" required>
    </div>
  </div>

  <div class="form-group">
    <label for="IDdata">Motif</label>
    <select class="form-control custom-select" id="IDdata" name="IDdata" required>
      <?php
      $liste_motifs = $db->getAll("SELECT `_IDdata` as `ID`, `_texte` as `text` from `absent_data` WHERE `_visible` = 'O' AND `_lang` = ?s ", $_SESSION['lang']);
        foreach ($liste_motifs as $liste_motif) {
          if ($IDdata == $liste_motif->ID) $selected = 'selected'; else $selected = '';
          echo '<option value="'.$liste_motif->ID.'" '.$selected.'>'.$liste_motif->text.'</option>';
        }
      ?>
    </select>
  </div>

  <div class="form-group">
    <label for="note">Note</label>
    <textarea class="form-control" id="note" name="note" rows="3"><?php if (isset($note)) echo $note; ?></textarea>
  </div>

  <div class="form-group">
    <label for="file">Pièce jointe</label>
    <div class="custom-file">
      <input type="file" class="custom-file-input" id="file" name="file">
      <label class="custom-file-label" for="file" data-browse="Parcourir">Choisir un fichier</label>
    </div>
  </div>


  <div class="custom-control custom-checkbox">
    <input type="checkbox" class="custom-control-input" id="justified" name="justified" value="O" <?php echo $justified_checked; ?>>
    <label class="custom-control-label" for="justified">Absence justifiée ?</label>
  </div>
</form>




<script>
$(function() {
  $('input[name="start"]').daterangepicker({
    timePicker: true,
    "timePicker24Hour": true,
    "timePickerIncrement": 15,
    opens: 'left',
    locale: lang_datepicker_time,
    "singleDatePicker": true,
    "autoApply": true
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>



<script>
$(function() {
  $('input[name="end"]').daterangepicker({
    timePicker: true,
    "timePicker24Hour": true,
    "timePickerIncrement": 15,
    opens: 'left',
    locale: lang_datepicker_time,
    "singleDatePicker": true,
    "autoApply": true
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>

<script>
// Lorsque l'on upload un fichier, on affiche le nom du fichier dans le champ
$('#file').on('change',function(){
  var fileName = $(this).val();
  var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
  $(this).next('.custom-file-label').html(cleanFileName);
})
</script>
