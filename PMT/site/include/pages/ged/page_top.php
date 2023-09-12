<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $page_title; ?></h1>

  <?php if ($current_page == 'my_files') { ?>
    <div style="float: right; text-align: right;">
  		<form class="form-search" action="index.php?item=28&cmde=search" method="POST" id="searchForm">
  			<div class="input-group mb-3">
  				<input type="text" class="form-control search-query" id="appendedInputButton" name="searchField" autocomplete="off">
  				<div class="input-group-append">
  					<button class="btn btn-outline-secondary" type="submit"><?php echo $msg->read($USER_RESEARCH); ?></button>
  				</div>
  			</div>
  		</form>
    </div>
  <?php } ?>


  <?php if ($current_page == 'copies') { ?>

    <form action="index.php" id="formulaire" method="GET">
      <input type="hidden" name="item" value="<?php echo $_GET['item']; ?>">
      <input type="hidden" name="cmde" value="<?php echo $_GET['cmde']; ?>">

      <div style="float: right; margin-bottom: 20px;">
  			<div class="control-group" style="display: inline-block; margin-bottom: 3px;" id="start_date">
  				Depuis le&nbsp;
  			  <input MaxLength="10" class="format-d-m-y divider-slash" onchange="document.forms.formulaire.submit()" id="date" name="start_date" style="margin-bottom: 0px; width:90px; height: 18px; padding: 6px 4px 4px 6px; text-align: center" type="text" value="<?php echo $start_date; ?>" />
  			</div>


  			<?php if ($_SESSION['CnxAdm'] == 255) { ?>
  	      <select name="idUV" onchange="document.forms.formulaire.submit()" style="display: inline-block; margin-bottom: 0px;">
  	        <option value="">Tous les UV</option>
  	        <?php
  	          $query = "SELECT `_ID_exam`, `_nom` FROM `campus_examens` WHERE 1 ";
  	          $result = mysqli_query($query);
  	          while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
  	            if ($row[0] == $idUV) $selected = "selected";
  	            else $selected = "";
  	            echo "<option value=\"".$row[0]."\" ".$selected.">".$row[1]."</option>";
  	          }
  	        ?>
  	      </select>

  	      <div style="width: 250px !important; display: inline-block;">
  	        <?php echo showPMAList('IDmat', $IDmat) ?>
  	      </div>

  	      <select name="user_ID" onchange="document.forms.formulaire.submit()" style="display: inline-block; margin-bottom: 0px;">
  	        <option value="">Tous les utilisateurs</option>
  	        <?php
  	          $query = "SELECT DISTINCT `_ID` FROM `images` WHERE `_type` = 'copies_eleves' ";
  	          $result = mysqli_query($query);
  	          while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
  	            if ($row[0] == $user_ID) $selected = "selected";
  	            else $selected = "";
  	            echo "<option value=\"".$row[0]."\" ".$selected.">".getUserNameByID($row[0])."</option>";
  	          }
  	        ?>
  	      </select>
  			<?php } ?>
      </div>
    </form>


  <?php } ?>


</div>



<?php
  if (isset($page_breadcrumbs) && $page_breadcrumbs != '') echo $page_breadcrumbs;
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
		<div class="btn-group" style="z-index: 0;">
			<a href="index.php?item=28" class="btn btn-primary <?php if ($current_page == 'my_files') echo 'active'; ?>"><?php print($msg->read($USER_MY_FILES_LIGHT)); ?></a>
			<a href="index.php?item=28&cmde=shared" class="btn btn-primary <?php if ($current_page == 'shared_files') echo 'active'; ?>"><?php print($msg->read($USER_SHARED_FILES)); ?></a>
			<?php if (getParam('importCopies') == 1) { ?>
				<?php
					if ($_SESSION['CnxAdm'] == 255) $libele = $msg->read($USER_COPIES);
					else $libele = "Mes copies";
				?>
				<a href="index.php?item=28&cmde=myWork" class="btn btn-primary <?php if ($current_page == 'copies') echo 'active'; ?>"><?php echo $libele; ?></a>
			<?php } ?>
		</div>
  </div>
  <div class="card-body">
