(function ($, Drupal) {
  Drupal.behaviors.disableClipboard = {
    attach: function (context, settings) {

      $('form#user-login-form input', context)
        .once('disableClipboard')
        .on('copy paste cut drag drop', function (e) {
          e.preventDefault();
          return false;
        });

    }
  };
})(jQuery, Drupal);
