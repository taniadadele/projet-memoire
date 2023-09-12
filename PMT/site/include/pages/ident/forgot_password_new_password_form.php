<center>Veuillez entrer un nouveau mot-de-passe</center>
<?php if (!$timedout) { ?>
  <div class="form-group">
    <input type="password" name="newPass_1" id="newPass_1" class="form-control form-control-user" placeholder="<?php echo $msg->read($USER_PASSWORD); ?>">
    <div class="invalid-feedback" for="newPass_1"></div> <!-- Pour les messages d'erreur -->
  </div>

  <div class="form-group">
    <input type="password" name="newPass_2" id="newPass_2" class="form-control form-control-user" placeholder="<?php echo $msg->read($USER_PASSWORD); ?>">
    <div class="invalid-feedback" for="newPass_2"></div> <!-- Pour les messages d'erreur -->
  </div>

  <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
  <input type="submit" class="btn btn-primary btn-user btn-block" value="Enregistrer">
<?php } else { ?>
  <div class="alert alert-error" style="text-align: center;"><strong><?php echo $msg->read($USER_TIMEOUT_LINK); ?></strong></div>
<?php } ?>

<script>
    // jQuery Validator
    $(function() {
      // Initialize validation
      $("#forgotpwdform").validate({
        ignore: ".ignore, .select2-input",
        focusInvalid: false,
        rules: {
          "newPass_1": {
            required: true,
            minlength: 6,
            equalTo: "input[name=\"newPass_2\"]",
          },
          "newPass_2": {
            required: true,
            equalTo: "input[name=\"newPass_1\"]",
            minlength: 6
          }
        },
        // Errors
        errorPlacement: function errorPlacement(error, element) {
          var name = $(element).attr('name');
          $('.invalid-feedback[for="' + name + '"]').html(error.html());
          return;
        },
        highlight: function(element) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
          $(element).removeClass('is-invalid');
        },
        messages: {
          newPass_1: {
            required: "Champ requis",
            minlength: jQuery.validator.format("Au moins {0} caractères requis"),
            equalTo : "Les champs doivent correspondre"
          },
          newPass_2: {
            required: "Champ requis",
            minlength: jQuery.validator.format("Au moins {0} caractères requis"),
            equalTo : "Les champs doivent correspondre"
          }
        }
      });
    });
  </script>
