(function ($, Drupal, drupalSettings) {
  'use strict';

  function pemToArrayBuffer(pem) {
    var base64 = pem
      .replace(/-----BEGIN PUBLIC KEY-----/g, '')
      .replace(/-----END PUBLIC KEY-----/g, '')
      .replace(/\s/g, '');
    var binary = window.atob(base64);
    var bytes = new Uint8Array(binary.length);

    for (var i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }

    return bytes.buffer;
  }

  function arrayBufferToBase64(buffer) {
    var bytes = new Uint8Array(buffer);
    var binary = '';

    for (var i = 0; i < bytes.byteLength; i++) {
      binary += String.fromCharCode(bytes[i]);
    }

    return window.btoa(binary);
  }

  function importPublicKey(publicKeyPem) {
    return window.crypto.subtle.importKey(
      'spki',
      pemToArrayBuffer(publicKeyPem),
      {
        name: 'RSA-OAEP',
        hash: 'SHA-1'
      },
      false,
      ['encrypt']
    );
  }

  function encryptField(field, publicKey) {
    var value = field.value;
    if (!value || value.indexOf('rsa:') === 0) {
      return Promise.resolve();
    }

    var encoded = new window.TextEncoder().encode(value);
    return window.crypto.subtle.encrypt({ name: 'RSA-OAEP' }, publicKey, encoded)
      .then(function (encrypted) {
        field.value = 'rsa:' + arrayBufferToBase64(encrypted);
      });
  }

  function encryptForm(form, publicKey) {
    var fields = form.querySelectorAll('input[name="name"], input[type="password"]');
    var tasks = [];

    fields.forEach(function (field) {
      tasks.push(encryptField(field, publicKey));
    });

    return Promise.all(tasks);
  }

  Drupal.behaviors.password_encrypt = {
    attach: function (context, settings) {
      var publicKeyPem = drupalSettings.password_encrypt && drupalSettings.password_encrypt.publicKey;
      var selector = 'form.user-login, form.user-login-form, form.user-register-form, form.user-form';
      var forms = [];

      if (context.matches && context.matches(selector)) {
        forms.push(context);
      }
      if (context.querySelectorAll) {
        forms = forms.concat(Array.prototype.slice.call(context.querySelectorAll(selector)));
      }

      if (!publicKeyPem || !window.crypto || !window.crypto.subtle || !window.TextEncoder) {
        return;
      }

      importPublicKey(publicKeyPem).then(function (publicKey) {
        forms.forEach(function (form) {
          if (form.dataset.passwordEncryptAttached === '1') {
            return;
          }

          form.dataset.passwordEncryptAttached = '1';
          form.addEventListener('submit', function (event) {
            if (form.dataset.passwordEncryptSubmitting === '1') {
              return;
            }

            event.preventDefault();
            encryptForm(form, publicKey).then(function () {
              form.dataset.passwordEncryptSubmitting = '1';
              if (form.requestSubmit) {
                form.requestSubmit();
              }
              else {
                form.submit();
              }
            }).catch(function () {
              window.alert(Drupal.t('Unable to encrypt credentials. Please reload the page and try again.'));
            });
          });
        });
      }).catch(function () {
        window.alert(Drupal.t('Unable to initialize credential encryption. Please reload the page and try again.'));
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
