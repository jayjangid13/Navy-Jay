function setCookie(name,value,days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') { c = c.substring(1,c.length);
    }
    if (c.indexOf(nameEQ) == 0) { return c.substring(nameEQ.length,c.length);
    }
  }
  return null;
}
function eraseCookie(name) {
  document.cookie = name + '=; Max-Age=-99999999;';
}

jQuery(document).inactivity( {
  timeout: drupalSettings.inactive_autologout.timeout,
  mouse: true,
  keyboard: true,
  touch: true,
  customEvents: "",
  triggerAll: true,
});

jQuery(document).on("activity", function () {
  if (drupalSettings.inactive_autologout.enable == 1) {
    if(drupalSettings.inactive_autologout.execute) {
      var localtimestamp = getCookie('localtimestamp');
      if(localtimestamp == null) {
        setCookie('localtimestamp', jQuery.now(), 1);
      }
      // localtimestamp + 20 Seconds
      localtimestamp = parseInt(localtimestamp) + parseInt(20000);
      if(localtimestamp < jQuery.now()) {
        setCookie('localtimestamp', jQuery.now(), 1);
        jQuery.ajax({
          url: drupalSettings.baseUrl + '/autologout_active?localtimestamp=' + localtimestamp,
          cache: false,
          success: function (response) {
            setCookie('servertimestamp', response.timestamp, 1);
          }
        });
      }
    }
  }
});

jQuery(document).on("inactivity", function () {
  var servertimestamp = getCookie('servertimestamp');
  if(servertimestamp != null) {
    jQuery.ajax({
      url: drupalSettings.baseUrl + '/autologout_gettimestamp',
      cache: false,
      success: function (response) {
        setCookie('servertimestamp', response.timestamp, 1);
      }
    });
    var ts = parseInt(drupalSettings.inactive_autologout.timeout) + parseInt(getCookie('servertimestamp'));
    if(ts < jQuery.now()) {
      window.location.replace(drupalSettings.baseUrl + '/autologout' );
    }
  }
});
