/**
 * @file
 * Implements Custom JS for Logout Redirect Functionality.
 */

/**
 * @file
 * JavaScript to allow back button submit wizard page.
 */
(function ($) {

  'use strict';
  var status = false;
  var logout_redirect_url = drupalSettings.logout_redirect_url || "/user/login";
  var login_status = localStorage.getItem('logout_userStatus') === "true";

  setTimeout(function() {
    if ($('body').hasClass('user-logged-in')) {
      localStorage.setItem('logout_userStatus', true);
    } else {
      localStorage.setItem('logout_userStatus', false);
    }
  }, 100);

  if (login_status && (!$('body').hasClass('user-logged-in'))) {
    status = true;
  }

  if (window.history && window.history.pushState) {
    window.history.pushState('', null, '');
    window.addEventListener('popstate', function (event) {
      console.log("Popstate fired. Status: " + status);
      if (status) {
        console.log("Redirecting to: " + logout_redirect_url);
        window.location.href = logout_redirect_url;
      } else {
        if ((window.location.href.indexOf(logout_redirect_url) > -1)) {
          console.log("On login page, pushing state.");
          window.history.pushState('', null, '');
        } else {
          console.log("Going back in history.");
          history.back(1);
        }
      }
    });
  }

})(jQuery);

