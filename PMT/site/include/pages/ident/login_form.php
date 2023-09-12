<?php if (getParam('show_demo_idents_login')) { ?>
  <div>
    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Identifiants:</div>
    <div class="h5 mb-0 font-weight-bold text-gray-800">
      <ul>
        <li>Administrateur: admin - admin</li>
        <li>Étudiant: student - student</li>
      </ul>
    </div>
  </div>
<?php } ?>

<?php if (getParam('show_default_admin_login')) { ?>
  <div>
    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Identifiants:</div>
    <div class="h5 mb-0 font-weight-bold text-gray-800">
      <ul>
        <li>Administrateur: admin - admin</li>
      </ul>
    </div>
  </div>
<?php } ?>

<div class="form-group">
  <input type="text" name="id" id="id" class="form-control form-control-user" aria-describedby="emailHelp" placeholder="<?php echo $msg->read($USER_USERID); ?>">
  <div class="invalid-feedback" for="id"></div> <!-- Pour les messages d'erreur -->
</div>
<div class="form-group">
  <input type="password" name="pwd" id="pwd" class="form-control form-control-user" placeholder="<?php echo $msg->read($USER_PASSWORD); ?>">
  <div class="invalid-feedback" for="pwd"></div> <!-- Pour les messages d'erreur -->
</div>


<p class="hidden"><input type="hidden" name="submitAuth" value="Valider" /></p>

<input type="submit" class="btn btn-primary btn-user btn-block" value="Connexion">












  <script>
    // jQuery Validator
    $(function() {
      // Initialize validation
      $("#loginform").validate({
        ignore: ".ignore, .select2-input",
        focusInvalid: false,
        rules: {
          "id": {
            required: true,
            // email: true
          },
          "pwd": {
            required: true
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
          id: {
            email: "Une adresse email est requise",
            required: "Champ requis"
          },
          pwd: {
            required: "Champ requis"
          }
        }
      });
    });
  </script>
