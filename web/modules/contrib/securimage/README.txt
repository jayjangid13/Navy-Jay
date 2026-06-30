Securimage CAPTCHA provides a way for users of forms to verify they are human by
entering a series of characters read from an image or audio sample. Compared to
other CAPTCHA methods, Securimage:

- is completely open-source
- does not rely on a third-party server
- provides HTML5 and Flash-based audio versions of the challenge, for users with
  visual disabilities
- is highly configurable


Installation:

- Use composer to install the Securimage library, Version 4.0.1 or later, from
  https://www.phpcaptcha.org/, like so:
    composer require dapphp/securimage
- Install and activate the vendor_stream_wrapper module from
  https://drupal.org/project/vendor_stream_wrapper
- In order to use MP3 audio format, which works in more browsers than the
  Flash-based playback method, the LAME audio processor
  (https://sourceforge.net/projects/lame/) needs to be installed on your system.
  Most distributions include this as a package.
- Activate the Securimage module.


Configuration:

Use the main CAPTCHA configuration page, admin/config/people/captcha, to enable
Securimage for one or more forms. Use the Securimage CAPTCHA tab,
admin/config/people/captcha/securimage, to configure settings specific to
Securimage.
