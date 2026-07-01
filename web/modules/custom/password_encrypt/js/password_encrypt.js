(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.password_encrypt = {
    attach: function (context, settings) {
      // console.log("Password encrypted JS loaded.");

      var passkey = drupalSettings.password_encrypt.passkey;

      // -----------------------------
      // 1) LOGIN FORM ENCRYPTION
      // -----------------------------
      $('form.user-login, form.user-login-form', context).submit(function (event) {
      // console.log("Login form submit triggered.");
        // Encrypt Username
        var uname = $('#edit-name').val();
        if (uname !== '') {
          var encUser = CryptoJS.AES.encrypt(uname, passkey).toString();
          $('#edit-name').val(encUser);
        }

        // Encrypt Password
        var pass = $('#edit-pass').val();
        if (pass !== '') {
          var encPass = CryptoJS.AES.encrypt(pass, passkey).toString();
          $('#edit-pass').val(encPass);
        }

      });

      // -----------------------------
      // 2) REGISTER / USER FORM 
      // -----------------------------
      $('form.user-register-form, form.user-form', context).submit(function (event) {

        var current_pass = $('#edit-current-pass').val();
        var pass = $('#edit-pass-pass1').val();
        var cpass = $('#edit-pass-pass2').val();

        if (pass !== cpass) {
          $('span.error').append("<div>Password doesn't match. Please enter correct password.<div>");
          $('#edit-pass-pass2').addClass('error').focus();
          return false;
        }

        if (current_pass !== '') {
          var encCur = CryptoJS.AES.encrypt(current_pass, passkey).toString();
          $('#edit-current-pass').val(encCur);
        }

        if (pass !== '') {
          var encNew = CryptoJS.AES.encrypt(pass, passkey).toString();
          $('#edit-pass-pass1').val(encNew);
          $('#edit-pass-pass2').val(encNew);
        }

      });

    }
  };

})(jQuery, Drupal, drupalSettings);

