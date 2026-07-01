(function () {
  'use strict';

  var importedPublicKeyPromise = null;

  function translate(message) {
    if (window.Drupal && window.Drupal.t) {
      return window.Drupal.t(message);
    }
    return message;
  }

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

  function getPublicKey() {
    var settings = window.drupalSettings || {};
    var publicKeyPem = settings.password_encrypt && settings.password_encrypt.publicKey;
    if (!publicKeyPem || !window.crypto || !window.crypto.subtle || !window.TextEncoder) {
      return null;
    }

    if (!importedPublicKeyPromise) {
      importedPublicKeyPromise = window.crypto.subtle.importKey(
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

    return importedPublicKeyPromise;
  }

  function shouldProtectForm(form) {
    if (!form || form.dataset.passwordEncryptSubmitting === '1') {
      return false;
    }

    var action = form.getAttribute('action') || '';
    var id = form.getAttribute('id') || '';
    var formId = form.querySelector('input[name="form_id"]');
    var hasCredentialFields = form.querySelector('input[name="name"], input[type="password"]');

    return !!hasCredentialFields && (
      id.indexOf('user-login-form') === 0 ||
      id.indexOf('user-register-form') === 0 ||
      action.indexOf('/user/login') !== -1 ||
      action.indexOf('/user/register') !== -1 ||
      action.indexOf('/user/') !== -1 ||
      (formId && ['user_login_form', 'user_register_form', 'user_form'].indexOf(formId.value) !== -1)
    );
  }

  function encryptField(field, publicKey) {
    var value = field.value;
    if (!value || value.indexOf('rsa:') === 0) {
      return Promise.resolve();
    }

    return window.crypto.subtle.encrypt(
      { name: 'RSA-OAEP' },
      publicKey,
      new window.TextEncoder().encode(value)
    ).then(function (encrypted) {
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

  function submitEncryptedForm(form) {
    form.dataset.passwordEncryptSubmitting = '1';
    if (form.requestSubmit) {
      form.requestSubmit();
    }
    else {
      form.submit();
    }
  }

  function attachSubmitProtection() {
    var publicKeyPromise = getPublicKey();
    if (!publicKeyPromise || window.passwordEncryptDocumentAttached) {
      return;
    }

    window.passwordEncryptDocumentAttached = true;
    document.addEventListener('submit', function (event) {
      var form = event.target;
      if (!shouldProtectForm(form)) {
        return;
      }

      event.preventDefault();
      publicKeyPromise.then(function (publicKey) {
        return encryptForm(form, publicKey);
      }).then(function () {
        submitEncryptedForm(form);
      }).catch(function () {
        window.alert(translate('Unable to encrypt credentials. Please reload the page and try again.'));
      });
    }, true);
  }

  if (window.Drupal) {
    window.Drupal.behaviors.password_encrypt = {
      attach: function () {
        attachSubmitProtection();
      }
    };
  }

  attachSubmitProtection();

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachSubmitProtection);
  }

})();
