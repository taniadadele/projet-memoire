<h5><?php echo $msg->read($CONFIG_SECURITY); ?></h5>

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="1" id="authusr" name="authusr" <?php if ($authusr) echo 'checked'; ?>>
  <label class="form-check-label" for="authusr">
    <?php echo $msg->read($CONFIG_CREATACCOUNT).' ('.$msg->read($CONFIG_BYUSER).')'; ?>
  </label>
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="1" id="debug" name="debug" <?php if ($debug) echo 'checked'; ?>>
  <label class="form-check-label" for="debug">
    <?php echo $msg->read($CONFIG_DEBUG); ?>
  </label>
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="1" id="demo" name="demo" <?php if ($demo) echo 'checked'; ?>>
  <label class="form-check-label" for="demo">
    <?php echo $msg->read($CONFIG_DEMO).' ('.$msg->read($CONFIG_MULTIPLE).')'; ?>
  </label>
</div>
