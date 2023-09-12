<h5><?php echo $msg->read($CONFIG_DATA); ?></h5>

<div class="form-group">
  <label for="log"><?php echo $msg->read($CONFIG_LOGS).' ('.$msg->read($CONFIG_DAYS).')'; ?></label>
  <select class="form-control custom-select" id="log" name="log">
    <?php
      for ($i = 10; $i <= 60; $i += 10) {
        if ($i == $log) $selected = 'selected'; else $selected = '';
        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
      }
    ?>
  </select>
</div>

<div class="form-group">
  <label for="stats"><?php echo $msg->read($CONFIG_STATS).' ('.$msg->read($CONFIG_DAYS).')'; ?></label>
  <select class="form-control custom-select" id="stats" name="stats">
    <?php
      for ($i = 10; $i <= 60; $i += 10) {
        if ($i == $stats) $selected = 'selected'; else $selected = '';
        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
      }
    ?>
  </select>
</div>

<div class="form-group">
  <label for="postit"><?php echo $msg->read($CONFIG_POSTIT).' ('.$msg->read($CONFIG_WEEKS).')'; ?></label>
  <select class="form-control custom-select" id="postit" name="postit">
    <?php
      for ($i = 0; $i <= 10; $i++) {
        if ($i == $postit) $selected = 'selected'; else $selected = '';
        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
      }
    ?>
  </select>
</div>
