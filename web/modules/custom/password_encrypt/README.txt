This module encrypts login and password form fields in browser-side
JavaScript before form submission.

The legacy AES passkey design has been removed because a symmetric AES key
sent to the browser is public. The module now uses RSA public/private key
encryption: the browser receives only the public key, while the private key
stays server-side and decrypts form values before Drupal validates them.
HTTPS/TLS must still be enforced. Drupal continues to store user passwords
as server-side hashes.

Dependencies:
----------------
1. OpenSSL PHP extension.
2. HTTPS/TLS must be enforced for login, registration, and password forms.

Installation:
----------------
1. Deploy the updated module code.
2. Run database updates so password_encrypt_update_9001() removes the legacy
   AES passkey from Drupal state and generates RSA keys.
3. Clear Drupal caches.
