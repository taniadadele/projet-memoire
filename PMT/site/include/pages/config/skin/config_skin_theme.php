<?php
  // Page de modification des CSS du thème
  // Copyright Thomas Dazy (contact@thomasdazy.fr)

  if ($_SESSION['CnxAdm'] != 255) exit();

  if (isset($_GET['id'])) $id = $_GET['id'];
  elseif (isset($_POST['id'])) $id = $_POST['id'];
  else exit();
  $theme = $db->getRow("SELECT _title as name, _ident as ident from config WHERE _IDconf = ?i ", $id);

  $filePath = 'download/logos/'.getLogoDir($theme->ident).'/theme_custom.css';

  // On écris le contenu quand on confirme
  if (isset($_POST['css'])) file_put_contents($filePath, $_POST['css']);

  // On récupère le contenu pour l'afficher, sinon on crée un fichier vide
  if (file_exists($filePath)) $filecontent = file_get_contents($filePath);
  else {
    fopen($filePath, 'w');
    $filecontent = '';
  }

?>
<!-- Script de l'éditeur de code -->
<script src="script/codeflask/codeflask.min.js"></script>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Page de modification des CSS du thème <strong><?php echo $theme->name; ?></strong></h1>
</div>

<?php echo $alert->info('Attention', 'Ces CSS seront effectif sur tout le site, prenez garde à ce que vous y mettez !'); ?>


<div class="card shadow mb-4">
  <div class="card-body">
    <div class="css_code"><?php echo $filecontent; ?></div>
  </div>
  <div class="card-footer text-muted">
    <button class="btn btn-success" onclick="saveCss();" type="button"><?php echo $msg->getTrad('_SAVE'); ?></button>
  </div>
</div>

<form id="formulaire" action="index.php?item=<?php echo $item; ?>&cmde=<?php echo $cmde; ?>" method="post">
  <input type="hidden" name="css" id="form_input_css" value="">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
</form>


<style>
  .css_code {
    position: relative;
    width: 100%;
    height: 200px;
  }
</style>

<script>
  // On initialise l'éditeur de code
  const flask = new CodeFlask('.css_code', {
    language: 'css',
    lineNumbers: true,
    handleTabs: true,
    defaultTheme: false
  });


  // Quand on clique sur enregistrer
  function saveCss() {
    var css = flask.getCode();
    $('#form_input_css').val(css);
    $('#formulaire').submit();
  }
</script>
