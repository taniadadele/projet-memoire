
<div class="form-group">
  <input type="text" name="ident" id="ident" class="form-control form-control-user" placeholder="<?php echo $msg->read($USER_USERID_OR_EMAIL); ?>">
  <div class="invalid-feedback" for="ident"></div> <!-- Pour les messages d'erreur -->
</div>






<input class="btn btn-primary btn-user btn-block" type="submit" name="valid" value="<?php echo $msg->read($USER_INPUTOK); ?>"></button>

<a href="index.php" style="margin-top: 20px;" class="btn btn-danger btn-user btn-block"><?php echo $msg->read($USER_BACK_BTN); ?></a>



<script>
  // jQuery Validator
  $(function() {
    // Initialize validation
    $("#forgotpwdform").validate({
      ignore: ".ignore, .select2-input",
      focusInvalid: false,
      rules: {
        "ident": {
          required: true,
        }
      },
      // Errors
      errorPlacement: function errorPlacement(error, element) {
        var name = $(element).attr('name');
        $('.invalid-feedback[for="' + name + '"]').html(error.html());
        checkImgMargin();
        return;
      },
      highlight: function(element) {
        $(element).addClass('is-invalid');
        checkImgMargin();
      },
      unhighlight: function(element) {
        $(element).removeClass('is-invalid');
        checkImgMargin();
      },
      messages: {
        ident: {
          email: "Une adresse email est requise",
          required: "Champ requis"
        }
      }
    });
  });
</script>
